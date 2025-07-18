<?php

namespace App\Exports;

use App\Models\Autor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AutoresExport implements FromCollection, WithHeadings
{
    protected $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        // Se nenhum ID foi passado, exporta todos
        if (empty($this->ids) || (count($this->ids) === 1 && $this->ids[0] === '')) {
            return Autor::all(['id', 'nome']);
        }

        return Autor::whereIn('id', $this->ids)->get(['id', 'nome']);
    }

    public function headings(): array
    {
        return ['ID', 'Nome'];
    }
}
