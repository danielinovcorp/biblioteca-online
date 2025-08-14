<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('livro_id')->constrained('livros')->cascadeOnDelete();
            $table->foreignId('requisicao_id')->constrained('requisicoes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');           // 1..5
            $table->text('comentario')->nullable();

            $table->string('status', 10)->default('suspenso'); // suspenso|ativo|recusado
            $table->text('justificativa')->nullable();         // se recusado

            $table->timestamps();

            $table->unique(['requisicao_id','user_id']);
            $table->index(['livro_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
