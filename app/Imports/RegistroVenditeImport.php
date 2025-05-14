<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use App\Models\RegistroVenditeDettaglio;
use App\Models\Libro;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RegistroVenditeImport implements ToCollection, WithHeadingRow
{
    protected $registroVendita;
    protected static $errori = [];

    public function __construct($registroVendita)
    {
        $this->registroVendita = $registroVendita;
    }

    public function collection(Collection $rows)
    {
        $righeAmbigue = [];

        foreach ($rows as $index => $row) {
            $rigaExcel = $index + 2; // corrisponde alla riga del file (dopo header)

            $dataRaw = trim($row['data'] ?? '');
            $isbn = trim($row['isbn'] ?? '');
            $titoloInput = trim($row['titolo'] ?? '');
            $quantita = $row['quantita'] ?? 0;
            $periodo = $row['periodo'] ?? 'N/D';

            if (empty($isbn) && empty($titoloInput) && empty($quantita)) {
                continue;
            }

            $data = $this->parseData($dataRaw);
            if (is_null($data)) {
                self::$errori[] = "Errore alla riga $rigaExcel: formato data non valido.";
                continue;
            }

            $libro = null;

            if (!empty($isbn)) {
                $libro = Libro::where('isbn', $isbn)->first();
                if (!$libro) {
                    self::$errori[] = "Errore alla riga $rigaExcel: ISBN '{$isbn}' non trovato.";
                    continue;
                }
            } else {
                // ricerca per titolo
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
                } elseif ($candidati->count() > 1) {
                    $opzioni = $candidati->map(function ($libro) {
                        return [
                            'isbn' => $libro->isbn,
                            'titolo' => $libro->titolo,
                        ];
                    })->values()->all();

                    $righeAmbigue[] = [
                        'data' => $data,
                        'periodo' => $periodo,
                        'quantita' => $quantita,
                        'titolo' => $titoloInput,
                        'isbn' => $isbn,
                        'opzioni' => $opzioni,
                    ];
                    continue;
                } else {
                    self::$errori[] = "Errore alla riga $rigaExcel: titolo '{$titoloInput}' non trovato.";
                    continue;
                }
            }

            if ($libro) {
                RegistroVenditeDettaglio::create([
                    'registro_vendita_id' => $this->registroVendita->id,
                    'data' => $data,
                    'periodo' => $periodo,
                    'isbn' => $libro->isbn,
                    'titolo' => $libro->titolo,
                    'quantita' => $quantita,
                    'prezzo' => $libro->prezzo ?? 0,
                    'valore_lordo' => $quantita * ($libro->prezzo ?? 0),
                ]);
            }
        }

        if (!empty($righeAmbigue)) {
            Session::put('righe_ambigue', $righeAmbigue);
        }

        if (!empty(self::$errori)) {
            Session::flash('import_errori', self::$errori);
        } else {
            Session::flash('success', 'Vendite importate con successo!');
        }
    }

    private function parseData($data)
    {
        if (is_null($data) || trim($data) === '') return null;

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
