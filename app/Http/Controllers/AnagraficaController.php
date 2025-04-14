<?php

namespace App\Http\Controllers;

use App\Models\Anagrafica;
use Illuminate\Http\Request;

class AnagraficaController extends Controller
{
    public function index(Request $request)
    {
        $query = Anagrafica::query();
    
        if ($request->filled('search')) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $items = $query->latest()->paginate(100);
        return view('anagrafiche.index', compact('items'));
    }

    public function create()
    {
        return view('anagrafiche.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria' => 'required|string',
            'nome' => 'required|string',
            'indirizzo_fatturazione' => 'nullable|string',
            'indirizzo_spedizione' => 'nullable|string',
            'partita_iva' => 'nullable|string',
            'codice_fiscale' => 'nullable|string',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string',
            'pec' => 'nullable|string',
            'codice_univoco' => 'nullable|string',
        ]);
        

        Anagrafica::create($validated);

        return redirect()->route('anagrafiche.index')->with('success', 'Anagrafica aggiunta con successo.');
    }

    public function edit($id)
    {
        $item = Anagrafica::findOrFail($id);
        return view('anagrafiche.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'categoria' => 'required|string',
            'nome' => 'required|string',
            'indirizzo_fatturazione' => 'nullable|string',
            'indirizzo_spedizione' => 'nullable|string',
            'partita_iva' => 'nullable|string',
            'codice_fiscale' => 'nullable|string',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string',
            'pec' => 'nullable|string',
            'codice_univoco' => 'nullable|string',
        ]);
        

        $anagrafica = Anagrafica::findOrFail($id);

        $anagrafica->update($validated);

        return redirect()->route('anagrafiche.index')->with('success', 'Anagrafica aggiornata con successo.');
    }

    public function destroy($id)
    {
        Anagrafica::findOrFail($id)->delete();
        return redirect()->route('anagrafiche.index')->with('success', 'Anagrafica eliminata con successo.');
    }

    public function autocomplete(Request $request)
{
    $query = $request->input('term');

    $anagrafiche = Anagrafica::where('nome', 'LIKE', "%{$query}%")
        ->limit(10)
        ->get(['id', 'nome']);

    return response()->json($anagrafiche);
}

}