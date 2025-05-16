<?php

namespace App\Imports;

use App\Models\Libro;
use App\Models\RegistroTiraturaDettaglio;
use App\Models\RegistroTirature;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class RegistroTiraturaDettaglioImport implements ToCollection, WithHeadingRow
{
    protected $registro;
    protected static $errori = [];

    public function __construct(RegistroTirature $registro)
    {
        $this->registro = $registro;
    }

    public function collection(Collection $rows)
    {
        $righeAmbigue = [];

        foreach ($rows as $index => $row) {
            $rigaExcel = $index + 2;
            $dataRaw = trim($row['data'] ?? '');
            $isbn = trim($row['isbn'] ?? '');
            $titoloInput = trim($row['titolo'] ?? '');
            $copie = intval($row['copie_stampate'] ?? 0);

            if (empty($dataRaw) && empty($isbn) && empty($titoloInput) && $copie === 0) continue;

            $data = $this->parseData($dataRaw);
            if (!$data) {
                self::$errori[] = "Errore alla riga $rigaExcel: data non valida.";
                continue;
            }

            $libro = null;

            if (!empty($isbn)) {
                $libro = Libro::where('isbn', $isbn)->first();
                if (!$libro) {
                    self::$errori[] = "Errore alla riga $rigaExcel: ISBN '$isbn' non trovato.";
                    continue;
                }
            } else {
                $titoloNormalizzato = strtolower(preg_replace('/[^a-z0-9]/i', '', $titoloInput));
                $libri = Libro::all()->filter(function ($libro) use ($titoloNormalizzato) {
                    $tDb = strtolower(preg_replace('/[^a-z0-9]/i', '', $libro->titolo));
                    similar_text($tDb, $titoloNormalizzato, $percentuale);
                    return str_contains($tDb, $titoloNormalizzato) || $percentuale > 75;
                });

                if ($libri->count() === 1) {
                    $libro = $libri->first();
                } elseif ($libri->count() > 1) {
                    $righeAmbigue[] = [
                        'data' => $data,
                        'copie_stampate' => $copie,
                        'titolo' => $titoloInput,
                        'isbn' => '',
                        'opzioni' => $libri->map(fn($l) => ['id' => $l->id, 'titolo' => $l->titolo])->values()->all(),
                    ];
                    continue;
                } else {
                    self::$errori[] = "Errore alla riga $rigaExcel: titolo '$titoloInput' non trovato.";
                    continue;
                }
            }

            if ($libro) {
                $prezzo = floatval($libro->prezzo);
                $imponibileRelativo = $copie * $prezzo * 0.3;
                $imponibile = $imponibileRelativo / 1.04;
                $iva = $imponibileRelativo - $imponibile;

                RegistroTiraturaDettaglio::create([
                    'registro_tirature_id' => $this->registro->id,
                    'titolo_id' => $libro->id,
                    'data' => $data,
                    'copie_stampate' => $copie,
                    'prezzo_vendita_iva' => $prezzo,
                    'imponibile_relativo' => $imponibileRelativo,
                    'imponibile' => $imponibile,
                    'iva_4percento' => $iva,
                ]);
            }
        }

        if (!empty($righeAmbigue)) {
            Session::put('righe_ambigue_tirature', $righeAmbigue);
        }

        if (!empty(self::$errori)) {
            Session::flash('import_errori', self::$errori);
        } else {
            Session::flash('success', 'Tirature importate con successo!');
        }
    }

    private function parseData($data)
    {
        try {
            return is_numeric($data)
                ? Carbon::instance(ExcelDate::excelToDateTimeObject($data))->toDateString()
                : Carbon::parse($data)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
