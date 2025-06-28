<?php

namespace App\Http\Controllers;

use App\Models\ScaricoRichiesto;
use App\Models\Giacenza;
use Illuminate\Http\Request;

class ScaricoRichiestoController extends Controller
{
    public function index()
    {
        $richieste = ScaricoRichiesto::where('stato', 'in attesa')
            ->with(['ordine', 'libro'])
            ->get();

        foreach ($richieste as $r) {
            $giacenza = Giacenza::where('libro_id', $r->libro_id)
                ->whereHas('magazzino.anagrafica', function ($q) {
                    $q->where('categoria', 'magazzino editore');
                })
                ->with('magazzino.anagrafica')
                ->first();

            $r->magazzino_individuato = $giacenza->magazzino ?? null;
        }

        return view('scarichi_richiesti.index', compact('richieste'));
    }


    public function approva($id)
    {
        $richiesta = ScaricoRichiesto::findOrFail($id);

        $giacenza = Giacenza::where('magazzino_id', $richiesta->magazzino_id)
            ->where('libro_id', $richiesta->libro_id)
            ->first();

        if ($giacenza) {
            $giacenza->quantita = max(0, $giacenza->quantita - $richiesta->quantita);
            $giacenza->note = 'Scarico approvato ordine ' . $richiesta->ordine->codice;
            $giacenza->data_ultimo_aggiornamento = now();
            $giacenza->save();
        }

        $richiesta->update(['stato' => 'approvato']);

        return back()->with('success', 'Scarico approvato con successo.');
    }

    public function rifiuta($id)
    {
        $richiesta = ScaricoRichiesto::findOrFail($id);
        $richiesta->update(['stato' => 'rifiutato']);

        return back()->with('success', 'Scarico rifiutato.');
    }
}
