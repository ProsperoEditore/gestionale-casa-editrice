<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Libro;
use App\Models\Anagrafica;
use App\Models\Contratto;
use App\Models\Magazzino;
use App\Models\Ordine;
use App\Models\RegistroVendite;
use App\Models\RegistroTirature;
use App\Models\Report;
use App\Models\Scarico;

class MultiSheetExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Libri' => new SingoloExport(Libro::class),
            'Anagrafiche' => new SingoloExport(Anagrafica::class),
            'Contratti' => new SingoloExport(Contratto::class),
            'Magazzini' => new SingoloExport(Magazzino::class),
            'Giacenze' => new SottotabellaExport('magazzino'),
            'Ordini' => new SingoloExport(Ordine::class),
            'Righe Ordini' => new SottotabellaExport('righe_ordine'),
            'Registro Vendite' => new SingoloExport(RegistroVendite::class),
            'Dettagli Vendite' => new SottotabellaExport('registro_vendite'),
            'Registro Tirature' => new SingoloExport(RegistroTirature::class),
            'Dettagli Tirature' => new SottotabellaExport('registro_tirature'),
            'Report' => new SingoloExport(Report::class),
            'Spedizioni' => new SingoloExport(Scarico::class),
        ];
    }
}
