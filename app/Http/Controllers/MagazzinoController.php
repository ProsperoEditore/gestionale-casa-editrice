<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use App\Models\Magazzino;
use App\Models\Anagrafica;
use Carbon\Carbon;
use App\Models\RendicontoEmailLog;

class MagazzinoController extends Controller
{
    public function index(Request $request)
    {
        $query = Magazzino::with('anagrafica');
    
        // Filtro per nome dell'anagrafica
        if ($request->filled('search')) {
            $search = strtolower(str_replace(' ', '', $request->search));
            $query->whereHas('anagrafica', function ($q) use ($search) {
                $q->whereRaw("
                    LOWER(REPLACE(
                        CONCAT(
                            COALESCE(denominazione, ''),
                            COALESCE(nome, ''),
                            COALESCE(cognome, '')
                        ), ' ', '')
                    ) LIKE ?
                ", ["%{$search}%"]);
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
            return $catCmp !== 0 ? $catCmp : strcmp($a->anagrafica->nome_completo ?? '', $b->anagrafica->nome_completo ?? '');
        }
        if ($aDate->ne($bDate)) return $aDate->gt($bDate) ? 1 : -1;
        $catCmp = strcmp($a->anagrafica->categoria ?? '', $b->anagrafica->categoria ?? '');
        return $catCmp !== 0 ? $catCmp : strcmp($a->anagrafica->nome_completo ?? '', $b->anagrafica->nome_completo ?? '');
        });

        // Merge e paginazione
        $magazziniFinale = $editori->merge($altriOrdinati)->values();
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $magazziniFinale->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedMagazzini = new LengthAwarePaginator($currentItems, $magazziniFinale->count(), $perPage, $currentPage);
        $paginatedMagazzini->setPath($request->url());
        $paginatedMagazzini->appends($request->query());

        $inviati = RendicontoEmailLog::orderBy('created_at', 'desc')->get()
            ->groupBy('magazzino_id')
            ->map(function ($logs) {
                return $logs->take(3)->map(function ($log) {
                    return $log->created_at->format('d-m-y');
                })->implode(', ');
            });

        // Passaggio alla view
        return view('magazzini.index', [
            'magazzini' => $paginatedMagazzini,
            'inviati' => $inviati
        ]);
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

    

public function inviaRendiconto($id)
{
    $magazzino = \App\Models\Magazzino::with('anagrafica')->findOrFail($id);
    $email = $magazzino->anagrafica->email;
    $nome = $magazzino->anagrafica->nome_completo;

    if (!$email) {
        return back()->with('error', 'Nessun indirizzo email disponibile per questo magazzino.');
    }

    $profilo = \App\Models\Profilo::first();
    $mittenteEmail = $profilo->email ?? config('mail.from.address');
    $mittenteNome = $profilo->denominazione ?? config('mail.from.name');

    try {
        Mail::send('emails.richiesta_rendiconto', [
            'nome' => $nome,
            'profilo' => $profilo,
        ], function ($message) use ($email, $mittenteEmail, $mittenteNome) {
            $message->to($email)
                    ->from($mittenteEmail, $mittenteNome)
                    ->subject('Richiesta invio rendiconto');
        });

                // ✅ Salva log
        \App\Models\RendicontoEmailLog::create([
            'magazzino_id' => $magazzino->id,
            'email' => $email,
        ]);

        return back()->with('success', 'Email inviata con successo.');
    } catch (\Exception $e) {
        return back()->with('error', 'Errore durante l’invio dell’email: ' . $e->getMessage());
    }
}


    
}
