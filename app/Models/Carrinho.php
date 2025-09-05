<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use App\Models\Concerns\LogsModelEvents;


class Carrinho extends Model
{
	use LogsModelEvents;

	protected static string $MODULE_NAME = 'Carrinhos';

	protected $table = 'carrinhos';

	protected $fillable = [
		'user_id',
		'status',
		'abandoned_notified_at',
	];

	protected $casts = [
		'abandoned_notified_at' => 'datetime',
	];

	/** Relations */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	// muitos-para-muitos com pivot (quantidade, timestamps)
	public function livros(): BelongsToMany
	{
		return $this->belongsToMany(Livro::class, 'carrinho_livro')
			->withPivot(['quantidade'])
			->withTimestamps();
	}

	/** Static helpers */
	public static function forUser(int $userId): self
	{
		$c = static::where('user_id', $userId)->orderByDesc('updated_at')->first();
		return $c ?? static::create(['user_id' => $userId]);
	}


	/** Domain helpers */
	public function addLivro(Livro $livro, int $quantidade = 1): void
	{
		$existing = $this->livros()->where('livro_id', $livro->id)->first();

		if ($existing) {
			$novaQtd = $existing->pivot->quantidade + $quantidade;
			$this->livros()->updateExistingPivot($livro->id, ['quantidade' => $novaQtd]);
		} else {
			$this->livros()->attach($livro->id, ['quantidade' => max(1, $quantidade)]);
		}

		$this->touch(); // atualiza updated_at (ajuda no abandono 1h)
	}

	public function setQuantidade(Livro $livro, int $quantidade): void
	{
		$quantidade = max(0, $quantidade);

		if ($quantidade === 0) {
			$this->livros()->detach($livro->id);
		} else {
			$this->livros()->syncWithoutDetaching([
				$livro->id => ['quantidade' => $quantidade],
			]);
		}

		$this->touch();
	}

	public function removeLivro(Livro $livro): void
	{
		$this->livros()->detach($livro->id);
		$this->touch();
	}

	public function limpar(): void
	{
		$this->livros()->detach();
		$this->touch();
	}

	public function total(): float
	{
		// assume coluna 'preco' em livros
		return (float) $this->livros->sum(function (Livro $l) {
			$q = (int) $l->pivot->quantidade;
			$p = (float) ($l->preco ?? 0);
			return $q * $p;
		});
	}

	/**
	 * Cria uma Encomenda a partir do carrinho (snapshot de preços e títulos),
	 * limpa o carrinho e retorna a Encomenda criada.
	 *
	 * @param array $dadosEntrega ['morada' => ..., 'cidade' => ..., 'codigo_postal' => ..., 'telefone' => ...]
	 */
	// App/Models/Carrinho.php
	public function criarEncomenda(array $dadosEntrega, bool $limparCarrinho = false): Encomenda
	{
		if ($this->livros()->count() === 0) {
			throw new \RuntimeException('Carrinho vazio.');
		}

		$items = $this->livros()->get()->map(function (Livro $l) {
			return [
				'livro_id'       => $l->id,
				'quantidade'     => (int) $l->pivot->quantidade,
				'preco_unitario' => (float) ($l->preco ?? 0),
				'titulo_livro'   => (string) ($l->nome ?? $l->titulo ?? 'Livro'),
			];
		});

		$total = array_reduce($items->all(), fn($acc, $it) => $acc + ($it['quantidade'] * $it['preco_unitario']), 0.0);

		return DB::transaction(function () use ($dadosEntrega, $items, $total, $limparCarrinho) {
			/** @var Encomenda $encomenda */
			$encomenda = Encomenda::create([
				'user_id'             => $this->user_id,
				'morada'              => $dadosEntrega['morada'] ?? '',
				'cidade'              => $dadosEntrega['cidade'] ?? null,
				'codigo_postal'       => $dadosEntrega['codigo_postal'] ?? null,
				'telefone'            => $dadosEntrega['telefone'] ?? null,
				'estado'              => 'pendente',
				'total'               => $total,
				'stripe_session_id'   => null,
				'stripe_payment_intent' => null,
			]);

			foreach ($items as $it) {
				$encomenda->livros()->attach($it['livro_id'], [
					'quantidade'     => $it['quantidade'],
					'preco_unitario' => $it['preco_unitario'],
					'titulo_livro'   => $it['titulo_livro'],
				]);
			}

			if ($limparCarrinho) {
				$this->livros()->detach();
				$this->touch();
			}

			return $encomenda;
		});
	}
}
