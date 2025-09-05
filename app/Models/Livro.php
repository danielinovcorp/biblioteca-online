<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\LogsModelEvents;


class Livro extends Model
{
	use HasFactory, LogsModelEvents;

	protected static string $MODULE_NAME = 'Livros';

	protected $table = 'livros';

	protected $fillable = [
		'isbn',
		'nome',
		'editora_id',
		'bibliografia',
		'imagem_capa',
		'preco',
		'stock',
	];

	// Anexamos os atributos virtuais ao JSON/array
	protected $appends = ['disponivel', 'disponivel_label'];

	// Relações
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

	public function alertas()
	{
		return $this->hasMany(\App\Models\AlertaDisponibilidade::class);
	}

	public function keywords()
	{
		return $this->hasMany(LivroKeyword::class);
	}

	/**
	 * ATRIBUTO VIRTUAL: disponivel (bool)
	 * Verdadeiro quando NÃO há requisição ativa (sem devolução) para este livro.
	 */
	public function getDisponivelAttribute(): bool
	{
		// Se a relação já foi carregada (load/eager load), usamos a coleção em memória
		if ($this->relationLoaded('requisicoes')) {
			$emAberto = $this->requisicoes->contains(function ($r) {
				$status = strtolower((string) ($r->status ?? '')); // lida com null/strings
				return is_null($r->data_devolucao)
					&& !in_array($status, ['devolvida', 'cancelada'], true);
			});
			return !$emAberto;
		}

		// Caso contrário, consultamos o BD
		$emAberto = $this->requisicoes()
			->whereNull('data_devolucao')
			->whereNotIn('status', ['devolvida', 'cancelada']) // seguro mesmo se status existir
			->exists();

		return !$emAberto;
	}

	/**
	 * ATRIBUTO VIRTUAL: disponivel_label ("Sim"/"Não")
	 * Útil para badges na UI sem quebrar a lógica booleana.
	 */
	public function getDisponivelLabelAttribute(): string
	{
		return $this->disponivel ? 'Sim' : 'Não';
	}

	/**
	 * Livros relacionados por keywords (com fallbacks).
	 */
	public function related(int $limit = 8)
	{
		// 1) Pega até 15 keywords mais "fortes" do livro
		$kw = $this->keywords()
			->orderByDesc('weight')
			->limit(15)
			->pluck('keyword')
			->toArray();

		// Fallback 0: se o livro ainda não tem keywords, mostra os mais recentes (diferentes dele)
		if (empty($kw)) {
			return static::where('id', '<>', $this->id)
				->with('editora')
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

		// 3) Fallback semântico simples (mesmo(s) autor(es) e/ou mesma editora)
		if ($rows->isEmpty()) {
			$query = static::query()->where('id', '<>', $this->id);

			// mesmos autores
			$autorIds = $this->autores()->pluck('autors.id');
			if ($autorIds->isNotEmpty()) {
				$query->orWhereHas('autores', function ($q) use ($autorIds) {
					$q->whereIn('autors.id', $autorIds);
				});
			}

			// mesma editora
			if ($this->editora_id) {
				$query->orWhere('editora_id', $this->editora_id);
			}

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

		if (\DB::getDriverName() === 'sqlite') {
			$cases = collect($ids)->map(fn($id, $i) => "WHEN id = {$id} THEN {$i}")->implode(' ');
			$orderRaw = "CASE {$cases} END";
		} else {
			$orderRaw = 'FIELD(id,' . implode(',', $ids) . ')';
		}

		// 5) Retorna uma Eloquent\Collection ordenada
		return static::whereIn('id', $ids)
			->with('editora')
			->orderByRaw($orderRaw)
			->get();
	}

	public function carrinhos()
	{
		return $this->belongsToMany(\App\Models\Carrinho::class, 'carrinho_livro')
			->withPivot(['quantidade'])
			->withTimestamps();
	}

	public function encomendas()
	{
		return $this->belongsToMany(\App\Models\Encomenda::class, 'encomenda_livro')
			->withPivot(['quantidade', 'preco_unitario', 'titulo_livro'])
			->withTimestamps();
	}
}
