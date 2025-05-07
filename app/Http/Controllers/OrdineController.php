<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ordine;
use App\Models\Libro;
use App\Models\Anagrafica;
use App\Models\MarchioEditoriale;
use App\Models\Giacenza;
use App\Models\Magazzino;
use Barryvdh\DomPDF\Facade\Pdf;
use Milon\Barcode\DNS1D;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;



class OrdineController extends Controller
{

    public function index(Request $request)
    {
            $query = Ordine::with('anagrafica');
        
            if ($request->filled('search')) {
                $query->where('anagrafica_id', $request->search);
            }
        
    // Recupera tutti gli ordini (senza paginate qui)
    $ordini = $query->get();

    // Ordinamento personalizzato
    $ordiniOrdinati = $ordini->sort(function ($a, $b) {
        $aDate = $a->data ? \Carbon\Carbon::parse($a->data) : null;
        $bDate = $b->data ? \Carbon\Carbon::parse($b->data) : null;

        // Ordina per data decrescente (più recente prima)
        if ($aDate && $bDate && $aDate->ne($bDate)) {
            return $aDate->lt($bDate) ? 1 : -1;
        }

        // Se stessa data, ordina per tipo ordine
        $tipoCmp = strcmp($a->tipo_ordine ?? '', $b->tipo_ordine ?? '');
        if ($tipoCmp !== 0) return $tipoCmp;

        // Se stesso tipo ordine, ordina per nome anagrafica
        return strcmp($a->anagrafica->nome ?? '', $b->anagrafica->nome ?? '');
    });

    // Paginazione manuale
    $perPage = 30;
    $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
    $currentItems = $ordiniOrdinati->slice(($currentPage - 1) * $perPage, $perPage)->all();
    $paginatedOrdini = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $ordiniOrdinati->count(), $perPage, $currentPage);
    $paginatedOrdini->setPath($request->url());
    $paginatedOrdini->appends($request->query());

    $tutteAnagrafiche = Anagrafica::orderBy('nome')->get();

    return view('ordini.index', [
        'ordini' => $paginatedOrdini,
        'tutteAnagrafiche' => $tutteAnagrafiche
    ]);
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
        $tipo = $request->input('tipo_ordine');
    
        $rules = [
            'codice' => 'required|string|unique:ordines,codice',
            'data' => 'required|date',
            'anagrafica_id' => 'required|exists:anagraficas,id',
            'tipo_ordine' => 'required|string',
        ];
    
        if ($tipo === 'acquisto') {
            $rules['canale'] = 'required|string|in:vendite indirette,vendite dirette,evento';
        }
    
        $validatedData = $request->validate($rules);

        $validatedData['pagato'] = $request->input('pagato'); 
    
        // ✅ Se non è acquisto, metti un valore valido placeholder per evitare errori di constraint
        if ($tipo !== 'acquisto') {
            $validatedData['canale'] = 'evento';
        }
    
        $ordine = Ordine::create($validatedData);
    
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
            'pagato' => $request->input('pagato'),
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
    
        $ordine->refresh();
    
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
    
        // ✅ Registro vendite (solo se "acquisto" e non "acquisto autore")
        if ($ordine->tipo_ordine === 'acquisto' && $ordine->canale !== 'acquisto autore') {
            $registro = \App\Models\RegistroVendite::firstOrNew([
                'ordine_id' => $ordine->id,
            ], [
                'anagrafica_id' => $ordine->anagrafica_id,
                'periodo' => date('Y'),
            ]);
            
    
            $registro->ordine_id = $ordine->id;
            switch ($ordine->canale) {
                case 'vendite indirette':
                    $registro->canale_vendita = 'Vendite indirette';
                    break;
                case 'vendite dirette':
                    $registro->canale_vendita = 'Vendite dirette';
                    break;
                case 'evento':
                    $registro->canale_vendita = 'Evento';
                    break;
                default:
                    $registro->canale_vendita = 'Altro';
                    break;
            }
            
            $registro->save();
    
            foreach ($ordine->libri as $libro) {
                \App\Models\RegistroVenditeDettaglio::updateOrCreate(
                    [
                        'registro_vendita_id' => $registro->id,
                        'ordine_id' => $ordine->id,
                        'isbn' => $libro->isbn,
                    ],
                    [
                        'data' => $ordine->data,
                        'periodo' => date('Y'),
                        'titolo' => $libro->titolo,
                        'quantita' => $libro->pivot->quantita,
                        'prezzo' => $libro->prezzo,
                        'valore_lordo' => $libro->pivot->valore_vendita_lordo ?? ($libro->prezzo * $libro->pivot->quantita),
                    ]
                );
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
// Commenta la parte in cui usi S3 per il barcode
/*
        // Genera il codice a barre per ogni libro e assicura che il percorso venga passato alla vista
        foreach ($ordine->libri as $libro) {
            // Aggiungi la proprietà 'barcode' per ogni libro
            $libro->barcode = Storage::disk('s3')->url('barcodes/' . $libro->isbn . '.png');
        }
*/
        // Genera il PDF con i dati
        $pdf = Pdf::loadView('ordini.pdf', compact('ordine', 'marchio'));

        // Nome del file PDF
        $nomeCliente = preg_replace('/[\/:*?"<>|]/', '', $ordine->anagrafica->nome ?? '');
        $filename = preg_replace('/[\/\\\\]/', '-', $ordine->codice . ' ' . $nomeCliente) . '.pdf';


        // Restituisci il PDF in download
        return $pdf->download($filename);
    }
    

    public function gestioneLibri($id)
    {
        $ordine = Ordine::with('libri')->findOrFail($id);
        $libri = Libro::with('marchio_editoriale')->select('id', 'titolo', 'isbn', 'prezzo')->get();
    
        // ✅ Recupero quantità disponibili nei magazzini di tipo "magazzino editore"
        $quantitaMagazzinoEditore = \App\Models\Giacenza::whereHas('magazzino', function ($query) {
            $query->where('categoria', 'magazzino editore');
        })->get()->groupBy('libro_id')->map(function ($giacenze) {
            return $giacenze->sum('quantita');
        });
    
        return view('ordini.ordini_libri', compact('ordine', 'libri', 'quantitaMagazzinoEditore'));
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
        $ordine = Ordine::select('id', 'anagrafica_id', 'data', 'canale', 'codice', 'tipo_ordine')->findOrFail($id);
    
        $ordine->update([
            'pagato' => $request->input('pagato'),
            'causale' => $request->input('causale'),
            'condizioni_conto_deposito' => $request->input('condizioni_conto_deposito'),
            'totale_netto_compilato' => $request->input('totale_netto_compilato'),
            'tempi_pagamento' => $request->input('tempi_pagamento'),
            'modalita_pagamento' => $request->input('modalita_pagamento'),
            'specifiche_iva' => $request->input('specifiche_iva'),
            'costo_spedizione' => $request->input('costo_spedizione'),
            'altre_specifiche_iva' => $request->input('altre_specifiche_iva'),
        ]);
    
        $ordine->refresh();
    
        // 🔄 Registro righe attuali dell'ordine prima della modifica
        $righePrecedenti = \App\Models\RegistroVenditeDettaglio::where('ordine_id', $ordine->id)->get()->keyBy('isbn');
        $righeAttuali = [];
    
        if ($request->has('libro_id') && is_array($request->libro_id)) {
            $libriSync = [];
    
            foreach ($request->libro_id as $index => $libro_id) {
                if (!empty($libro_id)) {
                    $quantita = $request->quantita[$index] ?? 0;
                    $libro = \App\Models\Libro::find($libro_id);
                    $prezzo_copertina = $libro ? $libro->prezzo : 0.00;
                    $valore_lordo = $request->valore_vendita_lordo[$index] ?? 0.00;
                    $sconto = $request->sconto[$index] ?? 0.00;
                    $netto = $request->netto_a_pagare[$index] ?? 0.00;
                    $info = $request->info_spedizione[$index] ?? null;
    
                    $libriSync[$libro_id] = [
                        'quantita' => $quantita,
                        'prezzo_copertina' => $prezzo_copertina,
                        'valore_vendita_lordo' => $valore_lordo,
                        'sconto' => $sconto,
                        'netto_a_pagare' => $netto,
                        'info_spedizione' => $info,
                    ];
    
                    // 🔄 SOTTRAZIONE da magazzino editore solo se specificato
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
    
            $ordine->libri()->sync($libriSync);
    
            if ($ordine->tipo_ordine === 'conto deposito') {
                $magazzino = \App\Models\Magazzino::where('anagrafica_id', $ordine->anagrafica_id)->first();
    
                if ($magazzino) {
                    foreach ($request->libro_id as $index => $libro_id) {
                        $libro = \App\Models\Libro::find($libro_id);
                        $quantita = $request->quantita[$index] ?? 0;
    
                        $giacenza = \App\Models\Giacenza::firstOrNew([
                            'libro_id' => $libro_id,
                            'magazzino_id' => $magazzino->id,
                        ]);
    
                        $giacenza->quantita = ($giacenza->quantita ?? 0) + $quantita;
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
    
        if (
            $ordine->tipo_ordine === 'acquisto' &&
            !in_array($ordine->canale, ['omaggio', 'acquisto autore'])
        ) {
            $registro = \App\Models\RegistroVendite::firstOrNew([
                'ordine_id' => $ordine->id,
            ], [
                'anagrafica_id' => $ordine->anagrafica_id,
                'periodo' => date('Y'),
            ]);
    
            $registro->ordine_id = $ordine->id;
    
            switch (strtolower(trim($ordine->canale))) {
                case 'vendite indirette':
                    $registro->canale_vendita = 'Vendite indirette';
                    break;
                case 'vendite dirette':
                    $registro->canale_vendita = 'Vendite dirette';
                    break;
                case 'evento':
                    $registro->canale_vendita = 'Evento';
                    break;
                default:
                    $registro->canale_vendita = 'Altro';
                    break;
            }
    
            $registro->save();
    
            foreach ($request->libro_id as $index => $libro_id) {
                $libro = \App\Models\Libro::find($libro_id);
                $isbn = $libro->isbn;
                $quantita = $request->quantita[$index] ?? 0;
                $valore_lordo = $request->valore_vendita_lordo[$index] ?? 0.00;
    
                $righeAttuali[$isbn] = true;
    
                \App\Models\RegistroVenditeDettaglio::updateOrCreate(
                    [
                        'registro_vendita_id' => $registro->id,
                        'ordine_id' => $ordine->id,
                        'isbn' => $isbn,
                    ],
                    [
                        'data' => $ordine->data,
                        'periodo' => date('Y'),
                        'titolo' => $libro->titolo,
                        'quantita' => $quantita,
                        'prezzo' => $libro->prezzo,
                        'valore_lordo' => $valore_lordo,
                    ]
                );
            }
    
            foreach ($righePrecedenti as $isbn => $riga) {
                if (!isset($righeAttuali[$isbn])) {
                    $riga->delete();
                }
            }
        }
    
        return redirect()->route('ordini.gestione_libri', $id)->with('success', 'Libri salvati con successo.');
    }
    

    
    
    
    
    public function search(Request $request)
    {
        $query = $request->input('term');
    
        $libri = Libro::where('titolo', 'LIKE', "%{$query}%")
            ->orWhere('isbn', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();
    
        return response()->json($libri->map(function ($libro) {
            return [
                'id' => $libro->id,
                'label' => $libro->titolo,
                'value' => $libro->titolo,
                'isbn' => $libro->isbn,
                'prezzo' => $libro->prezzo_copertina,
            ];
        }));
    }
    
    

}
