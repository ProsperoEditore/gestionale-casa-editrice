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
        $magazzino = Magazzino::with('anagrafica')->findOrFail($magazzino_id);
        $giacenze = Giacenza::where('magazzino_id', $magazzino_id)->get();
        $libri = Libro::with('marchio_editoriale')->get();

        $query = Giacenza::query()->where('magazzino_id', $magazzino_id);

        // Filtro di ricerca per titolo
        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');
            $query->whereHas('libro', function ($q) use ($searchTerm) {
                $q->where('titolo', 'like', '%' . $searchTerm . '%');
            });
        }
    
        // Carica i risultati filtrati
        $giacenze = $query->paginate(200); 
    
        // Carica le informazioni per il magazzino
        $magazzino = Magazzino::findOrFail($magazzino_id);

        return view('giacenze.create', compact('magazzino', 'giacenze', 'libri'));
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
            if (empty($giacenzaData['isbn']) || empty($giacenzaData['titolo']) || (int)$giacenzaData['quantita'] === 0) {
                continue;
            }

            $libro = Libro::where('isbn', $giacenzaData['isbn'])->first();
            if (!$libro) {
                continue; // Salta questa riga se non esiste un libro corrispondente
            }

            $fields = [
                'magazzino_id' => $magazzino_id,
                'isbn' => $giacenzaData['isbn']
            ];

            $values = [
                'titolo' => $giacenzaData['titolo'],
                'quantita' => $giacenzaData['quantita'],
                'prezzo' => $giacenzaData['prezzo'],
                'note' => $giacenzaData['note'],
                'data_ultimo_aggiornamento' => now(),
                'libro_id' => $libro?->id,
            ];

            if ($categoria === 'magazzino editore') {
                $values['costo_produzione'] = $giacenzaData['costo_produzione'] ?? $giacenzaData['costo_sconto'] ?? 0;
                $values['sconto'] = null;
            } else {
                $values['sconto'] = $giacenzaData['sconto'] ?? $giacenzaData['costo_sconto'] ?? 0;
                $values['costo_produzione'] = null;
            }

            $giacenza = Giacenza::updateOrCreate($fields, $values);
            $savedIds[] = ['id' => $giacenza->id, 'isbn' => $giacenza->isbn];
        }

        return response()->json(['success' => true, 'saved_ids' => $savedIds]);
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
