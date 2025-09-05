<?php

use App\Models\User;
use App\Models\Livro;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('impede requisitar livro sem stock disponivel', function () {
	Storage::fake('public');
	Mail::fake();

	$user  = User::factory()->create();
	$livro = Livro::factory()->create(['stock' => 0]);

	$this->actingAs($user)
		->post(route('requisicoes.store'), [
			'livro_id'     => $livro->id,
			'foto_cidadao' => UploadedFile::fake()->image('cid.jpg'),
		])
		->assertSessionHasErrors(['livro_id']);
});
