<?php

namespace App\Http\Controllers;

use App\Models\Scarico;
use App\Models\Ordine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ScaricoController extends Controller
{
    public function index(Request $request)
    {
        $query = Scarico::query();
    
        // Filtro di ricerca per destinatario
        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');
            $query->where('destinatario_nome', 'like', '%' . $searchTerm . '%');
        }
    
        // Recupera i dati filtrati e paginati
        $scarichi = $query->latest()->paginate(50);
    
        return view('scarichi.index', compact('scarichi'));
    }
    

    public function updateInfoSpedizione(Request $request, $id)
    {
        $request->validate([
            'info_spedizione' => 'nullable|string|max:500',
            'stato' => 'nullable|string|in:In attesa,Spedito',
        ]);
    
        $scarico = Scarico::findOrFail($id);
    
        $scarico->info_spedizione = $request->info_spedizione;
    
        if ($request->filled('stato')) {
            $scarico->stato = $request->stato;
            $scarico->data_stato_info = now();
        }
    
        $scarico->save();
    
        return redirect()->route('scarichi.index')->with('success', 'Spedizione aggiornata.');
    }
    
    
    
    


    public function create()
    {
        $ordini = \App\Models\Ordine::with(['anagrafica'])->get();
        return view('scarichi.create', compact('ordini'));
    }

    public function store(Request $request)
    {
            $request->validate([
                'ordine_id' => 'nullable|exists:ordines,id',
                'altro_ordine' => 'nullable|string|max:255',
                'destinatario_nome' => 'required|string|max:255',
                'info_spedizione' => 'nullable|string|max:255',
            ]);

            // Se è presente ordine_id, anagrafica_id è obbligatoria
            if ($request->filled('ordine_id')) {
                $request->validate([
                    'anagrafica_id' => 'required|exists:anagraficas,id',
                ]);
            }

    
        Scarico::create([
            'ordine_id' => $request->ordine_id,
            'altro_ordine' => $request->altro_ordine,
            'anagrafica_id' => $request->anagrafica_id,
            'destinatario_nome' => $request->destinatario_nome,
            'info_spedizione' => $request->info_spedizione,
        ]);
    
        return redirect()->route('scarichi.index')->with('success', 'Spedizione creata con successo.');
    }
    
    

        public function edit(Scarico $scarichi)
        {
            $ordini = Ordine::with('anagrafica')->get();
            return view('scarichi.edit', ['scarico' => $scarichi, 'ordini' => $ordini]);
        }

    
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'ordine_id' => 'nullable|exists:ordines,id',
            'altro_ordine' => 'nullable|string|max:255',
            'destinatario_nome' => 'required|string|max:255',
            'info_spedizione' => 'nullable|string|max:255',
        ]);

        // Se è presente ordine_id, allora anagrafica_id è obbligatoria
        if ($request->filled('ordine_id')) {
            $request->validate([
                'anagrafica_id' => 'required|exists:anagraficas,id',
            ]);
        }


        $scarico = Scarico::findOrFail($id);

        $scarico->ordine_id = $request->ordine_id;
        $scarico->altro_ordine = $request->altro_ordine;
        $scarico->anagrafica_id = $request->anagrafica_id;
        $scarico->destinatario_nome = $request->destinatario_nome;
        $scarico->info_spedizione = $request->info_spedizione;

        $scarico->save();

        return redirect()->route('scarichi.index')->with('success', 'Scarico aggiornato con successo.');
    }


    public function destroy($id)
    {
        Scarico::findOrFail($id)->delete();
        return redirect()->route('scarichi.index')->with('success', 'Scarico eliminato con successo.');
    }

   
    public function autocompleteOrdini(Request $request)
    {
        $query = $request->get('query');
    
        $ordini = Ordine::where('codice', 'like', "%{$query}%")
            ->orWhereHas('anagrafica', function ($q) use ($query) {
                $q->where('nome', 'like', "%{$query}%");
            })
            ->with('anagrafica')
            ->limit(10)
            ->get();
    
        return response()->json($ordini->map(function ($ordine) {
            return [
                'id' => $ordine->id,
                'codice' => $ordine->codice,
                'nome_cliente' => optional($ordine->anagrafica)->nome ?? '⚠️ Nessun nome',
                'anagrafica_id' => optional($ordine->anagrafica)->id ?? null,
            ];
        }));
    }

    public function updateStato(Request $request, $id)
{
    $scarico = Scarico::findOrFail($id);
    $scarico->stato = $request->input('stato');
    $scarico->data_stato_info = now();
    $scarico->save();

    return response()->json(['success' => true]);
}

    
    

}