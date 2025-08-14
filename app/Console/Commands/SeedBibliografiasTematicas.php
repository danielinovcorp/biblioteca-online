<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Livro;
use Illuminate\Support\Str;

class SeedBibliografiasTematicas extends Command
{
	protected $signature = 'livros:seed-bibliografias 
							{--overwrite : sobrescreve mesmo se já houver bibliografia}
							{--themes= : lista separada por vírgula para substituir os temas padrão}';
	protected $description = 'Gera bibliografias com palavras-chave por tema para facilitar Livros Relacionados';

	public function handle(): int
	{
		$themesOpt = $this->option('themes');
		$themes = $themesOpt ? array_map('trim', explode(',', $themesOpt)) : [
			// temas padrão
			'programacao php laravel framework web api backend eloquent blade',
			'banco de dados sql performance index transacao normalizacao stored procedure',
			'javascript frontend react componentes estado hook navegador dom',
			'redes tcp ip http dns roteamento protocolo seguranca firewall',
			'historia medieval reino imperio batalhas cultura sociedade economia',
			'ciencia fisica quimica biologia pesquisa experimento laboratorio teoria',
			'fantasia magia reinos dragao aventura espada feitico heroi jornada',
			'romance amizade familia amor destino conflito emocao cotidiano'
		];

		$count = Livro::count();
		$this->info("Gerando bibliografias temáticas para {$count} livros…");

		$bar = $this->output->createProgressBar($count);
		Livro::orderBy('id')->chunk(200, function ($chunk) use ($themes, $bar) {
			foreach ($chunk as $livro) {
				if (!$this->option('overwrite') && filled($livro->bibliografia)) {
					$bar->advance();
					continue;
				}

				$idx = ($livro->id - 1) % count($themes);
				$kw = $themes[$idx];

				$titulo = trim($livro->nome ?? '');
				$frases = [
					"Este volume apresenta uma visão prática sobre {$kw}.",
					"Ao longo dos capítulos, o leitor encontra conceitos, casos e exercícios focados em {$kw}.",
					"Recomendado para iniciantes e também para quem já trabalha com {$kw}.",
				];
				$texto = ($titulo ? "{$titulo}. " : '') . implode(' ', $frases);

				$livro->bibliografia = Str::limit($texto, 900, '…');
				$livro->save();
				$bar->advance();
			}
		});

		$bar->finish();
		$this->newLine(2);
		$this->info('Bibliografias temáticas geradas com sucesso.');
		$this->warn('Agora rode: php artisan livros:reindex-keywords --flush');
		return Command::SUCCESS;
	}
}
