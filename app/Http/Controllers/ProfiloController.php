<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profilo;

class ProfiloController extends Controller
{
    public function index()
    {
        $profilo = Profilo::first();
        return view('profilo.index', compact('profilo'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->sede_unica) {
            $data['indirizzo_operativa'] = $data['indirizzo_amministrativa'];
            $data['numero_civico_operativa'] = $data['numero_civico_amministrativa'];
            $data['cap_operativa'] = $data['cap_amministrativa'];
            $data['comune_operativa'] = $data['comune_amministrativa'];
            $data['provincia_operativa'] = $data['provincia_amministrativa'];
            $data['nazione_operativa'] = $data['nazione_amministrativa'];
        }

        Profilo::updateOrCreate(['id' => 1], $data);

        return redirect()->route('profilo.index')->with('success', 'Profilo aggiornato con successo.');
    }
}