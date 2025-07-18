<?php

namespace Database\Factories;

use App\Models\Editora;
use Illuminate\Database\Eloquent\Factories\Factory;

class LivroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'isbn' => $this->faker->isbn13(),
            'nome' => $this->faker->sentence(3),
            'editora_id' => Editora::inRandomOrder()->first()->id ?? Editora::factory(),
            'bibliografia' => $this->faker->paragraph(),
            'imagem_capa' => fn () => 'https://picsum.photos/seed/livro' . rand(1, 1000) . '/120/180',
            'preco' => $this->faker->randomFloat(2, 5, 100),
        ];
    }
}

