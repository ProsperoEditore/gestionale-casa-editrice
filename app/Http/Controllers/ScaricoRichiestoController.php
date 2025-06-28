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
            ->with(['ordine.libri', 'libro']) // carica libri con pivot
            ->get();

        foreach ($richieste as $r) {
            $ordine = $r->ordine;
            $libroId = $r->libro_id;

            // ✅ Usa il pivot corretto
            $pivot = $ordine->libri->firstWhere('pivot.libro_id', $libroId)?->pivot;
            $infoSpedizione = $pivot?->info_spedizione;

            if (in_array($infoSpedizione, ['spedito da magazzino editore', 'consegna a mano'])) {
                $giacenza = \App\Models\Giacenza::with('magazzino.anagrafica')
                    ->where('libro_id', $libroId)
                    ->whereHas('magazzino.anagrafica', function ($q) {
                        $q->where('categoria', 'magazzino editore');
                    })
                    ->first();

                $r->magazzino_individuato = $giacenza?->magazzino;
            } else {
                $r->magazzino_individuato = null;
            }
        }

        return view('scarichi_richiesti.index', compact('richieste'));
    }


    public function approva($id)
    {
        $richiesta = ScaricoRichiesto::findOrFail($id);
        $ordine = $richiesta->ordine;
        $libroId = $richiesta->libro_id;

        $infoSpedizione = $ordine->libri()
            ->where('libro_id', $libroId)
            ->first()
            ->pivot
            ->info_spedizione ?? null;

        if (in_array($infoSpedizione, ['spedito da magazzino editore', 'consegna a mano'])) {
            $giacenza = Giacenza::where('libro_id', $libroId)
                ->whereHas('magazzino.anagrafica', function ($q) {
                    $q->where('categoria', 'magazzino editore');
                })
                ->first();

            if ($giacenza) {
                $giacenza->quantita = max(0, $giacenza->quantita - $richiesta->quantita);
                $giacenza->note = 'Scarico approvato ordine ' . $ordine->codice;
                $giacenza->data_ultimo_aggiornamento = now();
                $giacenza->save();
            }
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
