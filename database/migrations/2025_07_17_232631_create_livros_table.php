<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('livros', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->unique();
            $table->string('nome');
            $table->unsignedBigInteger('editora_id');
            $table->text('bibliografia')->nullable();
            $table->string('imagem_capa')->nullable();
            $table->decimal('preco', 8, 2)->default(0);
            $table->timestamps();

            $table->foreign('editora_id')->references('id')->on('editoras')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
