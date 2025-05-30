<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Giacenza;
use App\Models\Magazzino;
use App\Models\Libro;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GiacenzeImport;
use App\Exports\GiacenzeExport;

class GiacenzaController extends Controller
{
public function create($magazzino_id, Request $request)
{
    // Carica il magazzino con anagrafica
    $magazzino = Magazzino::with('anagrafica')->findOrFail($magazzino_id);
    $libri = Libro::with('marchio_editoriale')->get();

    // Costruisci query base
    $query = Giacenza::query()->where('magazzino_id', $magazzino_id);

    // Applica filtro per titolo (ricerca)
    if ($request->filled('search')) {
        $searchTerm = strtolower($request->input('search'));
        $query->whereHas('libro', function ($q) use ($searchTerm) {
            $q->whereRaw("LOWER(titolo) LIKE ?", ["%{$searchTerm}%"]);
        });
    }

    // Esegui query con join e ordinamento per marchio
    $giacenze = $query
        ->join('libri', 'giacenze.libro_id', '=', 'libri.id')
        ->leftJoin('marchio_editoriales', 'libri.marchio_editoriale_id', '=', 'marchio_editoriales.id')
        ->orderByRaw("
            CASE marchio_editoriales.nome
                WHEN 'Prospero Editore' THEN 1
                WHEN 'Calibano Editore' THEN 2
                WHEN 'Miranda Editrice' THEN 3
                ELSE 4
            END
        ")
        ->orderBy('libri.titolo')
        ->select('giacenze.*')
        ->get();

    // Calcolo dei totali SOLO se il magazzino non è troppo grande
    $totali = null;
    $categoria = strtolower($magazzino->anagrafica->categoria ?? '');

    if (!in_array($categoria, ['magazzino editore', 'distributore'])) {
        $giacenzeConRelazioni = Giacenza::with('libro.marchio_editoriale')
            ->where('magazzino_id', $magazzino_id)
            ->get();

        $totali = [
            'marchi' => $giacenzeConRelazioni->pluck('libro.marchio_editoriale.nome')->filter()->unique()->count(),
            'titoli' => $giacenzeConRelazioni->count(),
            'quantita' => $giacenzeConRelazioni->sum('quantita'),
            'valore_lordo' => $giacenzeConRelazioni->sum(fn($g) => $g->prezzo * $g->quantita),
            'costo_totale' => $giacenzeConRelazioni->sum(fn($g) => $g->costo_produzione * $g->quantita),
        ];
    }

    return view('giacenze.create', compact('magazzino', 'giacenze', 'libri', 'totali'));
}


    public function store(Request $request, $magazzino_id)
    {
        $data = $request->json()->all();
    
        if (!isset($data['giacenze']) || !is_array($data['giacenze'])) {
            return response()->json(['success' => false, 'message' => 'Dati non validi.']);
        }
    
        $categoria = Magazzino::with('anagrafica')->find($magazzino_id)?->anagrafica?->categoria;
        $savedIds = [];
    
        foreach ($data['giacenze'] as $giacenzaData) {
            if (empty($giacenzaData['isbn']) || empty($giacenzaData['titolo'])) {
                continue;
            }

    
            $libro = Libro::where('isbn', $giacenzaData['isbn'])->first();
            if (!$libro) {
                continue;
            }
    
            $giacenza = Giacenza::where('magazzino_id', $magazzino_id)
                                ->where('isbn', $giacenzaData['isbn'])
                                ->first();
    
            $isNuova = false;
            if (!$giacenza) {
                $giacenza = new Giacenza([
                    'magazzino_id' => $magazzino_id,
                    'isbn' => $giacenzaData['isbn'],
                    'libro_id' => $libro->id,
                ]);
                $isNuova = true;
            }
    
            $modificata = false;
    
            // Campi comuni
            foreach (['titolo', 'quantita', 'prezzo', 'note'] as $campo) {
                if ($giacenza->$campo != $giacenzaData[$campo]) {
                    $giacenza->$campo = $giacenzaData[$campo];
                    $modificata = true;
                }
            }
    
            // Campi condizionati
            if ($categoria === 'magazzino editore') {
                $costo = $giacenzaData['costo_produzione'] ?? $giacenzaData['costo_sconto'] ?? 0;
                if ($giacenza->costo_produzione != $costo) {
                    $giacenza->costo_produzione = $costo;
                    $modificata = true;
                }
                if ($giacenza->sconto !== null) {
                    $giacenza->sconto = null;
                    $modificata = true;
                }
            } else {
                $sconto = $giacenzaData['sconto'] ?? $giacenzaData['costo_sconto'] ?? 0;
                if ($giacenza->sconto != $sconto) {
                    $giacenza->sconto = $sconto;
                    $modificata = true;
                }
                if ($giacenza->costo_produzione !== null) {
                    $giacenza->costo_produzione = null;
                    $modificata = true;
                }
            }
    
                if ($modificata || $isNuova) {
                    $giacenza->data_ultimo_aggiornamento = now();
                    $giacenza->save();
                    $savedIds[] = ['id' => $giacenza->id, 'isbn' => $giacenza->isbn];
                }
            
        }
    
        return response()->json(['success' => true, 'saved_ids' => $savedIds]);
    }
    

public function storeSingola(Request $request, $id_or_magazzino, $maybe_magazzino = null)
{
    // Verifica se stai aggiornando o creando
    if ($request->isMethod('put')) {
        $id = $id_or_magazzino;
        $magazzino_id = $maybe_magazzino;
    } else {
        $id = null;
        $magazzino_id = $id_or_magazzino;
    }

    $magazzino = Magazzino::with('anagrafica')->find($magazzino_id);
    if (!$magazzino) {
        return response()->json(['success' => false, 'message' => 'Magazzino non trovato.']);
    }
    try {
        $data = $request->input('giacenza');

        if (empty($data['isbn']) || empty($data['titolo'])) {
            return response()->json(['success' => false, 'message' => 'Dati mancanti.', 'debug' => $data]);
        }

        $libro = Libro::where('isbn', $data['isbn'])->first();
        if (!$libro) {
            return response()->json(['success' => false, 'message' => 'Libro non trovato.']);
        }

        if ($request->isMethod('put') && $id) {
            $giacenza = Giacenza::find($id);
            if (!$giacenza) {
                return response()->json(['success' => false, 'message' => 'Giacenza non trovata.']);
            }
        } else {
            $giacenza = Giacenza::where('magazzino_id', $magazzino_id)->where('isbn', $data['isbn'])->first();
            if (!$giacenza) {
                $giacenza = new Giacenza();
                $giacenza->magazzino_id = $magazzino_id;
                $giacenza->isbn = $data['isbn'];
            }
        }

        $giacenza->libro_id = $libro->id;
        $giacenza->titolo = $data['titolo'];
        $giacenza->quantita = $data['quantita'];
        $giacenza->prezzo = $data['prezzo'];
        $giacenza->note = $data['note'] ?? null;
        $giacenza->data_ultimo_aggiornamento = now();

        $giacenza->loadMissing('magazzino.anagrafica');

        if ($giacenza->magazzino && $giacenza->magazzino->anagrafica && $giacenza->magazzino->anagrafica->categoria === 'magazzino editore') {
            $giacenza->costo_produzione = $data['costo_produzione'] ?? 0;
            $giacenza->sconto = null;
        } else {
            $giacenza->sconto = $data['sconto'] ?? 0;
            $giacenza->costo_produzione = null;
        }

        $giacenza->save();

        return response()->json(['success' => true, 'id' => $giacenza->id]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Eccezione: ' . $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);
    }
}





    public function importGiacenze(Request $request, $magazzino_id)
    {
        Excel::import(new GiacenzeImport($magazzino_id), $request->file('file'));
        return back()->with('success', 'Giacenze importate con successo!');
    }

    public function exportGiacenze($magazzino_id)
    {
    return Excel::download(new GiacenzeExport($magazzino_id), 'giacenze.xlsx');
    }

    public function edit(Giacenza $giacenza)
    {
        $magazzini = Magazzino::with('anagrafica')->get();
        $libri = Libro::all();
        return view('giacenze.edit', compact('giacenza', 'magazzini', 'libri'));
    }

    public function update(Request $request, Giacenza $giacenza)
    {
        $request->validate([
            'magazzino_id' => 'required|exists:magazzini,id',
            'libro_id' => 'required|exists:libri,id',
            'quantita' => 'required|integer|min:1'
        ]);

        $giacenza->update($request->all());

        return redirect()->route('giacenze.index')->with('success', 'Giacenza aggiornata con successo!');
    }

    public function destroy($id)
    {
        $giacenza = Giacenza::find($id);

        if ($giacenza) {
            $giacenza->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Giacenza non trovata.']);
        }
    }
}
