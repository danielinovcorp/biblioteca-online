<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Livro;
use App\Models\LivroKeyword;
use App\Services\KeywordExtractor;
use Illuminate\Support\Facades\DB;

class ReindexLivroKeywords extends Command
{
	protected $signature = 'livros:reindex-keywords {--flush : Limpa keywords antes de reindexar}';
	protected $description = 'Extrai e indexa palavras-chave dos livros';

	public function handle(KeywordExtractor $extractor): int
	{
		if ($this->option('flush')) {
			LivroKeyword::query()->delete();
			$this->info('Tabela livro_keywords limpa.');
		}

		$bar = $this->output->createProgressBar(Livro::count());
		Livro::chunk(200, function ($chunk) use ($extractor, $bar) {
			foreach ($chunk as $livro) {
				DB::transaction(function () use ($livro, $extractor) {
					$text = trim(($livro->bibliografia ?? '') . ' ' . ($livro->nome ?? ''));
					$tf = $extractor->extractTF($text);
					$weights = $extractor->toWeighted($tf);

					// apaga antigas do livro
					$livro->keywords()->delete();

					// mantém top 25 palavras por peso
					arsort($weights);
					$top = array_slice($weights, 0, 25, true);

					foreach ($top as $kw => $w) {
						$livro->keywords()->create([
							'keyword' => $kw,
							'tf'      => $tf[$kw] ?? 1,
							'weight'  => $w,
						]);
					}
				});
				$bar->advance();
			}
		});
		$bar->finish();
		$this->newLine(2);
		$this->info('Reindex concluído!');
		return Command::SUCCESS;
	}
}
