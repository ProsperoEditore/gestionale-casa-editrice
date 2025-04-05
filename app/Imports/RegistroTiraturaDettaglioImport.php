<?php

namespace App\Imports;

use App\Models\RegistroTiraturaDettaglio;
use App\Models\Libro;
use App\Models\RegistroTirature;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RegistroTiraturaDettaglioImport implements ToCollection, WithHeadingRow
{
    protected $registroTirature;

    public function __construct(RegistroTirature $registroTirature)
    {
        $this->registroTirature = $registroTirature;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $libro = Libro::where('isbn', $row['isbn'])->first();

            if (!$libro) {
                continue; // ISBN non trovato → salta riga
            }

            $copie = intval($row['copie_stampate']);
            $prezzo = floatval(str_replace(',', '.', $libro->prezzo));
            $imponibile_relativo = $copie * $prezzo * 0.3;
            $imponibile = $imponibile_relativo / 1.04;
            $iva_4percento = $imponibile_relativo - $imponibile;

            RegistroTiraturaDettaglio::create([
                'registro_tirature_id' => $this->registroTirature->id,
                'titolo_id' => $libro->id,
                'data' => $row['data'],
                'copie_stampate' => $copie,
                'prezzo_vendita_iva' => $prezzo,
                'imponibile_relativo' => $imponibile_relativo,
                'imponibile' => $imponibile,
                'iva_4percento' => $iva_4percento,
            ]);
        }
    }
}
