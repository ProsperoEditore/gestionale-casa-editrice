<?php

namespace App\Http\Controllers;

use App\Models\SchedaLibro;
use App\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS1DFacade;
use PDF;

class SchedaLibroController extends Controller
{
    public function index()
    {
        $schede = SchedaLibro::with('libro')->get();
        return view('schede_libro.index', compact('schede'));
    }

    public function create()
    {
        // Prepara libri per autocomplete con titolo + ISBN
        $libri = \App\Models\Libro::select('id', 'titolo', 'isbn')->get();
        return view('schede_libro.create', compact('libri'));
    }

    public function show($id)
    {
    // Se non ti serve, puoi semplicemente fare un redirect o un messaggio
    return redirect()->route('schede-libro.index');
    }

    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libro_id' => 'required|exists:libri,id',
            'descrizione_breve' => 'nullable|string',
            'sinossi' => 'nullable|string',
            'strillo' => 'nullable|string',
            'extra' => 'nullable|string',
            'biografia_autore' => 'nullable|string',
            'formato' => 'nullable|string',
            'numero_pagine' => 'nullable|integer',
            'copertina' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'copertina_stesa' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($request->hasFile('copertina')) {
            $validated['copertina_path'] = $request->file('copertina')->store('schede_libro', 'public');
        }

        if ($request->hasFile('copertina_stesa')) {
            $validated['copertina_stesa_path'] = $request->file('copertina_stesa')->store('schede_libro', 'public');
        }

        SchedaLibro::create($validated);

        return redirect()->route('schede-libro.index')->with('success', 'Scheda creata con successo!');
    }



    public function pdf($id)
    {
    $scheda = SchedaLibro::with('libro')->findOrFail($id);

    $pdf = PDF::loadView('schede_libro.pdf', compact('scheda'))->setPaper('a4', 'portrait');

    return $pdf->download('Scheda_' . Str::slug($scheda->libro->titolo) . '.pdf');
    }



    public function edit($id)
    {
    $scheda = SchedaLibro::with('libro')->findOrFail($id);
    $libri = \App\Models\Libro::select('id', 'titolo', 'isbn')->get();

    return view('schede_libro.edit', compact('scheda', 'libri'));
    }



    public function update(Request $request, $id)
    {
    $scheda = SchedaLibro::findOrFail($id);

    $validated = $request->validate([
        'libro_id' => 'required|exists:libri,id',
        'descrizione_breve' => 'nullable|string',
        'sinossi' => 'nullable|string',
        'strillo' => 'nullable|string',
        'extra' => 'nullable|string',
        'biografia_autore' => 'nullable|string',
        'formato' => 'nullable|string',
        'numero_pagine' => 'nullable|integer',
        'copertina' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        'copertina_stesa' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
    ]);

    if ($request->hasFile('copertina')) {
        $validated['copertina_path'] = $request->file('copertina')->store('schede_libro', 'public');
    }

    if ($request->hasFile('copertina_stesa')) {
        $validated['copertina_stesa_path'] = $request->file('copertina_stesa')->store('schede_libro', 'public');
    }

    $scheda->update($validated);

    return redirect()->route('schede-libro.index')->with('success', 'Scheda aggiornata con successo!');
    }



    public function destroy($id)
    {
    $scheda = SchedaLibro::findOrFail($id);

    // Elimina le immagini salvate (se esistono)
    if ($scheda->copertina_path && Storage::disk('public')->exists($scheda->copertina_path)) {
        Storage::disk('public')->delete($scheda->copertina_path);
    }

    if ($scheda->copertina_stesa_path && Storage::disk('public')->exists($scheda->copertina_stesa_path)) {
        Storage::disk('public')->delete($scheda->copertina_stesa_path);
    }

    $scheda->delete();

    return redirect()->route('schede-libro.index')->with('success', 'Scheda eliminata con successo!');
    }


    
public function autocompleteLibro(Request $request)
{
    $term = $request->get('query');

    $libri = Libro::where('titolo', 'ilike', "%{$term}%")
                ->orWhere('isbn', 'ilike', "%{$term}%")
                ->get();

    return response()->json($libri);
}


}
