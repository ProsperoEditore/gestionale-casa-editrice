<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\RegistroVenditeDettaglio;
use App\Models\Libro;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


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
    // Se è un numero (Excel serial date), convertilo
    if (is_numeric($data)) {
        try {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data)->format('Y-m-d');
        } catch (\Throwable $e) {
            Log::error('Errore conversione data Excel: ' . $e->getMessage());
            return now()->toDateString();
        }
    }

    // Altrimenti prova con strtotime
    $timestamp = strtotime($data);
    return $timestamp ? date('Y-m-d', $timestamp) : now()->toDateString();
}

    

}
