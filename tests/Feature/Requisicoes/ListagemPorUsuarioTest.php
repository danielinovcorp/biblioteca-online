<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lista apenas as requisicoes do usuario logado', function () {
	[$u1, $u2] = User::factory()->count(2)->create();
	$livro = Livro::factory()->create();

	Requisicao::factory()->count(2)->create(['user_id' => $u1->id, 'livro_id' => $livro->id]);
	Requisicao::factory()->count(3)->create(['user_id' => $u2->id, 'livro_id' => $livro->id]);

	$resp = $this->actingAs($u1)->get(route('requisicoes.index'))->assertOk();

	$resp->assertViewHas('requisicoes', fn ($requisicoes) =>
		$requisicoes->every(fn ($r) => $r->user_id === $u1->id)
	);
});
