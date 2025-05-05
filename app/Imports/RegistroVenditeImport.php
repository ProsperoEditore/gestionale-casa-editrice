<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\RegistroVenditeDettaglio;
use App\Models\Libro;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;


class RegistroVenditeImport implements ToModel, WithHeadingRow
{
    protected $registroVendita;

    public function __construct($registroVendita)
    {
        $this->registroVendita = $registroVendita;
    }

    public function model(array $row)
    {
        $isbn = $row['isbn'] ?? $row['ISBN'] ?? null;
        $quantita = $row['quantita'] ?? $row['Quantità'] ?? null;
        $periodo = $row['periodo'] ?? $row['Periodo'] ?? null;
        $data = $row['data'] ?? $row['Data'] ?? null;
    
        if (empty($isbn)) {
            Log::warning('Riga saltata: ISBN mancante o non leggibile.');
            return null;
        }
    
        $libro = Libro::where('isbn', trim($isbn))->first();
    
        if (!$libro) {
            Log::warning('ISBN non trovato: ' . $isbn);
            $libroTitolo = 'Titolo non trovato';
            $libroPrezzo = 0;
        } else {
            $libroTitolo = $libro->titolo;
            $libroPrezzo = $libro->prezzo;
        }
    
        return new RegistroVenditeDettaglio([
            'registro_vendita_id' => $this->registroVendita->id,
            'data' => $this->parseData($data),
            'periodo' => (string) $periodo,
            'isbn' => $isbn,
            'titolo' => $libroTitolo,
            'quantita' => $quantita,
            'prezzo' => $libroPrezzo,
            'valore_lordo' => $quantita * $libroPrezzo,
        ]);
    }

    private function parseData($data)
    {
        if (is_null($data)) {
            Log::warning('Data mancante, uso data odierna.');
            return now()->toDateString();
        }
    
        // Pulisce eventuali spazi o caratteri invisibili
        $data = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $data));
    
        // Se è un numero (formato seriale Excel)
        if (is_numeric($data)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data)->format('Y-m-d');
            } catch (\Throwable $e) {
                Log::error('Errore conversione data Excel (seriale): "' . $data . '" → ' . $e->getMessage());
                return now()->toDateString();
            }
        }
    
        // Se è una data testuale → usa Carbon
        try {
            return \Carbon\Carbon::parse($data)->format('Y-m-d');
        } catch (\Throwable $e) {
            Log::warning('Formato data non riconosciuto: "' . $data . '", uso data odierna.');
            return now()->toDateString();
        }
    }
    
    

    

}
