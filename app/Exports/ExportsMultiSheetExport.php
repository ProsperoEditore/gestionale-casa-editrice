<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Libri' => new SingoloExport(\App\Models\Libro::class),
            'Anagrafiche' => new SingoloExport(\App\Models\Anagrafica::class),
            'Contratti' => new SingoloExport(\App\Models\Contratto::class),
            'Magazzini' => new SingoloExport(\App\Models\Magazzino::class),
            'Ordini' => new SingoloExport(\App\Models\Ordine::class),
            'Scarichi' => new SingoloExport(\App\Models\Scarico::class),
            'Registro Tirature' => new SingoloExport(\App\Models\RegistroTirature::class),
            'Registro Vendite' => new SingoloExport(\App\Models\RegistroVendite::class),
            'Report' => new SingoloExport(\App\Models\Report::class),
        ];
    }
}
