<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('nao cria requisicao sem livro valido', function () {
	Storage::fake('public');
	Mail::fake();

	$user = User::factory()->create();

	$this->actingAs($user)
		->post(route('requisicoes.store'), [
			'livro_id' => 999999, // inexistente
			// omitimos 'foto_cidadao' para disparar validação
		])
		->assertSessionHasErrors(['livro_id', 'foto_cidadao']);
});
