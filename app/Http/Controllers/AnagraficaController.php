<?php

namespace App\Http\Controllers;

use App\Models\Anagrafica;
use Illuminate\Http\Request;

class AnagraficaController extends Controller
{
    public function index(Request $request)
    {
        $query = Anagrafica::query();
    
        if ($request->filled('search')) {
            $search = strtolower(str_replace(' ', '', $request->search));
            $query->whereRaw("LOWER(REPLACE(nome, ' ', '')) LIKE ?", ["%{$search}%"]);
        }
    
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
    
        $items = $query
    ->orderByRaw("
        COALESCE(NULLIF(denominazione, ''), '')
    ")
    ->orderByRaw("
        CASE WHEN denominazione IS NULL OR denominazione = '' THEN LOWER(COALESCE(cognome, '')) ELSE '' END
    ")
    ->orderByRaw("
        CASE WHEN denominazione IS NULL OR denominazione = '' THEN LOWER(COALESCE(nome, '')) ELSE '' END
    ")
    ->paginate(50);

    
        return view('anagrafiche.index', compact('items'));
    }
    

    public function create()
    {
        return view('anagrafiche.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria' => 'required|string',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string',
            'pec' => 'nullable|string',
            'codice_univoco' => 'nullable|string',
            'partita_iva' => 'nullable|string',
            'codice_fiscale' => 'nullable|string',
            'denominazione' => 'nullable|string',
            'nome' => 'nullable|string',
            'cognome' => 'nullable|string',
        ]);

        // Validazione logica alternativa
        if (empty($request->denominazione) && (empty($request->nome) || empty($request->cognome))) {
            return back()->withInput()->withErrors([
                'denominazione' => 'Compila la denominazione oppure nome e cognome.',
            ]);
        }

        $data = $request->all();

        // Compone indirizzo fatturazione
        $data['indirizzo_fatturazione'] = implode(', ', array_filter([
            $data['via_fatturazione'] ?? '',
            $data['civico_fatturazione'] ?? ''
        ])) . ' - ' . implode(', ', array_filter([
            $data['cap_fatturazione'] ?? '',
            $data['comune_fatturazione'] ?? ''
        ])) . (!empty($data['provincia_fatturazione']) ? ' (' . $data['provincia_fatturazione'] . ')' : '');

        // Compone indirizzo spedizione
        $data['indirizzo_spedizione'] = implode(', ', array_filter([
            $data['via_spedizione'] ?? '',
            $data['civico_spedizione'] ?? ''
        ])) . ' - ' . implode(', ', array_filter([
            $data['cap_spedizione'] ?? '',
            $data['comune_spedizione'] ?? ''
        ])) . (!empty($data['provincia_spedizione']) ? ' (' . $data['provincia_spedizione'] . ')' : '');

        Anagrafica::create($data);

        return redirect()->route('anagrafiche.index')->with('success', 'Anagrafica aggiunta con successo.');
    }

    public function edit($id)
    {
        $item = Anagrafica::findOrFail($id);
        return view('anagrafiche.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'categoria' => 'required|string',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string',
            'pec' => 'nullable|string',
            'codice_univoco' => 'nullable|string',
            'partita_iva' => 'nullable|string',
            'codice_fiscale' => 'nullable|string',
            'denominazione' => 'nullable|string',
            'nome' => 'nullable|string',
            'cognome' => 'nullable|string',
        ]);

        if (empty($request->denominazione) && (empty($request->nome) || empty($request->cognome))) {
            return back()->withInput()->withErrors([
                'denominazione' => 'Compila la denominazione oppure nome e cognome.',
            ]);
        }

        $data = $request->all();

        $data['indirizzo_fatturazione'] = implode(', ', array_filter([
            $data['via_fatturazione'] ?? '',
            $data['civico_fatturazione'] ?? ''
        ])) . ' - ' . implode(', ', array_filter([
            $data['cap_fatturazione'] ?? '',
            $data['comune_fatturazione'] ?? ''
        ])) . (!empty($data['provincia_fatturazione']) ? ' (' . $data['provincia_fatturazione'] . ')' : '');

        $data['indirizzo_spedizione'] = implode(', ', array_filter([
            $data['via_spedizione'] ?? '',
            $data['civico_spedizione'] ?? ''
        ])) . ' - ' . implode(', ', array_filter([
            $data['cap_spedizione'] ?? '',
            $data['comune_spedizione'] ?? ''
        ])) . (!empty($data['provincia_spedizione']) ? ' (' . $data['provincia_spedizione'] . ')' : '');

        $anagrafica = Anagrafica::findOrFail($id);
        $anagrafica->update($data);

        return redirect()->route('anagrafiche.index')->with('success', 'Anagrafica aggiornata con successo.');
    }

    public function destroy($id)
    {
        Anagrafica::findOrFail($id)->delete();
        return redirect()->route('anagrafiche.index')->with('success', 'Anagrafica eliminata con successo.');
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('term');

        $anagrafiche = Anagrafica::whereRaw("
            LOWER(CONCAT_WS(' ', COALESCE(denominazione, ''), COALESCE(nome, ''), COALESCE(cognome, '')))
            LIKE ?
        ", ["%".strtolower($query)."%"])
        ->limit(10)
        ->get(['id', 'denominazione', 'nome', 'cognome']);

        // Restituisce 'nome_completo' per ciascun risultato
        $results = $anagrafiche->map(function ($a) {
            return [
                'id' => $a->id,
                'nome' => $a->nome_completo,
            ];
        });

        return response()->json($results);
    }


}