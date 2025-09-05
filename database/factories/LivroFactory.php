<?php

namespace Database\Factories;

use App\Models\Livro;
use App\Models\Editora;
use Illuminate\Database\Eloquent\Factories\Factory;

class LivroFactory extends Factory
{
	protected $model = Livro::class;

	public function definition(): array
	{
		return [
			'isbn'         => $this->faker->isbn13(),
			'nome'         => $this->faker->sentence(3),
			// pega um id existente OU cria uma nova editora via factory (sem dar erro quando não houver)
			'editora_id'   => Editora::query()->inRandomOrder()->value('id') ?? Editora::factory(),
			'bibliografia' => $this->faker->paragraph(),
			'imagem_capa'  => "https://picsum.photos/seed/livro{$this->faker->unique()->numberBetween(1, 100000)}/120/180",
			'preco'        => $this->faker->randomFloat(2, 5, 100),
			'stock'        => 5,
		];
	}
}
