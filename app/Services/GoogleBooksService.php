<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    protected string $baseUrl = 'https://www.googleapis.com/books/v1/volumes';

    public function pesquisar(string $termo, int $startIndex = 0): array
    {
        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $termo,
            'startIndex' => $startIndex,
            'maxResults' => 10
        ]);

        if (!$response->ok()) return [];

        $items = $response->json()['items'] ?? [];

        $resultados = [];

        foreach ($items as $item) {
            $volumeInfo = $item['volumeInfo'];

            $resultados[] = [
                'isbn' => $volumeInfo['industryIdentifiers'][0]['identifier'] ?? 'Sem ISBN',
                'nome' => $volumeInfo['title'] ?? 'Sem título',
                'autores' => $volumeInfo['authors'] ?? ['Desconhecido'],
                'editora' => $volumeInfo['publisher'] ?? 'Desconhecida',
                'bibliografia' => $volumeInfo['description'] ?? '',
                'imagem_capa' => $volumeInfo['imageLinks']['thumbnail'] ?? '',
                'preco' => rand(5, 50), // valor fictício
            ];
        }

        return $resultados;
    }
}
