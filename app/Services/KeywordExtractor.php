<?php

namespace App\Services;

use Illuminate\Support\Str;

class KeywordExtractor
{
    /** @var array<string,bool> */
    protected array $stop = [];

    public function __construct()
    {
        // Stopwords PT-PT/PT-BR (enxutas; podes ajustar)
        $words = [
            'a','à','às','ao','aos','aquela','aquele','aquilo','as','até','com','como','da','das','de','dela','dele','deles',
            'demais','depois','do','dos','e','é','ela','elas','ele','eles','em','entre','era','esse','esta','este','eu','foi',
            'há','isso','isto','já','la','lhe','lhes','lo','mais','mas','me','mesmo','meu','minha','muito','na','nas','não',
            'nem','no','nos','nós','o','os','ou','para','pela','pelas','pelo','pelos','por','qual','que','se','sem','ser',
            'seu','sua','são','só','também','te','tem','têm','ter','tua','tuas','um','uma','umas','uns','vai','você','vocês',
            'sobre','cada','onde','quando','quanto','quanta','todos','todas','dos','das'
        ];
        foreach ($words as $w) $this->stop[$this->normalize($w)] = true;
    }

    /** @return array<string,int> mapa keyword => tf */
    public function extractTF(string $text): array
    {
        $text = Str::of($text)->lower()->toString();
        // troca pontuação por espaço; mantém letras/números
        $text = preg_replace('/[^a-z0-9áàâãäéèêëíìîïóòôõöúùûüç\s-]/iu', ' ', $text) ?? '';
        $text = preg_replace('/[\s\-]+/u', ' ', $text) ?? '';
        $tokens = explode(' ', trim($text));

        $tf = [];
        foreach ($tokens as $raw) {
            $norm = $this->normalize($raw);
            if ($norm === '' || isset($this->stop[$norm]) || mb_strlen($norm) < 3) continue;
            $tf[$norm] = ($tf[$norm] ?? 0) + 1;
        }
        return $tf;
    }

    public function toWeighted(array $tf): array
    {
        if (!$tf) return [];
        $max = max($tf);
        $weighted = [];
        foreach ($tf as $k => $v) {
            // peso simples: 0.5 + 0.5*(tf/max)  (entre 0.5 e 1.0)
            $weighted[$k] = round(0.5 + 0.5 * ($v / $max), 4);
        }
        return $weighted;
    }

    protected function normalize(string $w): string
    {
        $w = Str::ascii(mb_strtolower($w));   // remove acentos
        return trim($w);
    }
}
