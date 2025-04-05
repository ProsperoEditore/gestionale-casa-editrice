<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contratto;

class ContrattoController extends Controller
{
    public function index()
    {
        $contratti = Contratto::all();
        return view('contratti.index', compact('contratti'));
    }

    public function create()
    {
        $contratto = new Contratto(); 
        return view('contratti.create', compact('contratto'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_contratto' => 'required|string|max:255',
            'sconto_proprio_libro' => 'nullable|numeric',
            'sconto_altri_libri' => 'nullable|numeric',
            'royalties_vendite_indirette' => 'nullable|numeric',
            'royalties_vendite_indirette_soglia_1' => 'required|integer',
            'royalties_vendite_indirette_percentuale_1' => 'required|numeric',
            'royalties_vendite_indirette_soglia_2' => 'required|integer',
            'royalties_vendite_indirette_percentuale_2' => 'required|numeric',
            'royalties_vendite_indirette_soglia_3' => 'required|integer',
            'royalties_vendite_indirette_percentuale_3' => 'required|numeric',
            'royalties_vendite_dirette' => 'nullable|numeric',
            'royalties_eventi' => 'nullable|numeric',
        ]);

        Contratto::create($request->all());

        return redirect()->route('contratti.index')->with('success', 'Contratto creato con successo.');
    }

    public function edit(Contratto $contratto)
    {
        return view('contratti.edit', compact('contratto'));
    }

    public function update(Request $request, Contratto $contratto)
    {
        $request->validate([
            'nome_contratto' => 'required|string|max:255',
            'sconto_proprio_libro' => 'nullable|numeric',
            'sconto_altri_libri' => 'nullable|numeric',
            'royalties_vendite_indirette' => 'nullable|numeric',
            'royalties_vendite_indirette_soglia_1' => 'required|integer',
            'royalties_vendite_indirette_percentuale_1' => 'required|numeric',
            'royalties_vendite_indirette_soglia_2' => 'required|integer',
            'royalties_vendite_indirette_percentuale_2' => 'required|numeric',
            'royalties_vendite_indirette_soglia_3' => 'required|integer',
            'royalties_vendite_indirette_percentuale_3' => 'required|numeric',
            'royalties_vendite_dirette' => 'nullable|numeric',
            'royalties_eventi' => 'nullable|numeric',
        ]);

        $contratto->update($request->all());

        return redirect()->route('contratti.index')->with('success', 'Contratto aggiornato con successo.');
    }

    public function destroy(Contratto $contratto)
    {
        $contratto->delete();
        return redirect()->route('contratti.index')->with('success', 'Contratto eliminato con successo.');
    }
}
