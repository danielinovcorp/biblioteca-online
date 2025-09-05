<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite devolver um livro', function () {
	$user  = User::factory()->create();
	$livro = Livro::factory()->create();

	$req = Requisicao::factory()->create([
		'user_id'  => $user->id,
		'livro_id' => $livro->id,
		'status'   => 'ativa',
	]);

	$this->actingAs($user)
		->post(route('requisicoes.confirmar-devolucao', $req))
		->assertRedirect(route('requisicoes.index'));

	$this->assertDatabaseHas('requisicoes', [
		'id'     => $req->id,
		'status' => 'devolvida',
	]);
});
