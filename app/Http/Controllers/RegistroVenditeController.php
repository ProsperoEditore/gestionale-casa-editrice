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
    
        if ($request->has('data') && is_array($request->data)) {
            foreach ($request->data as $index => $data) {
                $periodo = !empty($request->periodo[$index]) ? $request->periodo[$index] : 'N/D';
    
                // Se il dettaglio ha ID, aggiorna, altrimenti crea
                if (!empty($request->id[$index])) {
                    $dettaglio = RegistroVenditeDettaglio::find($request->id[$index]);
                    if ($dettaglio) {
                        $dettaglio->update([
                            'data' => $data,
                            'periodo' => $periodo,
                            'isbn' => $request->isbn[$index] ?? null,
                            'titolo' => $request->titolo[$index] ?? null,
                            'quantita' => $request->quantita[$index] ?? 0,
                            'prezzo' => $request->prezzo[$index] ?? 0.00,
                            'valore_lordo' => ($request->quantita[$index] ?? 0) * ($request->prezzo[$index] ?? 0.00),
                        ]);
                    }
                } else {
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

    
                // ✅ AGGIORNA GIACENZA se esiste il magazzino per l'anagrafica
                $magazzino = \App\Models\Magazzino::where('anagrafica_id', $registroVendita->anagrafica_id)->first();
    
                if ($magazzino) {
                    $libro = \App\Models\Libro::where('isbn', trim($request->isbn[$index]))->first();
                    if ($libro) {
                        $giacenza = \App\Models\Giacenza::where('magazzino_id', $magazzino->id)
                            ->where('libro_id', $libro->id)
                            ->first();
    
                        if ($giacenza) {
                            $quantitaDaSottrarre = $request->quantita[$index] ?? 0;
                            $giacenza->quantita = max(0, $giacenza->quantita - $quantitaDaSottrarre);
                            $giacenza->note = 'Aggiornato con rendiconto del ' . now()->format('d.m.Y');
                            $giacenza->data_ultimo_aggiornamento = now();
                            $giacenza->save();
                        }
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
        $totaleValoreLordo = $query->sum('valore_lordo');
        $dettagli = $query->orderBy('data', 'desc')->paginate(100)->appends($request->query());

        
        $libri = Libro::with('marchio_editoriale')->get();
    
        return view('registro-vendite.gestione', compact('registroVendita', 'dettagli', 'libri', 'totaleValoreLordo'));
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
    $registro = RegistroVendite::with(['anagrafica', 'dettagli.libro'])->findOrFail($id);

    $dataDa = $request->input('data_da');
    $dataA = $request->input('data_a');

    $dettagliFiltrati = $registro->dettagli->filter(function ($d) use ($dataDa, $dataA) {
        if ($dataDa && $d->data < $dataDa) return false;
        if ($dataA && $d->data > $dataA) return false;
        return true;
    });

    return Pdf::loadView('registro-vendite.pdf', [
        'registro' => $registro,
        'dettagli' => $dettagliFiltrati,
        'filtro_date' => [
            'da' => $dataDa,
            'a' => $dataA,
        ]
    ])->download('registro_vendite_' . $registro->id . '.pdf');
}



}
