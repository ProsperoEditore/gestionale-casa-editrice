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

    // Mappa info_spedizione → ID magazzino
    $magazziniMap = [
        'spedito da magazzino editore' => 1,
        'spedito da tipografia' => 2,
        'spedito da magazzino terzo' => 3,
    ];

    foreach ($richieste as $r) {
        $ordine = $r->ordine;
        $libroId = $r->libro_id;

        // Prendi info_spedizione dalla tabella pivot ordine-libro
        $infoSpedizione = $ordine->libri()
            ->where('libro_id', $libroId)
            ->first()
            ->pivot
            ->info_spedizione ?? null;

        // Trova l'ID del magazzino corretto
        $magazzinoId = $magazziniMap[$infoSpedizione] ?? null;

        // Se trovato, carica il magazzino (con anagrafica) e assegnalo
        if ($magazzinoId) {
            $r->magazzino_individuato = \App\Models\Magazzino::with('anagrafica')->find($magazzinoId);
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

    // Mappa info_spedizione al magazzino reale
    $magazziniMap = [
        'spedito da magazzino editore' => 1,
        'spedito da tipografia' => 2,
        'spedito da magazzino terzo' => 3,
    ];

    $magazzinoId = $magazziniMap[$infoSpedizione] ?? null;

    // Trova la giacenza corrispondente
    $giacenza = Giacenza::where('libro_id', $libroId)
        ->where('magazzino_id', $magazzinoId)
        ->first();

    if ($giacenza) {
        $giacenza->quantita = max(0, $giacenza->quantita - $richiesta->quantita);
        $giacenza->note = 'Scarico approvato ordine ' . $ordine->codice;
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
