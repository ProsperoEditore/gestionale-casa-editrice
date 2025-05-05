<?php

namespace App\Imports;

use Illuminate\Support\Facades\Session;
use App\Models\RegistroVenditeDettaglio;
use App\Models\Libro;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class RegistroVenditeImport implements ToModel, WithHeadingRow
{
    protected $registroVendita;
    protected static $errori = [];
    protected static $riga = 1;

    public function __construct($registroVendita)
    {
        $this->registroVendita = $registroVendita;
    }

    public function model(array $row)
    {
        self::$riga++;

        $dataRaw = trim($row['data'] ?? $row['Data'] ?? '');
        $isbn = trim($row['isbn'] ?? $row['ISBN'] ?? '');
        $quantita = $row['quantita'] ?? $row['Quantità'] ?? 0;
        $periodo = $row['periodo'] ?? $row['Periodo'] ?? 'N/D';

        // ✅ Salta righe completamente vuote
        if (empty($isbn) && empty($quantita)) {
            return null;
        }

        // ✅ Parsing sicuro della data
        $data = $this->parseData($dataRaw);
        if (is_null($data)) {
            self::$errori[] = "Errore alla riga " . self::$riga . ": formato data non valido.";
            return null;
        }

        // ✅ Verifica ISBN
        $libro = Libro::where('isbn', $isbn)->first();
        if (!$libro) {
            self::$errori[] = "Errore alla riga " . self::$riga . ": ISBN '{$isbn}' non trovato nel catalogo.";
            return null;
        }

        $titolo = $libro->titolo;
        $prezzo = $libro->prezzo ?? 0;

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
            return null;
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

    public function __destruct()
    {
        if (!empty(self::$errori)) {
            Session::flash('import_errori', self::$errori);
        } else {
            Session::flash('success', 'Vendite importate con successo!');
        }
    }
}
