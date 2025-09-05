<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void {
		Schema::create('logs', function (Blueprint $table) {
			$table->id();
			$table->timestamp('data_hora');
			$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
			$table->string('modulo');                 // Requisicoes, Livros, Encomendas, etc.
			$table->nullableMorphs('loggable');       // loggable_type + loggable_id
			$table->string('alteracao');              // created|updated|deleted|status_changed|request...
			$table->text('detalhes')->nullable();     // JSON ou texto
			$table->string('ip')->nullable();
			$table->text('browser')->nullable();
			$table->timestamps();
		});
	}
	public function down(): void {
		Schema::dropIfExists('logs');
	}
};
