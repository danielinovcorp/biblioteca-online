<?php
/**
 * Pest bootstrap para o projeto BibliotecaOnline.
 * - Faz todos os testes em tests/Feature e tests/Unit herdarem de Tests\TestCase.
 * - Mantém espaço para expectations e helpers globais (opcionais).
 */

use Tests\TestCase;

// Todos os testes em Feature/ e Unit/ usam a TestCase do Laravel.
uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations (opcional)
|--------------------------------------------------------------------------
| Exemplos de expectativas customizadas para usar com `expect($valor)->...`.
*/
expect()->extend('toBeOne', fn () => $this->toBe(1));

/*
|--------------------------------------------------------------------------
| Helpers globais (opcional)
|--------------------------------------------------------------------------
| Funções auxiliares disponíveis em todos os testes.
| Mantidas mínimas para não “poluir” a suite.
*/

/** Cria um upload de imagem falso (útil para o campo 'foto_cidadao'). */
function fakeUploadImage(string $name = 'cid.jpg')
{
    return \Illuminate\Http\UploadedFile::fake()->image($name);
}
