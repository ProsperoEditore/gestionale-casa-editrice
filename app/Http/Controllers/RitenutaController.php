<?php

namespace App\Http\Controllers;

use App\Models\Ritenuta;
use App\Models\MarchioEditoriale;
use App\Models\Libro;
use App\Models\Report;
use App\Models\ReportDettaglio;
use App\Models\Autore;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
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
Log::info('RICHIESTA CREAZIONE RITENUTA', $request->all());
Log::info('Prestazioni ricevute:', $request->prestazioni ?? []);

        // Converte il formato gg-mm-aaaa in Y-m-d
        if ($request->filled('data_nascita')) {
            $parts = explode('-', $request->data_nascita);
            if (count($parts) === 3 && strlen($parts[2]) === 4) {
                $request->merge([
                    'data_nascita' => $parts[2] . '-' . $parts[1] . '-' . $parts[0],
                ]);
            }
        }

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

        $numero = $request->numero_nota;

        if (!$numero) {
            $anno = Carbon::parse($request->data_emissione)->year;
            $numeroProgressivo = Ritenuta::whereYear('data_emissione', $anno)->count() + 1;
            $numero = str_pad($numeroProgressivo, 2, '0', STR_PAD_LEFT) . '/' . $anno;
        }


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
    $nomeFile = 'DA_' . str_replace(['/', '\\'], '-', $ritenuta->numero)
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

    if ($request->filled('data_nascita')) {
        $parts = explode('-', $request->data_nascita);
        if (count($parts) === 3 && strlen($parts[2]) === 4) {
            $request->merge([
                'data_nascita' => $parts[2] . '-' . $parts[1] . '-' . $parts[0],
            ]);
        }
    }

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
    $ritenuta_calcolata = round($imponibile * 0.20, 2);
    $netto = round($totale - $ritenuta_calcolata, 2);

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
        'ritenuta' => $ritenuta_calcolata,
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

    $ritenute = $query
    ->orderByDesc('data_emissione')
    ->orderByRaw("CAST(SPLIT_PART(numero, '/', 1) AS INTEGER) DESC")
    ->paginate(30);

    return view('ritenute.index', compact('ritenute'));
}

public function destroy($id)
{
    $ritenuta = Ritenuta::findOrFail($id);
    $ritenuta->delete();

    return redirect()->route('ritenute.index')->with('success', 'Ritenuta eliminata con successo.');
}

public function autocompleteAutore(Request $request)
{
    $term = strtolower($request->input('term'));

    $autori = Autore::whereRaw("LOWER(nome) LIKE ?", ["%{$term}%"])
        ->orWhereRaw("LOWER(cognome) LIKE ?", ["%{$term}%"])
        ->orWhereRaw("LOWER(pseudonimo) LIKE ?", ["%{$term}%"])
        ->orWhereRaw("LOWER(denominazione) LIKE ?", ["%{$term}%"])
        ->limit(10)
        ->get();

    return response()->json($autori->map(function ($a) {
        return [
            'label' => trim("{$a->nome} {$a->cognome}"),
            'value' => trim("{$a->nome} {$a->cognome}"),
            'nome' => $a->nome,
            'cognome' => $a->cognome,
            'codice_fiscale' => $a->codice_fiscale,
            'data_nascita' => $a->data_nascita,
            'luogo_nascita' => $a->luogo_nascita,
            'iban' => $a->iban,
            'indirizzo' => $a->indirizzo,
        ];
    }));
}



}
