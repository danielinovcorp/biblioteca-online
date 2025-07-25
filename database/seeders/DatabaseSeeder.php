<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $editoras = Editora::factory(5)->create();
        $autores = Autor::factory(10)->create();

        Livro::factory(15)->create()->each(function ($livro) use ($autores) {
            $livro->autores()->attach(
                $autores->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        // User::factory(10)->create();
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::create([
            'name' => 'Daniel Admin',
            'email' => 'admin@teste.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
