<?php

namespace App\Http\Controllers;

use App\Models\RegistroTirature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class RegistroTiratureController extends Controller
{
    public function index()
    {
        $registros = RegistroTirature::latest()->paginate(50);
        return view('registro-tirature.index', compact('registros'));
    }

    public function create()
    {
        return view('registro-tirature.create');
    }

    public function show($id)
    {
        $registro = RegistroTirature::findOrFail($id);
        $dettagli = $registro->dettagli ?? collect(); 
    
        return view('registro-tirature.show', compact('registro', 'dettagli'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'periodo' => 'required|string',
            'anno' => 'required|numeric|min:1900|max:' . (date('Y') + 1),
        ]);

        RegistroTirature::create($request->all());

        return redirect()->route('registro-tirature.index')->with('success', 'Registro tirature creato con successo.');
    }

    public function edit(RegistroTirature $registroTirature)
    {
        return view('registro-tirature.edit', compact('registroTirature'));
    }

    public function update(Request $request, RegistroTirature $registroTirature)
    {
        $request->validate([
            'periodo' => 'required|string',
            'anno' => 'required|numeric|min:1900|max:' . (date('Y') + 1),
        ]);

        $registroTirature->update($request->all());

        return redirect()->route('registro-tirature.index')->with('success', 'Registro tirature aggiornato con successo.');
    }

    public function destroy(RegistroTirature $registroTirature)
    {
        $registroTirature->delete();

        return redirect()->route('registro-tirature.index')->with('success', 'Registro tirature eliminato con successo.');
    }
}
