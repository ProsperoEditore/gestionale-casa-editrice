<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\RegistroVenditeDettaglio;
use App\Models\Libro;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;


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
    
        try {
            if (is_numeric($data)) {
                // Converte seriale Excel in oggetto Carbon
                return Carbon::instance(ExcelDate::excelToDateTimeObject($data))->toDateString();
            } else {
                return Carbon::parse($data)->toDateString(); // qualsiasi stringa interpretabile
            }
        } catch (\Throwable $e) {
            Log::warning('Errore parsing data "' . $data . '": ' . $e->getMessage());
            return now()->toDateString();
        }
    }
    
     

}
