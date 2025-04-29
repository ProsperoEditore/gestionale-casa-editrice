<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use App\Models\Libro;
use App\Models\MarchioEditoriale;
use Illuminate\Http\Request;

class LibroController extends Controller
{
    public function index(Request $request)
    {
        $query = Libro::with('marchio_editoriale');
    
        if ($request->filled('search')) {
            $query->where('titolo', 'like', '%' . $request->search . '%');
        }
    
        $items = $query->orderBy('titolo')->paginate(50)->appends($request->query());
    
        // Per popolare il menu Select2
        $tuttiTitoli = Libro::orderBy('titolo')->get();
    
        return view('libri.index', compact('items', 'tuttiTitoli'));
    }

    public function create()
    {
        $marchi = MarchioEditoriale::all(); // Recupera tutti i marchi per il menù a tendina
        return view('libri.create', compact('marchi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'isbn' => 'required|unique:libri,isbn',
            'titolo' => 'required',
            'marchio_editoriale_id' => 'required|exists:marchio_editoriales,id',
            'anno_pubblicazione' => 'required|integer',
            'prezzo' => 'required|numeric|min:0',
            'costo_produzione' => 'nullable|numeric|min:0',
            'stato' => 'nullable|in:C,FC,A',
        ]);

        Libro::create($request->all());

        return redirect()->route('libri.index')->with('success', 'Libro creato con successo.');
    }

    public function edit($id)
    {
        $libro = Libro::findOrFail($id);
        $marchi = MarchioEditoriale::all();
        return view('libri.edit', compact('libro', 'marchi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'isbn' => 'required|unique:libri,isbn,' . $id,
            'titolo' => 'required',
            'marchio_editoriale_id' => 'required|exists:marchio_editoriales,id',
            'anno_pubblicazione' => 'required|integer',
            'prezzo' => 'required|numeric|min:0',
            'costo_produzione' => 'nullable|numeric|min:0',
            'stato' => 'nullable|in:C,FC,A',
        ]);
    
        $libro = Libro::findOrFail($id);
        $vecchioStato = $libro->stato;
        $nuovoStato = $request->input('stato');
    
        // Mappa degli ID dei magazzini
        $mappaMagazzini = [
            'C'  => 1,
            'A'  => 2,
            'FC' => 3,
        ];
    
        if ($vecchioStato !== $nuovoStato && isset($mappaMagazzini[$nuovoStato])) {
            $nuovoMagazzinoId = $mappaMagazzini[$nuovoStato];
    
            $giacenzaAttuale = \App\Models\Giacenza::where('libro_id', $libro->id)
                ->whereHas('magazzino.anagrafica', function ($q) {
                    $q->where('categoria', 'magazzino editore');
                })
                ->first();
    
            if ($giacenzaAttuale) {
                $giacenzaEsistente = \App\Models\Giacenza::where('libro_id', $libro->id)
                    ->where('magazzino_id', $nuovoMagazzinoId)
                    ->first();
    
                if ($giacenzaEsistente) {
                    $giacenzaEsistente->quantita += $giacenzaAttuale->quantita;
                    $giacenzaEsistente->save();
                    $giacenzaAttuale->delete();
                } else {
                    $giacenzaAttuale->magazzino_id = $nuovoMagazzinoId;
                    $giacenzaAttuale->save();
                }
            }
        }
    
        $libro->update($request->all());
    
        return redirect()->route('libri.index')->with('success', 'Libro aggiornato con successo.');
    }
    

    public function destroy($id)
    {
        $libro = Libro::findOrFail($id);
        $libro->delete();

        return redirect()->route('libri.index')->with('success', 'Libro eliminato con successo.');
    }

    public function autocomplete(Request $request)
    {
        $term = strtolower($request->input('term'));
    
        try {
            $libri = Libro::where(function ($query) use ($term) {
                    $query->whereRaw("LOWER(titolo) LIKE ?", ['%' . $term . '%'])
                          ->orWhereRaw("LOWER(isbn) LIKE ?", ['%' . $term . '%']);
                })
                ->limit(20)
                ->get(['id', 'isbn', 'titolo', 'prezzo']);
    
            $formatted = $libri->map(function ($libro) {
                return [
                    'id' => $libro->id,
                    'text' => $libro->titolo,
                    'isbn' => $libro->isbn,
                    'prezzo' => $libro->prezzo
                ];
            });
    
            return response()->json($formatted);
    
        } catch (\Exception $e) {
            \Log::error('Errore in autocomplete: ' . $e->getMessage());
            return response()->json(['error' => 'Errore server.'], 500);
        }
    }
    
    
    
    
    
    
    
     

    public function import(Request $request)
    {
    $request->validate([
        'file' => 'required|mimes:xlsx|max:2048',
    ]);

    $file = $request->file('file')->getRealPath();
    $spreadsheet = IOFactory::load($file);
    $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    foreach ($data as $index => $row) {
        if ($index === 0) continue; // Salta l'intestazione

        $marchio = MarchioEditoriale::where('nome', $row['D'])->first();
        if (!$marchio) {
            continue; // Salta se il marchio non esiste
        }

        Libro::updateOrCreate(
            ['isbn' => $row['A']], // Cerca il libro per ISBN
            [
                'titolo' => $row['B'],
                'anno_pubblicazione' => intval($row['C']),
                'marchio_editoriale_id' => $marchio->id,
                'prezzo' => floatval($row['E']),
                'stato' => $row['F']
            ]
        );
    }

    return redirect()->route('libri.index')->with('success', 'Libri importati con successo!');
    }
}