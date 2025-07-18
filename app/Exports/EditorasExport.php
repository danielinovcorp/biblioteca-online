<?php

namespace App\Exports;

use App\Models\Editora;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EditorasExport implements FromCollection, WithHeadings
{
    protected $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        return Editora::whereIn('id', $this->ids)->get(['id', 'nome']);
    }

    public function headings(): array
    {
        return ['ID', 'Nome'];
    }
}
