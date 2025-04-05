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
        
        // Aggiungi una validazione per verificare che i campi siano presenti
        $request->validate([
            'anagrafica_id' => 'required',
            'canale_vendita' => 'required',
        ]);
    
        // Crea una nuova istanza del modello RegistroVendite
        $registroVendita = new RegistroVendite();
        $registroVendita->anagrafica_id = $request->input('anagrafica_id');
        $registroVendita->canale_vendita = $request->input('canale_vendita');
        $registroVendita->save();
    
        // Salva le righe solo se ci sono dati validi
        if ($request->has('data') && is_array($request->data)) {
            foreach ($request->data as $index => $data) {
                // Verifica se il periodo è stato inserito, altrimenti assegna un valore di default
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
    
        return redirect()->route('registro-vendite.index')->with('success', 'Registro Vendite creato con successo!');
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
    
        $dettagli = $query->paginate(100);

        return view('registro-vendite.gestione', compact('registroVendita', 'dettagli'));
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

    public function __construct()
{
    Paginator::useBootstrap();
}

}
