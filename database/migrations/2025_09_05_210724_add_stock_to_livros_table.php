<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('livros', function (Blueprint $table) {
			if (!Schema::hasColumn('livros', 'stock')) {
				$table->integer('stock')->default(5);
			}
		});
	}

	public function down(): void
	{
		Schema::table('livros', function (Blueprint $table) {
			if (Schema::hasColumn('livros', 'stock')) {
				$table->dropColumn('stock');
			}
		});
	}
};
