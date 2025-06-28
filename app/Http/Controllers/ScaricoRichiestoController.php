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
            $libroId = $r->libro_id;

            // Cerco la giacenza del libro (come fa la sezione Ordini)
            $giacenza = Giacenza::with('magazzino.anagrafica')
                ->where('libro_id', $libroId)
                ->orderByDesc('data_ultimo_aggiornamento') // opzionale
                ->first();

            $r->magazzino_individuato = $giacenza?->magazzino;
        }

        return view('scarichi_richiesti.index', compact('richieste'));
    }

    public function approva($id)
    {
        $richiesta = ScaricoRichiesto::findOrFail($id);
        $libroId = $richiesta->libro_id;

        // Trova la giacenza da cui sottrarre
        $giacenza = Giacenza::where('libro_id', $libroId)
            ->orderByDesc('data_ultimo_aggiornamento') // opzionale ma consigliato
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
