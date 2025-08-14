<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('livro_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livro_id')->constrained('livros')->cascadeOnDelete();
            $table->string('keyword', 80);               // normalizada (sem acentos)
            $table->unsignedInteger('tf')->default(1);   // term frequency no livro
            $table->decimal('weight', 8, 4)->default(1); // peso (ex.: TF normalizado)
            $table->timestamps();

            $table->unique(['livro_id', 'keyword']);
            $table->index('keyword');
            $table->index('livro_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('livro_keywords');
    }
};

