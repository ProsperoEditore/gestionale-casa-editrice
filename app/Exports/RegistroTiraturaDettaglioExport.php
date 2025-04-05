<?php

namespace App\Exports;

use App\Models\RegistroTiraturaDettaglio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RegistroTiraturaDettaglioExport implements FromCollection, WithHeadings
{
    protected $registroTiratureId;

    public function __construct($registroTiratureId)
    {
        $this->registroTiratureId = $registroTiratureId;
    }

    public function collection()
    {
        return RegistroTiraturaDettaglio::where('registro_tirature_id', $this->registroTiratureId)
            ->with('titolo')
            ->get()
            ->map(function ($item) {
                return [
                    'Data' => $item->data,
                    'Titolo' => $item->titolo->titolo ?? '',
                    'Copie Stampate' => $item->copie_stampate,
                    'Prezzo IVA' => $item->prezzo_vendita_iva,
                    'Imponibile Relativo' => $item->imponibile_relativo,
                    'Imponibile' => $item->imponibile,
                    'IVA 4%' => $item->iva_4percento,
                ];
            });
    }

    public function headings(): array
    {
        return ['Data', 'Titolo', 'Copie Stampate', 'Prezzo IVA', 'Imponibile Relativo', 'Imponibile', 'IVA 4%'];
    }
}
