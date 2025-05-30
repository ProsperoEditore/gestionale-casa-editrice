<?php

namespace App\Exports;

use App\Models\Giacenza;
use App\Models\Libro;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GiacenzeExport implements FromArray, WithHeadings, WithTitle
{
    protected $magazzino_id;
    protected $giacenze;

    public function __construct($magazzino_id)
    {
        $this->magazzino_id = $magazzino_id;
        $this->giacenze = Giacenza::with('libro.marchio_editoriale')
            ->where('magazzino_id', $this->magazzino_id)
            ->get();
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->giacenze as $g) {
            $rows[] = [
                $g->isbn,
                $g->titolo,
                $g->quantita,
                $g->prezzo,
                $g->sconto,
                $g->costo_produzione,
                $g->data_ultimo_aggiornamento ? $g->data_ultimo_aggiornamento->format('Y-m-d') : '',
                $g->note,
            ];
        }

        // Riga vuota per separazione
        $rows[] = [];

        // Totali
        $rows[] = ['RIEPILOGO'];
        $rows[] = ['Marchi presenti:', $this->giacenze->pluck('libro.marchio_editoriale.nome')->filter()->unique()->count()];
        $rows[] = ['Totale titoli a magazzino:', $this->giacenze->count()];
        $rows[] = ['Quantità complessiva:', $this->giacenze->sum('quantita')];
        $rows[] = ['Valore lordo complessivo (€):', $this->giacenze->sum(fn($g) => $g->prezzo * $g->quantita)];
        $rows[] = ['Totale prezzo di costo (€):', $this->giacenze->sum(fn($g) => $g->costo_produzione * $g->quantita)];

        return $rows;
    }

    public function headings(): array
    {
        return ['ISBN', 'Titolo', 'Quantità', 'Prezzo', 'Sconto', 'Costo Produzione', 'Data Ultimo Aggiornamento', 'Note'];
    }

    public function title(): string
    {
        return 'Giacenze';
    }
}
