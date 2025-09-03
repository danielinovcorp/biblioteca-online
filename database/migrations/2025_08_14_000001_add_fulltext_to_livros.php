<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Só aplica em MySQL/MariaDB
        if (DB::getDriverName() !== 'mysql') {
            return; // SQLite e outros: não faz nada
        }

        // Ajusta os nomes dos campos conforme o teu schema
        DB::statement('ALTER TABLE livros ADD FULLTEXT INDEX ft_nome_bibliografia (nome, bibliografia)');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE livros DROP INDEX ft_nome_bibliografia');
    }
};
