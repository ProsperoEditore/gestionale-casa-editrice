<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Magazzino;
use App\Models\Anagrafica;

class MagazzinoController extends Controller
{
    public function index()
    {
        $magazzini = Magazzino::with('anagrafica')->get();
        return view('magazzini.index', compact('magazzini'));
    }

    public function create()
    {
        $anagrafiche = Anagrafica::all();
        $categorie = ['Distributore', 'Libreria', 'Editore', 'Altro']; // Categorie definite
        return view('magazzini.create', compact('anagrafiche', 'categorie'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anagrafica_id' => 'required|exists:anagraficas,id',
        ]);
    
        Magazzino::create([
            'anagrafica_id' => $request->anagrafica_id,
            'prossima_scadenza' => $request->prossima_scadenza,
        ]);
    
        return redirect()->route('magazzini.index')->with('success', 'Magazzino creato con successo.');
    }
      

    public function update(Request $request, Magazzino $magazzino)
    {
        $request->validate([
            'categoria' => 'required|string',
            'nome' => 'required|string|max:255',
            'anagrafica_id' => 'required|exists:anagraficas,id',
        ]);

        $magazzino->update($request->all());

        return redirect()->route('magazzini.index')->with('success', 'Magazzino aggiornato con successo.');
    }

    public function updateScadenza(Request $request, $id)
    {
    $request->validate([
        'prossima_scadenza' => 'required|date'
    ]);

    $magazzino = Magazzino::findOrFail($id);
    $magazzino->prossima_scadenza = $request->prossima_scadenza;
    $magazzino->save();

    return response()->json(['success' => true]);
    }


    public function destroy($id)
    {
        $magazzino = Magazzino::findOrFail($id);
    
        // Elimina le giacenze associate al magazzino
        $magazzino->giacenze()->delete();
    
        // Elimina il magazzino
        $magazzino->delete();
    
        return redirect()->route('magazzini.index')->with('success', 'Magazzino e giacenze eliminate con successo.');
    }
    
}
