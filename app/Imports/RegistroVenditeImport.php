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
        if (is_null($data) || $data === '') {
            Log::warning('🟡 Data mancante o vuota, uso data odierna.');
            return now()->toDateString();
        }
    
        try {
            if (is_numeric($data)) {
                // 📅 Caso: Excel ha salvato la data come numero seriale
                $carbonDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data);
                $parsed = \Carbon\Carbon::instance($carbonDate)->toDateString();
                Log::info("✅ Data convertita da seriale Excel: {$data} ➜ {$parsed}");
                return $parsed;
            } else {
                // 📅 Caso: la data è una stringa (es. "2024-02-01")
                $parsed = \Carbon\Carbon::parse($data)->toDateString();
                Log::info("✅ Data convertita da stringa: {$data} ➜ {$parsed}");
                return $parsed;
            }
        } catch (\Throwable $e) {
            Log::error("❌ Errore nel parsing della data '{$data}': " . $e->getMessage());
            return now()->toDateString();
        }
    }
    
}
