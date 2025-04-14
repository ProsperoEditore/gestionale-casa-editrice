<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\RegistroVendite;
use App\Models\RegistroVenditeDettaglio;
use App\Imports\RegistroVenditeImport;
use App\Models\Anagrafica;
use App\Models\Libro;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\Paginator;

class RegistroVenditeController extends Controller
{
    public function index()
    {
        $items = RegistroVendite::with('anagrafica')->get();
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
    
                RegistroVenditeDettaglio::updateOrCreate(
                    [
                        'registro_vendita_id' => $registroVendita->id,
                        'isbn' => $request->isbn[$index],
                        'data' => $data
                    ],
                    [
                        'periodo' => $periodo,
                        'titolo' => $request->titolo[$index] ?? null,
                        'quantita' => $request->quantita[$index] ?? 0,
                        'prezzo' => $request->prezzo[$index] ?? 0.00,
                        'valore_lordo' => ($request->quantita[$index] ?? 0) * ($request->prezzo[$index] ?? 0.00),
                    ]
                );
    
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
        $query->whereHas('libro', function($q) use ($searchTerm) {
            $q->where('titolo', 'like', '%' . $searchTerm . '%');
        });
    }

    $dettagli = $query->paginate(100)->appends($request->query());
    $libri = Libro::with('marchio_editoriale')->get();

    return view('registro-vendite.gestione', compact('registroVendita', 'dettagli', 'libri'));
    }

   

    public function importExcel(Request $request, $id)
    {
        $registro = RegistroVendite::findOrFail($id);
    
        if ($request->hasFile('file')) {
            // Carica il file Excel
            $file = $request->file('file');
    
            // Importa i dati
            Excel::import(new RegistroVenditeImport($registro), $file);
    
            return redirect()->back()->with('success', 'Vendite importate con successo!');
        }
    
        return redirect()->back()->with('error', 'Errore nell\'importazione del file.');
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


}
