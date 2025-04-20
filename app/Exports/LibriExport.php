<?php

namespace App\Exports;

use App\Models\Libro;
use Maatwebsite\Excel\Concerns\FromCollection;

class LibriExport implements FromCollection
{
    public function collection()
    {
        return Libro::all();
    }
}
