<?php

namespace App\Exports;

use App\Models\Livro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LivrosExport implements FromCollection, WithHeadings
{
    protected $ids;
    protected $editoraId;
    protected $autorId;

    public function __construct($ids = null, $editoraId = null, $autorId = null)
    {
        $this->ids = $ids ? explode(',', $ids) : null;
        $this->editoraId = $editoraId;
        $this->autorId = $autorId;
    }

    public function collection()
    {
        $query = Livro::with(['editora', 'autores']);

        if ($this->ids) {
            $query->whereIn('id', $this->ids);
        } else {
            if ($this->editoraId) {
                $query->where('editora_id', $this->editoraId);
            }

            if ($this->autorId) {
                $query->whereHas('autores', function ($q) {
                    $q->where('id', $this->autorId);
                });
            }
        }

        return $query->get()->map(function ($livro) {
            return [
                'ID' => $livro->id,
                'Título' => $livro->nome,
                'ISBN' => $livro->isbn,
                'Editora' => $livro->editora->nome,
                'Autores' => $livro->autores->pluck('nome')->join(', '),
                'Preço' => number_format($livro->preco, 2, ',', '.'),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Título', 'ISBN', 'Editora', 'Autores', 'Preço'];
    }
}

