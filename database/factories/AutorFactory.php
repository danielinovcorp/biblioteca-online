<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AutorFactory extends Factory
{
    public function definition(): array
    {
        $sexo = fake()->randomElement(['men', 'women']);
        $numero = fake()->numberBetween(1, 99);
        
        return [
            'nome' => fake()->name($sexo === 'men' ? 'male' : 'female'),
            'foto' => "https://randomuser.me/api/portraits/{$sexo}/{$numero}.jpg",
        ];
    }
}

