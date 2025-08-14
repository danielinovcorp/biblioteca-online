<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;


class Livro extends Model
{
	use HasFactory;

	protected $table = 'livros';

	protected $fillable = [
		'isbn',
		'nome',
		'editora_id',
		'bibliografia',
		'imagem_capa',
		'preco',
		'disponivel',
	];

	protected $appends = ['disponivel'];

	public function editora()
	{
		return $this->belongsTo(Editora::class);
	}

	public function autores()
	{
		return $this->belongsToMany(Autor::class);
	}

	public function requisicoes()
	{
		return $this->hasMany(Requisicao::class);
	}
	public function reviews()
	{
		return $this->hasMany(Review::class);
	}

	public function getDisponivelAttribute()
	{
		return !$this->requisicoes()
			->where('status', 'ativa')
			->exists();
	}

	public function alertas()
	{
		return $this->hasMany(\App\Models\AlertaDisponibilidade::class);
	}

	public function keywords()
	{
		return $this->hasMany(LivroKeyword::class);
	}

	public function related(int $limit = 8)
	{
		// 1) Pega até 15 keywords mais "fortes" do livro
		$kw = $this->keywords()
			->orderByDesc('weight')
			->limit(15)
			->pluck('keyword')
			->toArray();

		// Fallback 0: se o livro ainda não tem keywords indexadas,
		// mostra os mais recentes (diferentes dele) e sai.
		if (empty($kw)) {
			return static::where('id', '<>', $this->id)
				->latest('updated_at')
				->limit($limit)
				->get();
		}

		// 2) Procura livros que compartilham essas keywords e pontua por soma de pesos
		$rows = \DB::table('livro_keywords as lk')
			->selectRaw('lk.livro_id, SUM(lk.weight) as score, COUNT(*) as matches')
			->whereIn('lk.keyword', $kw)
			->where('lk.livro_id', '<>', $this->id)
			->groupBy('lk.livro_id')
			->orderByRaw('score DESC, matches DESC')
			->limit($limit)
			->get();

		// 3) Se não houver matches por keywords, tenta um fallback "semântico simples":
		//   - mesmo(s) autor(es)
		//   - mesma editora
		if ($rows->isEmpty()) {
			$query = static::query()->where('id', '<>', $this->id);

			// mesmo(s) autores
			$autorIds = $this->autores()->pluck('autors.id'); // tabela pivot padrão "autor_livro" → "autors"
			if ($autorIds->isNotEmpty()) {
				$query->orWhereHas('autores', function ($q) use ($autorIds) {
					$q->whereIn('autors.id', $autorIds);
				});
			}

			// mesma editora
			if ($this->editora_id) {
				$query->orWhere('editora_id', $this->editora_id);
			}

			// Se mesmo assim não houver, volta os mais recentes (robusto)
			$fallback = $query->with('editora')
				->latest('updated_at')
				->limit($limit)
				->get();

			if ($fallback->isNotEmpty()) return $fallback;

			return static::where('id', '<>', $this->id)
				->with('editora')
				->latest('updated_at')
				->limit($limit)
				->get();
		}

		// 4) Constrói a ordem pelos IDs encontrados preservando ranking
		$ids = $rows->pluck('livro_id')->map(fn($v) => (int) $v)->all();

		// Em MySQL/MariaDB o FIELD funciona; se estiveres em SQLite, usa CASE WHEN:
		if (\DB::getDriverName() === 'sqlite') {
			$cases = collect($ids)->map(fn($id, $i) => "WHEN id = {$id} THEN {$i}")->implode(' ');
			$orderRaw = "CASE {$cases} END";
		} else {
			$orderRaw = 'FIELD(id,' . implode(',', $ids) . ')';
		}

		// 5) Retorna SEMPRE uma Eloquent\Collection (mesmo se vazia)
		return static::whereIn('id', $ids)
			->with('editora')
			->orderByRaw($orderRaw)
			->get();
	}
}
