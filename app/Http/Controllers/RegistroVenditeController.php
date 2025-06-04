<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\RegistroVendite;
use App\Models\RegistroVenditeDettaglio;
use App\Imports\RegistroVenditeImport;
use App\Models\Anagrafica;
use App\Models\Libro;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\Paginator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class RegistroVenditeController extends Controller
{
    public function index(Request $request)
    {
        $query = RegistroVendite::with('anagrafica');
    
        if ($request->filled('search')) {
            $search = strtolower(str_replace(' ', '', $request->search));
            $query->whereHas('anagrafica', function ($q) use ($search) {
                $q->whereRaw("LOWER(REPLACE(nome, ' ', '')) LIKE ?", ["%{$search}%"]);
            });
        }
        
    
        $items = $query->orderBy('created_at', 'desc')->paginate(30)->appends($request->query());
    
        return view('registro-vendite.index', compact('items'));
    }
    

    public function create()
    {
        $anagrafiche = Anagrafica::all();
        return view('registro-vendite.create', compact('anagrafiche'));
    }

    public function store(Request $request)
    {
        Log::info('Dati ricevuti:', $request->all());
    
        $request->validate([
            'anagrafica_id' => 'required',
            'canale_vendita' => 'required',
        ]);
    
        // 1. Crea il registro
        $registroVendita = new RegistroVendite();
        $registroVendita->anagrafica_id = $request->input('anagrafica_id');
        $registroVendita->canale_vendita = $request->input('canale_vendita');
        $registroVendita->save();
    
        // 2. Crea i dettagli
        if ($request->has('data') && is_array($request->data)) {
            foreach ($request->data as $index => $data) {
                $periodo = !empty($request->periodo[$index]) ? $request->periodo[$index] : 'N/D';
    
                RegistroVenditeDettaglio::create([
                    'registro_vendita_id' => $registroVendita->id,
                    'data' => $data,
                    'periodo' => $periodo,
                    'isbn' => $request->isbn[$index] ?? null,
                    'titolo' => $request->titolo[$index] ?? null,
                    'quantita' => $request->quantita[$index] ?? 0,
                    'prezzo' => $request->prezzo[$index] ?? 0.00,
                    'valore_lordo' => ($request->quantita[$index] ?? 0) * ($request->prezzo[$index] ?? 0.00),
                ]);
            }
        }
    
        // 3. AGGIORNA GIACENZE (dopo aver salvato tutto)
        $magazzino = \App\Models\Magazzino::where('anagrafica_id', $registroVendita->anagrafica_id)->first();
    
        if ($magazzino) {
            $dettagli = RegistroVenditeDettaglio::where('registro_vendita_id', $registroVendita->id)->get();
    
            foreach ($dettagli as $dettaglio) {
                $libro = \App\Models\Libro::where('isbn', trim($dettaglio->isbn))->first();
                if (!$libro) {
                    Log::warning('Libro non trovato per ISBN: ' . $dettaglio->isbn);
                    continue;
                }
            
                $giacenza = \App\Models\Giacenza::where('magazzino_id', $magazzino->id)
                            ->where('libro_id', $libro->id)
                            ->first();
            
                if ($giacenza) {
                    $giacenza->quantita = max(0, $giacenza->quantita - $dettaglio->quantita);
                    $giacenza->note = 'Aggiornato con rendiconto del ' . now()->format('d.m.Y');
                    $giacenza->data_ultimo_aggiornamento = now();
                    $giacenza->save();
                } else {
                    Log::info('Nessuna giacenza trovata per libro ID ' . $libro->id . ' nel magazzino ID ' . $magazzino->id);
                }
            }
        }
            
    
        return redirect()->route('registro-vendite.index')->with('success', 'Registro Vendite creato con successo!');
    }
    
    
    
public function salvaDettagli(Request $request, $id)
{
    $registroVendita = RegistroVendite::findOrFail($id);
    $righe = $request->input('righe', []);

    foreach ($righe as $riga) {
        // Verifica che ci siano dati sufficienti
        if (empty($riga['isbn']) || empty($riga['quantita'])) {
            continue; // salta righe vuote o non valide
        }

        $periodo = !empty($riga['periodo']) ? $riga['periodo'] : 'N/D';
        $quantita = (int) ($riga['quantita'] ?? 0);
        $prezzo = (float) ($riga['prezzo'] ?? 0.00);
        $valoreLordo = $quantita * $prezzo;

        if (!empty($riga['id'])) {
            // Update
            $dettaglio = RegistroVenditeDettaglio::find($riga['id']);
            if ($dettaglio) {
                $dettaglio->update([
                    'data' => $riga['data'] ?? null,
                    'periodo' => $periodo,
                    'isbn' => $riga['isbn'],
                    'titolo' => $riga['titolo'] ?? null,
                    'quantita' => $quantita,
                    'prezzo' => $prezzo,
                    'valore_lordo' => $valoreLordo,
                ]);
            }
        } else {
            // Create
            RegistroVenditeDettaglio::create([
                'registro_vendita_id' => $registroVendita->id,
                'data' => $riga['data'] ?? null,
                'periodo' => $periodo,
                'isbn' => $riga['isbn'],
                'titolo' => $riga['titolo'] ?? null,
                'quantita' => $quantita,
                'prezzo' => $prezzo,
                'valore_lordo' => $valoreLordo,
            ]);
        }

        // ✅ AGGIORNA GIACENZA se esiste il magazzino per l'anagrafica
        $magazzino = \App\Models\Magazzino::where('anagrafica_id', $registroVendita->anagrafica_id)->first();

        if ($magazzino && !empty($riga['isbn'])) {
            $libro = \App\Models\Libro::where('isbn', trim($riga['isbn']))->first();

            if ($libro) {
                $giacenza = \App\Models\Giacenza::where('magazzino_id', $magazzino->id)
                    ->where('libro_id', $libro->id)
                    ->first();

                if ($giacenza) {
                    $giacenza->quantita = max(0, $giacenza->quantita - $quantita);
                    $giacenza->note = 'Aggiornato con rendiconto del ' . now()->format('d.m.Y');
                    $giacenza->data_ultimo_aggiornamento = now();
                    $giacenza->save();
                }
            }
        }
    }

    return redirect()->route('registro-vendite.gestione', ['id' => $id])
        ->with('success', 'Dettagli aggiornati con successo!');
}

     

    public function gestione($id, Request $request)
    {
        $registroVendita = RegistroVendite::with('dettagli')->findOrFail($id);
        $query = RegistroVenditeDettaglio::where('registro_vendita_id', $id);
    
        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');
            $query->where('titolo', 'like', '%' . $searchTerm . '%');
        }

    
        // 👇 Qui aggiungi orderBy PRIMA di paginate
        $totaleValoreLordo = RegistroVenditeDettaglio::where('registro_vendita_id', $id)->sum('valore_lordo');
        $totaleQuantita = RegistroVenditeDettaglio::where('registro_vendita_id', $id)->sum('quantita');

        $dettagli = $query->orderBy('data', 'desc')->paginate(100)->appends($request->query());

        
        $libri = Libro::with('marchio_editoriale')->get();
    
        return view('registro-vendite.gestione', compact('registroVendita', 'dettagli', 'libri', 'totaleValoreLordo', 'totaleQuantita'));
    }

    


    public function importExcel(Request $request, $id)
    {
        $registro = RegistroVendite::findOrFail($id);
    
        if ($request->hasFile('file')) {
            $file = $request->file('file');
    
            // ⚠️ NON cancellare subito la sessione
            // Session::forget(['import_errori', 'righe_ambigue']);
    
            Excel::import(new RegistroVenditeImport($registro), $file);
    
            // ✅ Verifica e ritrasmetti righe ambigue
                if (session()->has('righe_ambigue') && !empty(session('righe_ambigue'))) {
                    if (session()->has('import_errori')) {
                        session(['import_errori_persistenti' => session('import_errori')]);
                    }
                    session()->reflash();
                    return redirect()->route('registro-vendite.gestione', $registro->id);
                }



    
            if (session()->has('import_errori')) {
                return redirect()->back()->with([
                    'success' => 'Vendite importate, alcune righe sono state scartate.',
                    'import_errori' => session('import_errori'),
                ]);
            }
    
            return redirect()->back()->with('success', 'Vendite importate con successo!');
        }
    
        return redirect()->back()->with('error', 'Errore nell\'importazione del file.');
    }
    

    
    public function risolviConflitti(Request $request, $id)
    {
        $registro = RegistroVendite::findOrFail($id);
        $righe = $request->input('righe', []);
    
            foreach ($righe as $riga) {
                if (empty($riga['isbn']) || $riga['isbn'] === '__SKIP__') {
                    continue; // ❌ Salta righe ignorate o senza selezione
                }

                $libro = Libro::where('isbn', $riga['isbn'])->first();
                if (!$libro) {
                    continue; // ⚠️ Salta righe con ISBN non valido
                }

                RegistroVenditeDettaglio::create([
                    'registro_vendita_id' => $registro->id,
                    'data' => $riga['data'],
                    'periodo' => $riga['periodo'] ?? 'N/D',
                    'isbn' => $libro->isbn,
                    'titolo' => $libro->titolo,
                    'quantita' => $riga['quantita'],
                    'prezzo' => $libro->prezzo,
                    'valore_lordo' => $riga['quantita'] * $libro->prezzo,
                ]);
            }

    
        return redirect()->route('registro-vendite.gestione', ['id' => $id])
            ->with('success', 'Righe confermate salvate correttamente.');
    }
     
    

    public function edit($id)
    {
        $item = RegistroVendite::findOrFail($id);
        $anagrafiche = Anagrafica::all(); 
        return view('registro-vendite.edit', compact('item', 'anagrafiche'));
    }
    

    public function update(Request $request, $id)
    {
        $registro = RegistroVendite::findOrFail($id);

        $registro->update([
            'anagrafica_id' => $request->anagrafica_id,
            'canale_vendita' => $request->canale_vendita,
        ]);

        return redirect()->route('registro-vendite.index');
    }

    public function destroy($id)
    {
        RegistroVendite::destroy($id);
        return redirect()->route('registro-vendite.index');
    }

    public function destroyDettaglio($id)
    {
    RegistroVenditeDettaglio::destroy($id);
    return response()->json(['success' => true]);
    }


    public function __construct()
    {
    Paginator::useBootstrap();
    }

    public function updateCanale(Request $request, $id)
    {
    $request->validate([
        'canale_vendita' => 'required|in:Vendite dirette,Vendite indirette,Eventi',
    ]);

    $registro = RegistroVendite::findOrFail($id);
    $registro->canale_vendita = $request->canale_vendita;
    $registro->save();

    return redirect()->back()->with('success', 'Canale aggiornato con successo.');
    }


public function stampa($id, Request $request)
{
    $registro = RegistroVendite::with('anagrafica')->findOrFail($id);

    $dataDa = $request->input('data_da');
    $dataA = $request->input('data_a');

    $dettagli_raw = RegistroVenditeDettaglio::with(['libro'])
        ->where('registro_vendita_id', $id)
        ->when($dataDa, fn($query) => $query->whereDate('data', '>=', $dataDa))
        ->when($dataA, fn($query) => $query->whereDate('data', '<=', $dataA))
        ->get()
        ->sortBy(fn($item) => $item->data)
        ->values();

    // Totali parziali
    $totali = [
        'quantita' => $dettagli_raw->sum('quantita'),
        'valore_lordo' => $dettagli_raw->sum('valore_lordo'),
    ];

    // Nome file
    $inizio = $dataDa ? Carbon::parse($dataDa)->format('d-m-Y') : 'inizio';
    $fine = $dataA ? Carbon::parse($dataA)->format('d-m-Y') : 'oggi';
    $nomeFile = "Registro_{$registro->anagrafica->nome}_da_{$inizio}_a_{$fine}.pdf";

    return Pdf::loadView('registro-vendite.pdf', [
        'registro' => $registro,
        'dettagli' => $dettagli_raw,
        'totali' => $totali,
        'filtro_date' => ['da' => $dataDa, 'a' => $dataA],
    ])
    ->setPaper('a4', 'landscape')
    ->download($nomeFile);
}


}
