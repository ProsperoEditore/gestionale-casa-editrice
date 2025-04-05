<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ordine;
use App\Models\Libro;
use App\Models\Anagrafica;
use App\Models\MarchioEditoriale;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdineController extends Controller
{

    public function index(Request $request)
    {
        $query = Ordine::with('anagrafica');
    
        // Filtro di ricerca per anagrafica
        if ($request->filled('search')) {
            $query->whereHas('anagrafica', function ($query) use ($request) {
                $query->where('nome', 'like', '%' . $request->search . '%');
            });
        }
    
        $ordini = $query->latest()->paginate(100);
    
        return view('ordini.index', compact('ordini'));
    }
    
    

    public function create()
    {
        $anagrafiche = Anagrafica::all();
        return view('ordini.create', compact('anagrafiche'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'codice' => 'required|string|unique:ordines,codice',
            'data' => 'required|date',
            'anagrafica_id' => 'required|exists:anagraficas,id',
            'canale' => 'required|string',
            'tipo_ordine' => 'required|string',
        ]);
    
        // Creazione dell'ordine
        $ordine = Ordine::create($validatedData);
    
        // Reindirizzamento alla pagina index con un messaggio di successo
        return redirect()->route('ordini.index')->with('success', 'Ordine aggiunto con successo.');
    }

    public function edit($id)
    {
        $ordine = Ordine::findOrFail($id);
        $anagrafiche = Anagrafica::all();
        return view('ordini.edit', compact('ordine', 'anagrafiche'));
    }

    public function destroy($id)
    {
    $ordine = Ordine::findOrFail($id);
    $ordine->delete();

    return redirect()->route('ordini.index')->with('success', 'Ordine eliminato con successo.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'data' => 'required|date',
            'anagrafica_id' => 'required|exists:anagraficas,id',
        ]);
    
        $ordine = \App\Models\Ordine::findOrFail($id);
    
        // Salviamo prima i dati originali dei libri con quantità
        $libriPrecedenti = $ordine->libri()->withPivot('quantita')->get()->keyBy('id');
    
        // Aggiorna i dati dell'ordine
        $ordine->update([
            'data' => $request->input('data'),
            'anagrafica_id' => $request->input('anagrafica_id'),
            'tipo_ordine' => $request->input('tipo_ordine'),
            'causale' => $request->input('causale'),
            'condizioni_conto_deposito' => $request->input('condizioni_conto_deposito'),
            'totale_netto_compilato' => $request->input('totale_netto_compilato'),
            'tempi_pagamento' => $request->input('tempi_pagamento'),
            'modalita_pagamento' => $request->input('modalita_pagamento'),
        ]);
        
    
        // ✅ Aggiorna i libri dell'ordine
        if ($request->has('libri')) {
            $libriSync = [];
    
            foreach ($request->input('libri') as $libroId => $dati) {
                $libriSync[$libroId] = [
                    'quantita' => $dati['quantita'],
                    'sconto' => $dati['sconto'] ?? 0,
                ];
            }
    
            $ordine->libri()->sync($libriSync);
        }
    
        // 🔁 Ricarica i libri dell'ordine con pivot aggiornata
        $ordine->load(['libri' => function ($query) {
            $query->withPivot(['quantita', 'sconto']);
        }]);
    
        // ✅ Gestione Delta per conto deposito
        if ($ordine->tipo_ordine === 'conto deposito') {
            $magazzino = \App\Models\Magazzino::where('anagrafica_id', $ordine->anagrafica_id)->first();
    
            if ($magazzino) {
                foreach ($ordine->libri as $libro) {
                    $libroId = $libro->id;
                    $quantitaNuova = (int) $libro->pivot->quantita;
                    $quantitaVecchia = (int) ($libriPrecedenti[$libroId]->pivot->quantita ?? 0);
                    $differenza = $quantitaNuova - $quantitaVecchia;
    
                    if ($differenza === 0) {
                        continue; // Nessuna modifica per questo libro
                    }
    
                    $giacenza = \App\Models\Giacenza::firstOrNew([
                        'magazzino_id' => $magazzino->id,
                        'libro_id' => $libroId,
                    ]);
    
                    $giacenza->isbn = $libro->isbn;
                    $giacenza->titolo = $libro->titolo;
                    $giacenza->quantita = max(0, ($giacenza->quantita ?? 0) + $differenza); // Evita valori negativi
                    $giacenza->prezzo = $libro->prezzo_copertina;
                    $giacenza->sconto = $libro->pivot->sconto;
                    $giacenza->costo_produzione = $libro->costo_produzione;
                    $giacenza->data_ultimo_aggiornamento = now();
                    $giacenza->note = 'Aggiornata per delta ordine ' . $ordine->codice;
    
                    $giacenza->save();
                }
            }
        }
    
        return redirect()->route('ordini.index')->with('success', 'Ordine aggiornato con successo.');
    }
    
    
    
    

    public function stampa($id)
    {
        $ordine = Ordine::with([
            'anagrafica',
            'libri.marchio_editoriale',
        ])->findOrFail($id);
    
        // Raccogli i marchi editoriali unici
        $marchi = $ordine->libri->pluck('marchio_editoriale')->filter()->unique('id');
    
        if ($marchi->count() === 1) {
            $marchio = $marchi->first();
        } else {
            $marchio = MarchioEditoriale::where('nome', 'Prospero Editore')->first();
        }
    
        $pdf = Pdf::loadView('ordini.pdf', compact('ordine', 'marchio'));
    
        return $pdf->download('ordine_' . $ordine->codice . '.pdf');
    }
    

    public function gestioneLibri($id)
    {
    $ordine = Ordine::with('libri')->findOrFail($id);
    $libri = Libro::all();
    return view('ordini.ordini_libri', compact('ordine', 'libri'));
    }

    public function importLibri(Request $request, $id)
    {
    $request->validate([
        'file' => 'required|mimes:xlsx',
    ]);

    // Qui implementi l'importazione dei dati da Excel.

    return redirect()->route('ordini.gestione_libri', $id)->with('success', 'Libri importati con successo.');
    }

    public function show($id)
    {
    return redirect()->route('ordini.gestione_libri', ['id' => $id]);
    }

    public function storeLibri(Request $request, $id)
    {
        $ordine = Ordine::findOrFail($id);
        $ordine->update([
            'causale' => $request->input('causale'),
            'condizioni_conto_deposito' => $request->input('condizioni_conto_deposito'),
            'totale_netto_compilato' => $request->input('totale_netto_compilato'),
            'tempi_pagamento' => $request->input('tempi_pagamento'),
            'modalita_pagamento' => $request->input('modalita_pagamento'),
        ]);
        $ordine->libri()->detach();
    
        if ($request->has('titolo') && is_array($request->titolo)) {
            foreach ($request->titolo as $index => $libro_id) {
                if (!empty($libro_id)) {
                    $quantita = $request->quantita[$index] ?? 0;
                    $prezzo = $request->prezzo[$index] ?? 0.00;
                    $valore_lordo = $request->valore_vendita_lordo[$index] ?? 0.00;
                    $sconto = $request->sconto[$index] ?? 0.00;
                    $netto = $request->netto_a_pagare[$index] ?? 0.00;
                    $info = $request->info_spedizione[$index] ?? null;
    
                    $ordine->libri()->attach($libro_id, [
                        'quantita' => $quantita,
                        'prezzo_copertina' => $prezzo,
                        'valore_vendita_lordo' => $valore_lordo,
                        'sconto' => $sconto,
                        'netto_a_pagare' => $netto,
                        'info_spedizione' => $info,
                    ]);
                }
            }
    
            // ✅ Se è "conto deposito", aggiorna le giacenze in modo accumulativo
            if ($ordine->tipo_ordine === 'conto deposito') {
                $magazzino = \App\Models\Magazzino::where('anagrafica_id', $ordine->anagrafica_id)->first();
    
                if ($magazzino) {
                    foreach ($request->titolo as $index => $libro_id) {
                        $libro = \App\Models\Libro::find($libro_id);
    
                        $giacenza = \App\Models\Giacenza::firstOrNew([
                            'libro_id' => $libro_id,
                            'magazzino_id' => $magazzino->id,
                        ]);
    
                        $quantita_ordine = $request->quantita[$index] ?? 0;
                        $giacenza->quantita = ($giacenza->quantita ?? 0) + $quantita_ordine;
    
                        $giacenza->titolo = $libro->titolo;
                        $giacenza->sconto = $request->sconto[$index] ?? 0;
    
                        // autocompletamento
                        $giacenza->isbn = $libro->isbn;
                        $giacenza->prezzo = $libro->prezzo;
                        $giacenza->costo_produzione = $libro->costo_produzione;
                        $giacenza->data_ultimo_aggiornamento = now();
                        $giacenza->note = 'Aggiornato da ordine ' . $ordine->codice;
    
                        $giacenza->save();
                    }
                }
            }
        }
    
        return redirect()->route('ordini.gestione_libri', $id)->with('success', 'Libri salvati con successo.');
    }
    
    
    
    
    
    public function search(Request $request)
    {
    $query = $request->input('query');

    $libri = Libro::where('titolo', 'LIKE', "%{$query}%")
                    ->orWhere('isbn', 'LIKE', "%{$query}%")
                    ->limit(10)
                    ->get(['id', 'isbn', 'titolo', 'prezzo_copertina']);

    return response()->json($libri);
    }


}
