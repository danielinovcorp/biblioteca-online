<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carrinhos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // para e-mail de carrinho abandonado (1h+)
            $table->timestamp('abandoned_notified_at')->nullable()->index();
            $table->timestamps();

            $table->unique('user_id'); // 1 carrinho ativo por utilizador
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrinhos');
    }
};
