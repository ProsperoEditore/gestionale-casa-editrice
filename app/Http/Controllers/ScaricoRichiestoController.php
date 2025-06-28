<?php

namespace App\Http\Controllers;

use App\Models\ScaricoRichiesto;
use App\Models\Giacenza;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ScaricoRichiestoController extends Controller
{
    public function index()
    {
        $richieste = ScaricoRichiesto::where('stato', 'in attesa')
            ->with(['ordine.anagrafica', 'libro'])
            ->get();

            foreach ($richieste as $r) {
                $giacenza = \App\Models\Giacenza::with('magazzino.anagrafica')
                    ->where('isbn', $r->libro->isbn)
                    ->where('quantita', '>', 0)
                    ->whereHas('magazzino.anagrafica', function ($q) {
                        $q->where('categoria', 'magazzino editore');
                    })
                    ->orderByDesc('data_ultimo_aggiornamento')
                    ->first();

                $r->magazzino_nome = $giacenza?->magazzino?->anagrafica?->denominazione ?? $giacenza?->magazzino?->nome;
                $r->quantita_disponibile = $giacenza?->quantita;
                $anagrafica = $r->ordine->anagrafica;
                $r->destinatario = $anagrafica->denominazione ?? trim($anagrafica->nome . ' ' . $anagrafica->cognome);
            }


        return view('scarichi_richiesti.index', compact('richieste'));
    }

    public function approva($id)
    {
        $richiesta = ScaricoRichiesto::findOrFail($id);

        // Usa la stessa logica della index per trovare la giacenza corretta
        $giacenza = Giacenza::with('magazzino.anagrafica')
            ->where('libro_id', $richiesta->libro_id)
            ->where('quantita', '>', 0)
            ->whereHas('magazzino.anagrafica', function ($q) {
                $q->where('categoria', 'magazzino editore');
            })
            ->orderByDesc('data_ultimo_aggiornamento')
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



public function exportPdf()
{
    $richieste = ScaricoRichiesto::where('stato', 'in attesa')
        ->with(['ordine.anagrafica', 'libro'])
        ->get();

    foreach ($richieste as $r) {
        $giacenza = \App\Models\Giacenza::with('magazzino.anagrafica')
            ->where('isbn', $r->libro->isbn)
            ->where('quantita', '>', 0)
            ->whereHas('magazzino.anagrafica', function ($q) {
                $q->where('categoria', 'magazzino editore');
            })
            ->orderByDesc('data_ultimo_aggiornamento')
            ->first();

        $r->magazzino_nome = $giacenza?->magazzino?->anagrafica?->denominazione ?? $giacenza?->magazzino?->nome;
        $r->quantita_disponibile = $giacenza?->quantita;

        $anagrafica = $r->ordine->anagrafica;
        $r->destinatario = $anagrafica->denominazione ?? trim($anagrafica->nome . ' ' . $anagrafica->cognome);
    }

    $pdf = Pdf::loadView('scarichi_richiesti.pdf', compact('richieste'))->setPaper('A4', 'landscape');
    return $pdf->download('scarichi-da-approvare.pdf');
}

}
