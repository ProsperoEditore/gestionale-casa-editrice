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
    
        $dataRaw = $row['data'] ?? $row['Data'] ?? null; 
        $isbn = $row['isbn'] ?? $row['ISBN'] ?? null;
        $quantita = $row['quantita'] ?? $row['Quantità'] ?? null;
        $periodo = $row['periodo'] ?? $row['Periodo'] ?? null;
    
        if (empty($isbn)) {
            Log::warning('Riga saltata: ISBN mancante o non leggibile.');
            return null;
        }
    
        $libro = Libro::where('isbn', trim($isbn))->first();
    
        $libroTitolo = $libro->titolo ?? 'Titolo non trovato';
        $libroPrezzo = $libro->prezzo ?? 0;
    
        return new RegistroVenditeDettaglio([
            'registro_vendita_id' => $this->registroVendita->id,
            'data' => $this->parseData($dataRaw),
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
        Log::info('🔍 Valore ricevuto per la data:', ['raw_data' => $data]);
    
        if (is_null($data) || $data === '') {
            Log::warning('⚠️ Data mancante o vuota. Uso data odierna.');
            return now()->toDateString();
        }
    
        try {
            if (is_numeric($data)) {
                // Valore numerico: potrebbe essere un seriale Excel
                $converted = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data);
                Log::info('📅 Data convertita da seriale Excel:', ['excel_seriale' => $data, 'convertita' => $converted]);
                return \Carbon\Carbon::instance($converted)->toDateString();
            } else {
                // Prova a fare il parse di una data stringa
                $parsed = \Carbon\Carbon::parse($data);
                Log::info('📅 Data convertita da stringa:', ['originale' => $data, 'convertita' => $parsed]);
                return $parsed->toDateString();
            }
        } catch (\Throwable $e) {
            Log::error('❌ Errore durante il parsing della data', [
                'valore' => $data,
                'errore' => $e->getMessage()
            ]);
            return now()->toDateString();
        }
    }
    

}
