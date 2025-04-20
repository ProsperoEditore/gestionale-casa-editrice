<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SottotabellaExport implements FromCollection, WithHeadings
{
    protected $tipo;

    public function __construct(string $tipo)
    {
        $this->tipo = $tipo;
    }

    public function collection()
    {
        return match ($this->tipo) {
            'righe_ordine' => DB::table('riga_ordine')->get(),
            'registro_vendite' => DB::table('registro_vendite_dettaglio')->get(),
            'registro_tirature' => DB::table('registro_tirature_dettaglio')->get(),
            'magazzino' => DB::table('giacenze')->get(),
            default => collect([]),
        };
    }

    public function headings(): array
    {
        $prima = $this->collection()->first();
        return $prima ? array_keys((array) $prima) : [];
    }
}
