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
                $query->where('id', $request->search);
            }

            if ($request->filled('categoria')) {
                $query->where('categoria', $request->categoria);
            }

            $items = $query
                ->orderByRaw("COALESCE(NULLIF(denominazione, ''), '')")
                ->orderByRaw("CASE WHEN denominazione IS NULL OR denominazione = '' THEN LOWER(COALESCE(cognome, '')) ELSE '' END")
                ->orderByRaw("CASE WHEN denominazione IS NULL OR denominazione = '' THEN LOWER(COALESCE(nome, '')) ELSE '' END")
                ->paginate(50);

            $tutteAnagrafiche = Anagrafica::orderByRaw("
                COALESCE(NULLIF(denominazione, ''), '') ASC,
                CASE WHEN denominazione IS NULL OR denominazione = '' THEN LOWER(COALESCE(cognome, '')) ELSE '' END ASC,
                CASE WHEN denominazione IS NULL OR denominazione = '' THEN LOWER(COALESCE(nome, '')) ELSE '' END ASC
            ")->get();

            return view('anagrafiche.index', compact('items', 'tutteAnagrafiche'));
    }

    

    public function create()
    {
        return view('anagrafiche.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_fatturazione' => 'required|in:B2B,PA',
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

        if ($request->has('usa_fatturazione')) {
            $data['via_spedizione'] = $data['via_fatturazione'] ?? '';
            $data['civico_spedizione'] = $data['civico_fatturazione'] ?? '';
            $data['cap_spedizione'] = $data['cap_fatturazione'] ?? '';
            $data['comune_spedizione'] = $data['comune_fatturazione'] ?? '';
            $data['provincia_spedizione'] = $data['provincia_fatturazione'] ?? '';
            $data['nazione_spedizione'] = $data['nazione_fatturazione'] ?? '';
        }


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
            'tipo_fatturazione' => 'required|in:B2B,PA',
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

        if ($request->has('usa_fatturazione')) {
            $data['via_spedizione'] = $data['via_fatturazione'] ?? '';
            $data['civico_spedizione'] = $data['civico_fatturazione'] ?? '';
            $data['cap_spedizione'] = $data['cap_fatturazione'] ?? '';
            $data['comune_spedizione'] = $data['comune_fatturazione'] ?? '';
            $data['provincia_spedizione'] = $data['provincia_fatturazione'] ?? '';
            $data['nazione_spedizione'] = $data['nazione_fatturazione'] ?? '';
        }


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

}