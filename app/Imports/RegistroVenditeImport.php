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
        $titoloInput = trim($row['titolo'] ?? $row['Titolo'] ?? '');
        $quantita = $row['quantita'] ?? $row['Quantità'] ?? 0;
        $periodo = $row['periodo'] ?? $row['Periodo'] ?? 'N/D';

        // Se riga vuota, salta
        if (empty($isbn) && empty($titoloInput) && empty($quantita)) {
            return null;
        }

        // Validazione data
        $data = $this->parseData($dataRaw);
        if (is_null($data)) {
            self::$errori[] = "Errore alla riga " . self::$riga . ": formato data non valido.";
            return null;
        }

        $libro = null;

        // Ricerca per ISBN se presente
        if (!empty($isbn)) {
            $libro = Libro::where('isbn', $isbn)->first();
            if (!$libro) {
                self::$errori[] = "Errore alla riga " . self::$riga . ": ISBN '{$isbn}' non trovato.";
                return null;
            }
        } else {
            // Ricerca per titolo con normalizzazione
            $libri = Libro::all();
            $titoloInputNormalizzato = strtolower(preg_replace('/[^a-z0-9]/i', '', $titoloInput));

            $candidati = $libri->filter(function ($libro) use ($titoloInputNormalizzato) {
                $titoloDbNormalizzato = strtolower(preg_replace('/[^a-z0-9]/i', '', $libro->titolo));
                similar_text($titoloDbNormalizzato, $titoloInputNormalizzato, $percentuale);
                return str_contains($titoloDbNormalizzato, $titoloInputNormalizzato) ||
                       str_contains($titoloInputNormalizzato, $titoloDbNormalizzato) ||
                       $percentuale > 75;
            });

            if ($candidati->count() === 1) {
                $libro = $candidati->first();
            } else {
                // 🔴 ATTENZIONE: blocca qui per evitare slittamento dati
                // ✅ Salviamo tutto il contesto nella sessione, compreso titolo e isbn originali
                $opzioni = $candidati->map(function ($libro) {
                    return [
                        'isbn' => $libro->isbn,
                        'titolo' => $libro->titolo,
                    ];
                })->values()->all();

                Session::push('righe_ambigue', [
                    'data' => $data,
                    'periodo' => $periodo,
                    'quantita' => $quantita,
                    'titolo' => $titoloInput,
                    'isbn' => $isbn,
                    'opzioni' => $opzioni,
                ]);

                return null; // fondamentale: blocca la riga prima della creazione
            }
        }

        // ✅ Se siamo arrivati qui, tutto è valido e il libro è sicuro
        return new RegistroVenditeDettaglio([
            'registro_vendita_id' => $this->registroVendita->id,
            'data' => $data,
            'periodo' => (string) $periodo,
            'isbn' => $libro->isbn,
            'titolo' => $libro->titolo,
            'quantita' => $quantita,
            'prezzo' => $libro->prezzo ?? 0,
            'valore_lordo' => $quantita * ($libro->prezzo ?? 0),
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
