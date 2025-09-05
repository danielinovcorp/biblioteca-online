<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RequisicaoFactory extends Factory
{
	protected $model = \App\Models\Requisicao::class;

	public function definition(): array
	{
		return [
			'user_id'      => \App\Models\User::factory(),
			'livro_id'     => \App\Models\Livro::factory(),
			'numero'       => 'REQ-'.str_pad($this->faker->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
			'status'       => 'ativa',
			'data_inicio'  => now()->toDateString(),
			'data_fim_prevista' => now()->addDays(5)->toDateString(),
			'data_fim_real'=> null,
			'foto_cidadao' => 'fotos_cidadao/fake.jpg',
		];
	}
}
