<?php

namespace App\Http\Controllers;

use App\Models\Autore;
use App\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AutoreController extends Controller
{
    public function index()
    {
        $autori = Autore::with('libri')->orderBy('cognome')->get();
        return view('autori.index', compact('autori'));
    }

    public function create()
    {
        $libri = Libro::all();
        return view('autori.form', compact('libri'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'nullable|string',
            'cognome' => 'nullable|string',
            'pseudonimo' => 'nullable|string',
            'denominazione' => 'nullable|string',
            'codice_fiscale' => 'nullable|string',
            'data_nascita' => 'nullable|date',
            'luogo_nascita' => 'nullable|string',
            'iban' => 'nullable|string',
            'indirizzo' => 'nullable|string',
            'biografia' => 'nullable|string',
            'foto' => 'nullable|image',
            'libri' => 'nullable|array',
            'libri.*' => 'exists:libri,id',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('autori', 'public');
        }

        $autore = Autore::create($data);
        $autore->libri()->sync($request->input('libri', []));

        return redirect()->route('autori.index')->with('success', 'Autore creato con successo.');
    }

    public function edit(Autore $autore)
    {
        $libri = Libro::all();
        return view('autori.form', compact('autore', 'libri'));
    }

    public function update(Request $request, Autore $autore)
    {
        $data = $request->validate([
            'nome' => 'nullable|string',
            'cognome' => 'nullable|string',
            'pseudonimo' => 'nullable|string',
            'denominazione' => 'nullable|string',
            'codice_fiscale' => 'nullable|string',
            'data_nascita' => 'nullable|date',
            'luogo_nascita' => 'nullable|string',
            'iban' => 'nullable|string',
            'indirizzo' => 'nullable|string',
            'biografia' => 'nullable|string',
            'foto' => 'nullable|image',
            'libri' => 'nullable|array',
            'libri.*' => 'exists:libri,id',
        ]);

        if ($request->hasFile('foto')) {
            if ($autore->foto) Storage::disk('public')->delete($autore->foto);
            $data['foto'] = $request->file('foto')->store('autori', 'public');
        }

        $autore->update($data);
        $autore->libri()->sync($request->input('libri', []));

        return redirect()->route('autori.index')->with('success', 'Autore aggiornato con successo.');
    }

    public function destroy(Autore $autore)
    {
        if ($autore->foto) Storage::disk('public')->delete($autore->foto);
        $autore->libri()->detach();
        $autore->delete();

        return redirect()->route('autori.index')->with('success', 'Autore eliminato con successo.');
    }

    public function autocompleteLibro(Request $request)
{
    $term = strtolower($request->input('term'));

    $libri = \App\Models\Libro::whereRaw('LOWER(titolo) LIKE ?', ["%{$term}%"])
        ->orWhere('isbn', 'like', "%{$term}%")
        ->limit(10)
        ->get();

    return response()->json($libri->map(function ($l) {
        return [
            'label' => "{$l->titolo} ({$l->isbn})",
            'value' => $l->id,
        ];
    }));
}

}
