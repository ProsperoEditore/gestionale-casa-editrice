<?php

namespace App\Imports;

use App\Models\Libro;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrdineLibriImport implements ToCollection, WithHeadingRow
{
    protected $ordine;
    protected static $errori = [];

    public function __construct($ordine)
    {
        $this->ordine = $ordine;
    }

    public function collection(Collection $rows)
    {
        $righeAmbigue = [];

        foreach ($rows as $index => $row) {
            $rigaExcel = $index + 2;
            $isbn = trim($row['isbn'] ?? '');
            $titoloInput = trim($row['titolo'] ?? '');
            $quantita = intval($row['quantita'] ?? 0);
            $sconto = floatval(str_replace(',', '.', $row['sconto'] ?? 0));

            if (empty($quantita) || (empty($isbn) && empty($titoloInput))) {
                self::$errori[] = "Errore alla riga $rigaExcel: specificare almeno la quantità e un valore tra ISBN o titolo.";
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
                        'quantita' => $quantita,
                        'sconto' => $sconto,
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
                $this->ordine->libri()->attach($libro->id, [
                    'isbn' => $libro->isbn,
                    'titolo' => $libro->titolo,
                    'quantita' => $quantita,
                    'prezzo_copertina' => $libro->prezzo_copertina,
                    'sconto' => $sconto,
                ]);
            }
        }

        if (!empty($righeAmbigue)) {
            Session::put('righe_ambigue_ordini', $righeAmbigue);
        }

        if (!empty(self::$errori)) {
            Session::flash('import_errori', self::$errori);
        } else {
            Session::flash('success', 'Libri importati con successo!');
        }
    }
}
