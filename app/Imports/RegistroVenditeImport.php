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
        // ✅ 1. Recupera i dati con fallback sicuro
        $dataRaw = trim($row['data'] ?? $row['Data'] ?? '');
        $isbn = trim($row['isbn'] ?? $row['ISBN'] ?? '');
        $quantita = $row['quantita'] ?? $row['Quantità'] ?? 0;
        $periodo = $row['periodo'] ?? $row['Periodo'] ?? 'N/D';
    
        // ✅ 2. Salta righe completamente vuote
        if (empty($isbn) && empty($quantita)) {
            return null;
        }
    
        // ✅ 3. Recupera dati libro
        $libro = Libro::where('isbn', $isbn)->first();
        $titolo = $libro->titolo ?? 'Titolo non trovato';
        $prezzo = $libro->prezzo ?? 0;
    
        // ✅ 4. Parsing sicuro della data
        $data = $this->parseData($dataRaw);
    
        // ✅ 5. Ritorna la riga del modello
        return new RegistroVenditeDettaglio([
            'registro_vendita_id' => $this->registroVendita->id,
            'data' => $data,
            'periodo' => (string) $periodo,
            'isbn' => $isbn,
            'titolo' => $titolo,
            'quantita' => $quantita,
            'prezzo' => $prezzo,
            'valore_lordo' => $quantita * $prezzo,
        ]);
    }
    
    


    private function parseData($data)
    {
        if (is_null($data) || trim($data) === '') {
            return null; // lasciare null è meglio che usare now()
        }
    
        try {
            if (is_numeric($data)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($data))->toDateString();
            } else {
                return Carbon::parse($data)->toDateString();
            }
        } catch (\Throwable $e) {
            return null;
        }
    }
    
    
}
