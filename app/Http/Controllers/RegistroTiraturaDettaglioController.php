<?php

namespace App\Http\Controllers;

use App\Models\RegistroTiraturaDettaglio;
use App\Models\RegistroTirature;
use App\Models\Libro;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RegistroTiraturaDettaglioImport;
use App\Exports\RegistroTiraturaDettaglioExport;
use Barryvdh\DomPDF\Facade\Pdf;

class RegistroTiraturaDettaglioController extends Controller
{
    public function index($registroTiratureId)
    {
        $registro = RegistroTirature::findOrFail($registroTiratureId);
        $dettagli = $registro->dettagli;

        return view('registro-tirature.dettagli.index', compact('registro', 'dettagli'));
    }

    public function create($registroTiratureId)
    {
        $registro = RegistroTirature::findOrFail($registroTiratureId);
        $libri = Libro::all();

        return view('registro-tirature.dettagli.create', compact('registro', 'libri'));
    }

    public function store(Request $request, RegistroTirature $registroTirature)
    {
        $request->validate([
            'data' => 'required|date',
            'titolo_id' => 'required|exists:libri,id',
            'copie_stampate' => 'required|integer|min:0',
            'prezzo_vendita_iva' => 'required',
            'imponibile_relativo' => 'required',
            'imponibile' => 'required',
            'iva_4percento' => 'required',
        ]);
    
        RegistroTiraturaDettaglio::create([
            'registro_tirature_id' => $registroTirature->id,
            'data' => $request->data,
            'titolo_id' => $request->titolo_id,
            'copie_stampate' => $request->copie_stampate,
            'prezzo_vendita_iva' => str_replace(',', '.', $request->prezzo_vendita_iva),
            'imponibile_relativo' => str_replace(',', '.', $request->imponibile_relativo),
            'imponibile' => str_replace(',', '.', $request->imponibile),
            'iva_4percento' => str_replace(',', '.', $request->iva_4percento),
        ]);
    
        return redirect()->route('registro-tirature.show', $registroTirature->id)
            ->with('success', 'Dettaglio aggiunto con successo.');
    }

    public function edit(RegistroTirature $registroTirature, RegistroTiraturaDettaglio $dettaglio)
    {
    $libri = \App\Models\Libro::all();

    return view('registro-tirature.dettagli.edit', [
        'registro' => $registroTirature,
        'dettaglio' => $dettaglio,
        'libri' => $libri,
    ]);
    }

    public function update(Request $request, RegistroTirature $registroTirature, RegistroTiraturaDettaglio $dettaglio)
    {
    $request->validate([
        'data' => 'required|date',
        'titolo_id' => 'required|exists:libri,id',
        'copie_stampate' => 'required|integer|min:0',
        'prezzo_vendita_iva' => 'required',
        'imponibile_relativo' => 'required',
        'imponibile' => 'required',
        'iva_4percento' => 'required',
    ]);

    $dettaglio->update([
        'registro_tirature_id' => $registroTirature->id,
        'data' => $request->data,
        'titolo_id' => $request->titolo_id,
        'copie_stampate' => $request->copie_stampate,
        'prezzo_vendita_iva' => str_replace(',', '.', $request->prezzo_vendita_iva),
        'imponibile_relativo' => str_replace(',', '.', $request->imponibile_relativo),
        'imponibile' => str_replace(',', '.', $request->imponibile),
        'iva_4percento' => str_replace(',', '.', $request->iva_4percento),
    ]);

    return redirect()->route('registro-tirature.show', $registroTirature->id)
        ->with('success', 'Dettaglio aggiornato con successo.');
    }

public function destroy(RegistroTirature $registroTirature, RegistroTiraturaDettaglio $dettaglio)
    {
    $dettaglio->delete();

    return redirect()->route('registro-tirature.show', $registroTirature->id)
        ->with('success', 'Dettaglio eliminato con successo.');
    }

    public function importExcel(Request $request, RegistroTirature $registroTirature)
    {
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls',
    ]);

    Excel::import(new RegistroTiraturaDettaglioImport($registroTirature), $request->file('file'));

        if (session()->has('righe_ambigue_tirature')) {
            session()->reflash(); // conserva righe + errori
            return redirect()->route('registro-tirature.show', $registroTirature->id);
        }

        if (session()->has('import_errori')) {
            return redirect()->back()->with([
                'success' => 'Alcune righe sono state scartate.',
                'import_errori' => session('import_errori'),
            ]);
        }


    return redirect()->route('registro-tirature.show', $registroTirature->id)
        ->with('success', 'File importato con successo.');
    }


public function risolviConflitti(Request $request, RegistroTirature $registro)
{
    foreach ($request->input('righe', []) as $riga) {
        if (!isset($riga['data']) || !isset($riga['copie_stampate'])) continue;
        if (empty($riga['id']) || $riga['id'] === '__SKIP__') continue;


        $libro = \App\Models\Libro::find($riga['id']);
        if (!$libro) continue;

        $copie = intval($riga['copie_stampate']);
        $prezzo = floatval($libro->prezzo);
        $impRel = $copie * $prezzo * 0.3;
        $imp = $impRel / 1.04;
        $iva = $impRel - $imp;

        RegistroTiraturaDettaglio::create([
            'registro_tirature_id' => $registro->id,
            'titolo_id' => $libro->id,
            'data' => $riga['data'],
            'copie_stampate' => $copie,
            'prezzo_vendita_iva' => $prezzo,
            'imponibile_relativo' => $impRel,
            'imponibile' => $imp,
            'iva_4percento' => $iva,
        ]);
    }

    return redirect()->route('registro-tirature.show', $registro->id)
        ->with('success', 'Righe ambigue salvate con successo.');
    }




public function exportExcel(RegistroTirature $registroTirature)
    {
    return Excel::download(new RegistroTiraturaDettaglioExport($registroTirature->id), 'registro_tirature.xlsx');
    }

    public function exportPDF($id)
    {
        $registro = RegistroTirature::findOrFail($id);
        $dettagli = $registro->dettagli()->with('titolo')->get();
    
        $data = [
            'registro' => $registro,
            'dettagli' => $dettagli,
            'pdf' => true
        ];
    
        // Togli setOption margini e impostali direttamente tramite setPaper
        $pdf = PDF::loadView('registro-tirature.dettagli.pdf', $data)
            ->setPaper('a4', 'landscape');
    
        return $pdf->download("Registro_Tirature_{$registro->periodo}_{$registro->anno}.pdf");
    }
    
    
    
}