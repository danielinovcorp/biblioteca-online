<?php

use App\Models\User;
use App\Models\Livro;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite criar uma requisicao de livro', function () {
	Storage::fake('public');
	Mail::fake();

	$user  = User::factory()->create();
	$livro = Livro::factory()->create();

	$this->actingAs($user)
		->post(route('requisicoes.store'), [
			'livro_id'     => $livro->id,
			'foto_cidadao' => UploadedFile::fake()->image('cid.jpg'),
			'observacoes'  => 'Quero por 5 dias',
		])
		->assertRedirect(route('requisicoes.index'));

	$this->assertDatabaseHas('requisicoes', [
		'user_id'  => $user->id,
		'livro_id' => $livro->id,
		'status'   => 'ativa',
	]);
});
