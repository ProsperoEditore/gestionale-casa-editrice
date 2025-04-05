<?php

namespace App\Imports;

use App\Models\Giacenza;
use App\Models\Libro;
use Maatwebsite\Excel\Concerns\ToModel;

class GiacenzeImport implements ToModel
{
    protected $magazzino_id;

    public function __construct($magazzino_id)
    {
        $this->magazzino_id = $magazzino_id;
    }

    public function model(array $row)
    {
        // Validazione: almeno ISBN e Quantità devono esserci
        if (!isset($row[0]) || !isset($row[1])) {
            return null;
        }

        $isbn = trim($row[0]);
        $quantita = (int) $row[1];

        // Pulizia costo produzione: converti virgola in punto
        $costoProduzioneRaw = $row[2] ?? null;
        $costoProduzione = $costoProduzioneRaw ? str_replace(',', '.', $costoProduzioneRaw) : null;
        $costoProduzione = is_numeric($costoProduzione) ? (float) $costoProduzione : null;

        $libro = Libro::where('isbn', $isbn)->first();

        if ($libro) {
            return new Giacenza([
                'magazzino_id' => $this->magazzino_id,
                'libro_id' => $libro->id,
                'isbn' => $libro->isbn,
                'titolo' => $libro->titolo,
                'quantita' => $quantita,
                'prezzo' => $libro->prezzo,
                'costo_produzione' => $costoProduzione,
                'data_ultimo_aggiornamento' => now(),
            ]);
        }

        return null;
    }
}
