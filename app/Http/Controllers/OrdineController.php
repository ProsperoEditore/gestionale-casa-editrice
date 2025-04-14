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

    public function autocompleteAnagrafica(Request $request)
    {
    $search = $request->input('query');

    $anagrafiche = Anagrafica::where('nome', 'like', "%{$search}%")
        ->select('id', 'nome')
        ->limit(10)
        ->get();

    return response()->json($anagrafiche);
    }


    public function store(Request $request)
    {
        // Definizione regole di validazione di base
        $rules = [
            'codice' => 'required|string|unique:ordines,codice',
            'data' => 'required|date',
            'anagrafica_id' => 'required|exists:anagraficas,id',
            'tipo_ordine' => 'required|string',
        ];
    
        // Se NON è un omaggio, il campo 'canale' è obbligatorio
        if ($request->input('tipo_ordine') !== 'omaggio') {
            $rules['canale'] = 'required|string';
        } else {
            // Se è omaggio, imposta comunque un valore per evitare problemi di database
            $request->merge(['canale' => 'omaggio']);
        }
    
        // Validazione
        $validatedData = $request->validate($rules);
    
        // Creazione dell'ordine
        $ordine = Ordine::create($validatedData);
    
        // Se è un ordine di tipo "conto deposito", crea il magazzino (se non esiste)
        if ($ordine->tipo_ordine === 'conto deposito') {
            $magazzinoEsistente = \App\Models\Magazzino::where('anagrafica_id', $ordine->anagrafica_id)->first();
    
            if (!$magazzinoEsistente) {
                \App\Models\Magazzino::create([
                    'anagrafica_id' => $ordine->anagrafica_id,
                    'prossima_scadenza' => null,
                ]);
            }
        }
    
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
    
        // Salviamo i dati originali dei libri con quantità
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
    
        // Aggiorna i libri dell'ordine
        if ($request->has('libri')) {
            $libriSync = [];
    
            foreach ($request->input('libri') as $libroId => $dati) {
                $libriSync[$libroId] = [
                    'quantita' => $dati['quantita'],
                    'sconto' => $dati['sconto'] ?? 0,
                    'info_spedizione' => $dati['info_spedizione'] ?? null,
                ];
            }
    
            $ordine->libri()->sync($libriSync);
        }
    
        // Ricarica i libri con pivot aggiornata
        $ordine->load(['libri' => function ($query) {
            $query->withPivot(['quantita', 'sconto', 'info_spedizione']);
        }]);
    
        foreach ($ordine->libri as $libro) {
            $libroId = $libro->id;
            $quantitaPrecedente = (int) ($libriPrecedenti[$libroId]->pivot->quantita ?? 0);
            $quantitaNuova = (int) $libro->pivot->quantita;
            $differenza = $quantitaNuova - $quantitaPrecedente;
    
            if ($differenza === 0) {
                continue;
            }
    
            // ✅ Se l'ordine è "conto deposito", aggiorna la giacenza nel magazzino del cliente
            if ($ordine->tipo_ordine === 'conto deposito') {
                $magazzino = \App\Models\Magazzino::where('anagrafica_id', $ordine->anagrafica_id)->first();
    
                if ($magazzino) {
                    $giacenza = \App\Models\Giacenza::firstOrNew([
                        'magazzino_id' => $magazzino->id,
                        'libro_id' => $libroId,
                    ]);
    
                    $giacenza->isbn = $libro->isbn;
                    $giacenza->titolo = $libro->titolo;
                    $giacenza->quantita = max(0, ($giacenza->quantita ?? 0) + $differenza);
                    $giacenza->prezzo = $libro->prezzo_copertina;
                    $giacenza->sconto = $libro->pivot->sconto;
                    $giacenza->costo_produzione = $libro->costo_produzione;
                    $giacenza->data_ultimo_aggiornamento = now();
                    $giacenza->note = 'Aggiornata per delta ordine ' . $ordine->codice;
                    $giacenza->save();
                }
            }
    
            // ✅ Indipendentemente dal tipo ordine, aggiorna magazzino editore se necessario
            $info = strtolower(trim($libro->pivot->info_spedizione ?? ''));
    
            if ($info === 'spedito da magazzino editore' && $quantitaNuova > $quantitaPrecedente) {
                $magazzinoEditore = \App\Models\Magazzino::whereHas('anagrafica', function ($query) {
                    $query->where('categoria', 'magazzino editore');
                })->first();
    
                if ($magazzinoEditore) {
                    $giacenzaEditore = \App\Models\Giacenza::where('magazzino_id', $magazzinoEditore->id)
                        ->where('libro_id', $libroId)
                        ->first();
    
                    if ($giacenzaEditore) {
                        $delta = $quantitaNuova - $quantitaPrecedente;
                        $giacenzaEditore->quantita = max(0, $giacenzaEditore->quantita - $delta);
                        $giacenzaEditore->note = 'Aggiornato (aggiunta copie in ordine ' . $ordine->codice . ')';
                        $giacenzaEditore->data_ultimo_aggiornamento = now();
                        $giacenzaEditore->save();
                    }
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
    
        $filename = 'ordine_' . preg_replace('/[\/\\\\]/', '-', $ordine->codice) . '.pdf';

        return PDF::loadView('ordini.pdf', compact('ordine', 'marchio'))
          ->download($filename);
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
            'file' => 'required|mimes:xlsx'
        ]);
    
        $file = $request->file('file');
        $rows = Excel::toCollection(null, $file)->first();
    
        foreach ($rows as $row) {
            // Estrazione dei dati
            $isbn = $row['isbn'] ?? null;
            $quantita = $row['quantita'];
            $titolo = $row['titolo'] ?? null;
            $sconto = $row['sconto'] ?? 0;
    
            if (empty($quantita)) {
                continue; // Ignora righe senza quantità
            }
    
            // Trova il libro usando ISBN o Titolo
            $libro = null;
            if ($isbn) {
                $libro = Libro::where('isbn', $isbn)->first();
            } elseif ($titolo) {
                // Usa LIKE per trovare titoli simili
                $libro = Libro::where('titolo', 'like', '%' . $titolo . '%')->first();
            }
    
            // Se un libro è trovato, salvalo nell'ordine
            if ($libro) {
                OrdineLibro::create([
                    'ordine_id' => $id,
                    'libro_id' => $libro->id,
                    'isbn' => $libro->isbn,
                    'titolo' => $libro->titolo,
                    'quantita' => $quantita,
                    'prezzo_copertina' => $libro->prezzo_copertina,
                    'sconto' => $sconto,
                ]);
            }
        }
    
        return redirect()->route('ordini.gestione_libri', $id)->with('success', 'Libri importati correttamente.');
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
            'specifiche_iva' => $request->input('specifiche_iva'),
            'costo_spedizione' => $request->input('costo_spedizione'),
            'altre_specifiche_iva' => $request->input('altre_specifiche_iva'),
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
    
                    // ✅ Aggiorna magazzino editore se richiesto
                    $info_normalized = strtolower(trim($info));
                    if ($info_normalized === 'spedito da magazzino editore') {
                        $magazzinoEditore = \App\Models\Magazzino::whereHas('anagrafica', function ($query) {
                            $query->where('categoria', 'magazzino editore');
                        })->first();
    
                        if ($magazzinoEditore) {
                            $giacenzaEditore = \App\Models\Giacenza::where('magazzino_id', $magazzinoEditore->id)
                                ->where('libro_id', $libro_id)
                                ->first();
    
                            if ($giacenzaEditore) {
                                $giacenzaEditore->quantita = max(0, $giacenzaEditore->quantita - $quantita);
                                $giacenzaEditore->note = 'Sottratto con ordine ' . $ordine->codice;
                                $giacenzaEditore->data_ultimo_aggiornamento = now();
                                $giacenzaEditore->save();
                            }
                        }
                    }
                }
            }
    
            // ✅ Se è "conto deposito", aggiorna il magazzino cliente
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
    
        // ✅ Se è "acquisto", crea automaticamente un registro vendite
        if ($ordine->tipo_ordine === 'acquisto') {
            $registro = \App\Models\RegistroVendite::create([
                'anagrafica_id' => $ordine->anagrafica_id,
                'periodo' => date('Y'),
                'origine' => 'ordine',
                'ordine_id' => $ordine->id,
            ]);
    
            foreach ($request->titolo as $index => $libro_id) {
                $libro = \App\Models\Libro::find($libro_id);
    
                \App\Models\RegistroVenditeDettaglio::create([
                    'registro_vendita_id' => $registro->id,
                    'data' => $ordine->data,
                    'periodo' => date('Y'),
                    'isbn' => $libro->isbn,
                    'titolo' => $libro->titolo,
                    'quantita' => $request->quantita[$index],
                    'prezzo' => $libro->prezzo,
                    'valore_lordo' => $valore_lordo,
                ]);
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
