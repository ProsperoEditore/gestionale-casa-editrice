<?php

namespace App\Http\Controllers;

use App\Models\Ritenuta;
use App\Models\MarchioEditoriale;
use App\Models\Libro;
use App\Models\Report;
use App\Models\ReportDettaglio;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class RitenutaController extends Controller
{



    public function create()
    {
        $marchi = MarchioEditoriale::all();
        $reportDisponibili = Report::with('libro')->latest()->get();

        return view('ritenute.create', compact('marchi', 'reportDisponibili'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'nome_autore' => 'required|string',
            'cognome_autore' => 'required|string',
            'data_nascita' => 'required|date',
            'luogo_nascita' => 'required|string',
            'codice_fiscale' => 'required|string',
            'data_emissione' => 'required|date',
            'prestazioni' => 'required|array',
            'prestazioni.*.descrizione' => 'required|string',
            'prestazioni.*.importo' => 'required|numeric|min:0',
        ]);

        $anno = Carbon::parse($request->data_emissione)->year;
        $numeroProgressivo = Ritenuta::whereYear('data_emissione', $anno)->count() + 1;
        $numero = str_pad($numeroProgressivo, 2, '0', STR_PAD_LEFT) . '/' . $anno;

        $dataNascita = Carbon::parse($request->data_nascita);
        $eta = $dataNascita->age;
        $under35 = $eta < 35;

        $totale = collect($request->prestazioni)->sum('importo');

        $quota_esente = round($totale * ($under35 ? 0.40 : 0.25), 2);
        $imponibile = round($totale - $quota_esente, 2);
        $ritenuta = round($imponibile * 0.20, 2);
        $netto = round($totale - $ritenuta, 2);

        $ritenuta = Ritenuta::create([
            'numero' => $numero,
            'data_emissione' => $request->data_emissione,
            'nome_autore' => $request->nome_autore,
            'cognome_autore' => $request->cognome_autore,
            'data_nascita' => $request->data_nascita,
            'luogo_nascita' => $request->luogo_nascita,
            'codice_fiscale' => $request->codice_fiscale,
            'iban' => $request->iban,
            'indirizzo' => $request->indirizzo,
            'marchio_id' => $request->marchio_id,
            'prestazioni' => $request->prestazioni,
            'totale' => $totale,
            'quota_esente' => $quota_esente,
            'imponibile' => $imponibile,
            'ritenuta' => $ritenuta,
            'netto_pagare' => $netto,
            'nota_iva' => $request->nota_iva,
            'marca_bollo' => $request->marca_bollo ?? '€ 2,00 (per importi superiori a 77,47)',
        ]);

        return redirect()->route('ritenute.index')->with('success', 'Ritenuta creata correttamente.');
    }

    // Opzionale: metodo per ricavare gli importi da ReportDettaglio
    public function getImportiDaReport(Request $request)
    {
        $request->validate([
            'titoli' => 'required|array',
            'dal' => 'required|date',
            'al' => 'required|date',
        ]);

        $righe = ReportDettaglio::whereIn('libro_id', $request->titoli)
            ->whereBetween('data', [$request->dal, $request->al])
            ->get();

        $prestazioni = $righe->groupBy('libro_id')->map(function ($r, $id) {
            $titolo = optional($r->first()->libro)->titolo ?? 'Titolo sconosciuto';
            $totale = $r->sum('royalties');
            return [
                'descrizione' => "Report \"{$titolo}\" dal " . $r->min('data')->format('d/m/Y') . " al " . $r->max('data')->format('d/m/Y'),
                'importo' => round($totale, 2),
            ];
        })->values();

        return response()->json($prestazioni);
    }


public function updatePagamento(Request $request, $id)
{
    $request->validate([
        'tipo' => 'required|in:netto,ritenuta',
        'data' => 'nullable|date',
    ]);

    $ritenuta = Ritenuta::findOrFail($id);

    if ($request->tipo === 'netto') {
        $ritenuta->data_pagamento_netto = $request->data;
    } else {
        $ritenuta->data_pagamento_ritenuta = $request->data;
    }

    $ritenuta->save();

    return response()->json(['success' => true]);
}



public function pdf(Ritenuta $ritenuta)
{
    $nomeFile = 'Ritenuta_' . str_replace(['/', '\\'], '-', $ritenuta->numero)
        . '_' . Str::slug($ritenuta->cognome_autore, '_')
        . '_' . Str::slug($ritenuta->nome_autore, '_') . '.pdf';

    return Pdf::loadView('ritenute.pdf', compact('ritenuta'))
        ->setPaper('A4')
        ->stream($nomeFile);
}


public function edit(Ritenuta $ritenuta)
{
    $marchi = MarchioEditoriale::all();
    return view('ritenute.edit', compact('ritenuta', 'marchi'));
}

public function update(Request $request, Ritenuta $ritenuta)
{
    $request->validate([
        'nome_autore' => 'required|string',
        'cognome_autore' => 'required|string',
        'data_nascita' => 'required|date',
        'luogo_nascita' => 'required|string',
        'codice_fiscale' => 'required|string',
        'data_emissione' => 'required|date',
        'prestazioni' => 'required|array',
        'prestazioni.*.descrizione' => 'required|string',
        'prestazioni.*.importo' => 'required|numeric|min:0',
    ]);

    $dataNascita = \Carbon\Carbon::parse($request->data_nascita);
    $under35 = $dataNascita->age < 35;

    $totale = collect($request->prestazioni)->sum('importo');
    $quota_esente = round($totale * ($under35 ? 0.40 : 0.25), 2);
    $imponibile = round($totale - $quota_esente, 2);
    $ritenuta = round($imponibile * 0.20, 2);
    $netto = round($totale - $ritenuta, 2);

    $ritenuta->update([
        'numero' => $request->numero_nota,
        'data_emissione' => $request->data_emissione,
        'nome_autore' => $request->nome_autore,
        'cognome_autore' => $request->cognome_autore,
        'data_nascita' => $request->data_nascita,
        'luogo_nascita' => $request->luogo_nascita,
        'codice_fiscale' => $request->codice_fiscale,
        'iban' => $request->iban,
        'indirizzo' => $request->indirizzo,
        'marchio_id' => $request->marchio_id,
        'prestazioni' => $request->prestazioni,
        'totale' => $totale,
        'quota_esente' => $quota_esente,
        'imponibile' => $imponibile,
        'ritenuta' => $ritenuta,
        'netto_pagare' => $netto,
        'nota_iva' => $request->nota_iva,
        'marca_bollo' => $request->marca_bollo ?? '€ 2,00 (per importi superiori a 77,47)',
    ]);

    return redirect()->route('ritenute.index')->with('success', 'Ritenuta aggiornata con successo.');
}


public function index(Request $request)
{
    $query = Ritenuta::query();

    if ($request->filled('anno')) {
        $query->whereYear('data_emissione', $request->anno);
    }

    if ($request->filled('autore')) {
        $query->where(function ($q) use ($request) {
            $q->where('nome_autore', 'like', '%' . $request->autore . '%')
              ->orWhere('cognome_autore', 'like', '%' . $request->autore . '%');
        });
    }

    $ritenute = $query->orderByDesc('data_emissione')->get();
    return view('ritenute.index', compact('ritenute'));
}

public function autocompleteAutori(Request $request)
{
    $term = $request->term;

    $autori = Ritenuta::selectRaw("CONCAT(nome_autore, ' ', cognome_autore) AS label")
        ->whereRaw("CONCAT(nome_autore, ' ', cognome_autore) ILIKE ?", ["%{$term}%"])
        ->distinct()
        ->limit(10)
        ->get();

    return response()->json($autori);
}


}
