<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // endereço de entrega
            $table->string('morada');
            $table->string('cidade')->nullable();
            $table->string('codigo_postal', 20)->nullable();
            $table->string('telefone', 30)->nullable();

            // pagamento
            $table->string('estado', 20)->default('pendente')->index(); // pendente | paga
            $table->decimal('total', 10, 2)->default(0);
            $table->string('stripe_session_id')->nullable()->index();
            $table->string('stripe_payment_intent')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encomendas');
    }
};
