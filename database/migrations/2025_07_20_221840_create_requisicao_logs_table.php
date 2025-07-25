<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requisicao_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisicao_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->timestamp('data');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisicao_logs');
    }
};
