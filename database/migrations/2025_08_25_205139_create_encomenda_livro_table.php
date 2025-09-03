<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('encomenda_livro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encomenda_id')->constrained('encomendas')->cascadeOnDelete();
            $table->foreignId('livro_id')->constrained('livros')->cascadeOnDelete();

            $table->unsignedInteger('quantidade')->default(1);
            $table->decimal('preco_unitario', 10, 2)->default(0); // snapshot
            $table->string('titulo_livro')->nullable();           // snapshot opcional

            $table->timestamps();

            $table->index('encomenda_id');
            $table->index('livro_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encomenda_livro');
    }
};
