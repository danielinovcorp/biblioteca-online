<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EditoraFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => $this->faker->company(),
            'logotipo' => fn () => 'https://picsum.photos/seed/editora' . rand(1, 1000) . '/100/100',
        ];
    }
}

