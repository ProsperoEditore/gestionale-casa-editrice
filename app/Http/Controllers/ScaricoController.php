<?php

namespace App\Http\Controllers;

use App\Models\Scarico;
use App\Models\Ordine;
use Illuminate\Http\Request;

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
        $scarichi = $query->latest()->paginate(100);
    
        return view('scarichi.index', compact('scarichi'));
    }
    

    public function updateInfoSpedizione(Request $request, Scarico $scarico)
    {
        $request->validate([
            'info_spedizione' => 'nullable|string|max:255',
        ]);
    
        $scarico->update([
            'info_spedizione' => $request->info_spedizione,
        ]);
    
        return redirect()->back()->with('success', 'Info consegna aggiornata.');
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
            'anagrafica_id' => 'nullable|exists:anagraficas,id',
            'destinatario_nome' => 'nullable|string|max:255',
            'info_spedizione' => 'nullable|string|max:255',
        ]);
    
        Scarico::create([
            'ordine_id' => $request->ordine_id,
            'altro_ordine' => $request->altro_ordine,
            'anagrafica_id' => $request->anagrafica_id,
            'destinatario_nome' => $request->destinatario_nome,
            'info_spedizione' => $request->info_spedizione,
        ]);
    
        return redirect()->route('scarichi.index')->with('success', 'Spedizione creata con successo.');
    }
    
    

    public function edit(Scarico $scarico)
    {
        $ordini = Ordine::with('anagrafica')->get(); // aggiungi questo
        return view('scarichi.edit', compact('scarico', 'ordini'));
    }
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'anagrafica_id' => 'required|exists:anagraficas,id',
            'ordine_id' => 'required|exists:ordines,id',
        ]);

        Scarico::findOrFail($id)->update($request->all());

        return redirect()->route('scarichi.index')->with('success', 'Scarico aggiornato con successo.');
    }

    public function destroy($id)
    {
        Scarico::findOrFail($id)->delete();
        return redirect()->route('scarichi.index')->with('success', 'Scarico eliminato con successo.');
    }
}