<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Magazzino;
use App\Models\Anagrafica;
use Carbon\Carbon;

class MagazzinoController extends Controller
{
    public function index(Request $request)
    {
        $query = Magazzino::with('anagrafica');
    
        // Filtro per nome dell'anagrafica
        if ($request->filled('search')) {
            $search = strtolower(str_replace(' ', '', $request->search));
            $query->whereHas('anagrafica', function ($q) use ($search) {
                $q->whereRaw("LOWER(REPLACE(nome, ' ', '')) LIKE ?", ["%{$search}%"]);
            });
        }
    
        // Filtro per categoria
        if ($request->filled('categoria')) {
            $query->whereHas('anagrafica', function ($q) use ($request) {
                $q->where('categoria', $request->categoria);
            });
        }

    $magazzini = $query->get();
    
    // Separazione
    $editori = $magazzini->filter(fn($m) => optional($m->anagrafica)->categoria === 'magazzino editore');
    $altri = $magazzini->reject(fn($m) => optional($m->anagrafica)->categoria === 'magazzino editore');

    // Ordinamento personalizzato
    $altriOrdinati = $altri->sort(function ($a, $b) {
        $aDate = $a->prossima_scadenza ? Carbon::parse($a->prossima_scadenza) : null;
        $bDate = $b->prossima_scadenza ? Carbon::parse($b->prossima_scadenza) : null;

        if (!$aDate && $bDate) return -1;
        if ($aDate && !$bDate) return 1;
        if (!$aDate && !$bDate) {
            $catCmp = strcmp($a->anagrafica->categoria ?? '', $b->anagrafica->categoria ?? '');
            return $catCmp !== 0 ? $catCmp : strcmp($a->anagrafica->nome ?? '', $b->anagrafica->nome ?? '');
        }
        if ($aDate->ne($bDate)) return $aDate->gt($bDate) ? 1 : -1;
        $catCmp = strcmp($a->anagrafica->categoria ?? '', $b->anagrafica->categoria ?? '');
        return $catCmp !== 0 ? $catCmp : strcmp($a->anagrafica->nome ?? '', $b->anagrafica->nome ?? '');
        });

        // Merge e paginazione
        $magazziniFinale = $editori->merge($altriOrdinati)->values();
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $magazziniFinale->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedMagazzini = new LengthAwarePaginator($currentItems, $magazziniFinale->count(), $perPage, $currentPage);
        $paginatedMagazzini->setPath($request->url());
        $paginatedMagazzini->appends($request->query());

    // Passaggio alla view
    return view('magazzini.index', ['magazzini' => $paginatedMagazzini]);
    }
    
    

    public function create()
    {
        $anagrafiche = Anagrafica::all();
        $categorie = ['Distributore', 'Libreria', 'Editore', 'Altro']; // Categorie definite
        return view('magazzini.create', compact('anagrafiche', 'categorie'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anagrafica_id' => 'required|exists:anagraficas,id',
        ]);
    
        Magazzino::create([
            'anagrafica_id' => $request->anagrafica_id,
            'prossima_scadenza' => $request->prossima_scadenza,
        ]);
    
        return redirect()->route('magazzini.index')->with('success', 'Magazzino creato con successo.');
    }
      

    public function update(Request $request, Magazzino $magazzino)
    {
        $request->validate([
            'categoria' => 'required|string',
            'nome' => 'required|string|max:255',
            'anagrafica_id' => 'required|exists:anagraficas,id',
        ]);

        $magazzino->update($request->all());

        return redirect()->route('magazzini.index')->with('success', 'Magazzino aggiornato con successo.');
    }

    public function updateScadenza(Request $request, $id)
    {
    $request->validate([
        'prossima_scadenza' => 'required|date'
    ]);

    $magazzino = Magazzino::findOrFail($id);
    $magazzino->prossima_scadenza = $request->prossima_scadenza;
    $magazzino->save();

    return response()->json(['success' => true]);
    }


    public function destroy($id)
    {
        $magazzino = Magazzino::findOrFail($id);
    
        // Elimina le giacenze associate al magazzino
        $magazzino->giacenze()->delete();
    
        // Elimina il magazzino
        $magazzino->delete();
    
        return redirect()->route('magazzini.index')->with('success', 'Magazzino e giacenze eliminate con successo.');
    }
    
}
