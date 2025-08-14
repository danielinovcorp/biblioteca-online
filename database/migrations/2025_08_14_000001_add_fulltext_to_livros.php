<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::statement('ALTER TABLE livros ADD FULLTEXT ft_nome_descricao (nome, descricao)');
    }
    public function down(): void {
        DB::statement('ALTER TABLE livros DROP INDEX ft_nome_descricao');
    }
};
