<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\ReportDettaglio;
use App\Models\RegistroVendite;
use App\Models\RegistroVenditeDettaglio;
use App\Models\Contratto;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportDettaglioController extends Controller
{
    public function index($reportId, Request $request)
    {
        $report = Report::with(['libro', 'contratto'])->findOrFail($reportId);
        $contratto = $report->contratto;
        $anno = $request->input('anno');
    
        // Recupera le percentuali delle royalties base
        $percentuali = [
            'diretta' => $contratto->royalties_vendite_dirette ?? 0,
            'indiretta' => $contratto->royalties_vendite_indirette ?? 0,
            'evento' => $contratto->royalties_eventi ?? 0,
        ];
    
        $dettagli_raw = RegistroVenditeDettaglio::with(['registroVendite'])
        ->where('isbn', $report->libro->isbn)
        ->when($anno, function ($query) use ($anno) {
            return $query->whereYear('periodo', $anno);
        })
        ->get()
        ->sortBy(fn($item) => $item->data ?? '') // ordinamento cronologico per il calcolo
        ->values();
    
    
        // Variabile per tenere traccia della quantità cumulativa
        $quantita_cumulata = 0;
    
        // Applica i calcoli per ogni riga
        $dettagli = $dettagli_raw->map(function ($item) use ($contratto, $percentuali, &$quantita_cumulata) {
            $canale = strtolower($item->registroVendite->canale_vendita ?? 'N/A');
            $luogo = $item->registroVendite->anagrafica->nome ?? 'N/A';
    
            $item->prezzo_unitario = $item->prezzo;
            $item->canale = ucfirst($canale);
            $item->luogo = $luogo;
    
            $quantita = $item->quantita;
            $prezzo_unitario = $item->prezzo;
            $royalties_totali = 0;
    
            if ($canale === 'vendite indirette') {
                $iniziale = $quantita_cumulata + 1;
                $finale = $quantita_cumulata + $quantita;
    
                for ($i = $iniziale; $i <= $finale; $i++) {
                    $s1 = $contratto->royalties_vendite_indirette_soglia_1 ?? PHP_INT_MAX;
                    $s2 = $contratto->royalties_vendite_indirette_soglia_2 ?? PHP_INT_MAX;
                    
                    $p1 = $contratto->royalties_vendite_indirette_percentuale_1 ?? $percentuali['indiretta'];
                    $p2 = $contratto->royalties_vendite_indirette_percentuale_2 ?? $percentuali['indiretta'];
                    $p3 = $contratto->royalties_vendite_indirette_percentuale_3 ?? $percentuali['indiretta'];
                    
                    if ($i <= $s1) {
                        $percentuale = $p1;
                    } elseif ($i <= $s2) {
                        $percentuale = $p2;
                    } else {
                        $percentuale = $p3;
                    }
                    
    
                    $royalties_totali += round($prezzo_unitario * ($percentuale / 100), 2);
                }
            } elseif ($canale === 'vendite dirette') {
                $percentuale = $percentuali['diretta'];
                $royalties_totali = round($item->valore_lordo * ($percentuale / 100), 2);
            } elseif ($canale === 'evento') {
                $percentuale = $percentuali['evento'];
                $royalties_totali = round($item->valore_lordo * ($percentuale / 100), 2);
            }
    
            $item->royalties = $royalties_totali;
            $quantita_cumulata += $quantita;
    
            return $item;
        });

        $dettagli = $dettagli->sortByDesc(fn($item) => $item->data)->values();


    
        // Calcola i totali per la tabella
        $totali = [
            'quantita' => $dettagli->sum('quantita'),
            'royalties' => $dettagli->sum('royalties'),
        ];
    
        return view('report.dettagli.index', compact(
            'report',
            'dettagli',
            'percentuali',
            'totali',
            'anno'
        ));
    }


    public function exportPdf($reportId, Request $request)
    {
        $report = Report::with(['libro', 'contratto'])->findOrFail($reportId);
        $contratto = $report->contratto;
    
        // Recupera le percentuali delle royalties
        $percentuali = [
            'diretta' => $contratto->royalties_vendite_dirette ?? 0,
            'indiretta' => $contratto->royalties_vendite_indirette ?? 0,
            'evento' => $contratto->royalties_eventi ?? 0,
        ];
    
        $dettagli_raw = RegistroVenditeDettaglio::with(['registroVendite.anagrafica'])
        ->where('isbn', $report->libro->isbn)
        ->get()
        ->sortByDesc(fn($item) => $item->data)
        ->values();
    
    
    
        $quantita_cumulata = 0;
    
        $dettagli = $dettagli_raw->map(function ($item) use ($contratto, $percentuali, &$quantita_cumulata) {
            $canale = strtolower($item->registroVendite->canale_vendita ?? 'N/A');
            $luogo = $item->registroVendite->anagrafica->nome ?? 'N/A';
    
            $item->prezzo_unitario = $item->prezzo;
            $item->canale = ucfirst($canale);
            $item->luogo = $luogo;
            $item->periodo_testo = $item->periodo;
    
            $quantita = $item->quantita;
            $prezzo_unitario = $item->prezzo;
            $royalties_totali = 0;
    
            if ($canale === 'vendite indirette') {
                $iniziale = $quantita_cumulata + 1;
                $finale = $quantita_cumulata + $quantita;
    
                $s1 = $contratto->royalties_vendite_indirette_soglia_1 ?? PHP_INT_MAX;
                $s2 = $contratto->royalties_vendite_indirette_soglia_2 ?? PHP_INT_MAX;
    
                $p1 = $contratto->royalties_vendite_indirette_percentuale_1 ?? $percentuali['indiretta'];
                $p2 = $contratto->royalties_vendite_indirette_percentuale_2 ?? $percentuali['indiretta'];
                $p3 = $contratto->royalties_vendite_indirette_percentuale_3 ?? $percentuali['indiretta'];
    
                for ($i = $iniziale; $i <= $finale; $i++) {
                    if ($i <= $s1) {
                        $percentuale = $p1;
                    } elseif ($i <= $s2) {
                        $percentuale = $p2;
                    } else {
                        $percentuale = $p3;
                    }
    
                    $royalties_totali += round($prezzo_unitario * ($percentuale / 100), 2);
                }
            } elseif ($canale === 'vendite dirette') {
                $percentuale = $percentuali['diretta'];
                $royalties_totali = round($item->valore_lordo * ($percentuale / 100), 2);
            } elseif ($canale === 'evento') {
                $percentuale = $percentuali['evento'];
                $royalties_totali = round($item->valore_lordo * ($percentuale / 100), 2);
            }
    
            $item->royalties = $royalties_totali;
            $quantita_cumulata += $quantita;
    
            return $item;
        });
    
        $totali = [
            'quantita' => $dettagli->sum('quantita'),
            'royalties' => $dettagli->sum('royalties'),
        ];
    
        $pdf = Pdf::loadView('report.dettagli.pdf', compact('report', 'dettagli', 'percentuali', 'totali'))
            ->setPaper('a4', 'landscape');
    
        return $pdf->download('Report_' . $report->libro->titolo . '.pdf');
    }
    
}