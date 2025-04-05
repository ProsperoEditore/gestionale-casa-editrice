<?php

namespace App\Exports;

use App\Models\Giacenza;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GiacenzeExport implements FromCollection, WithHeadings
{
    protected $magazzino_id;

    public function __construct($magazzino_id)
    {
        $this->magazzino_id = $magazzino_id;
    }

    public function collection()
    {
        return Giacenza::where('magazzino_id', $this->magazzino_id)
            ->select('isbn', 'titolo', 'quantita', 'prezzo', 'sconto', 'costo_produzione', 'data_ultimo_aggiornamento', 'note')
            ->get();
    }

    public function headings(): array
    {
        return ['ISBN', 'Titolo', 'Quantità', 'Prezzo', 'Sconto', 'Costo Produzione', 'Data Ultimo Aggiornamento', 'Note'];
    }
}
