<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\Libro;
use App\Models\Contratto;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::query()
            ->join('libri', 'libri.id', '=', 'reports.libro_id')  
            ->select('reports.*', 'libri.titolo'); 
    
            if ($request->filled('search')) {
                $searchTerm = strtolower(str_replace(' ', '', $request->input('search')));
                $query->whereRaw("LOWER(REPLACE(libri.titolo, ' ', '')) LIKE ?", ["%{$searchTerm}%"]);
            }
            
    
        $items = $query->latest()->paginate(30)->appends($request->query());
        return view('report.index', compact('items'));
    }
    
    
    

    public function create()
    {
        $contratti = Contratto::all();
        return view('report.create', compact('contratti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'data_creazione' => 'required|date',
            'libro_id' => 'required|exists:libri,id',
            'contratto_id' => 'required|exists:contratti,id',
        ]);

        Report::create($request->all());

        return redirect()->route('report.index')->with('success', 'Report creato con successo.');
    }

    public function edit($id)
    {
        $report = Report::findOrFail($id);
        return view('report.edit', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'data_creazione' => 'required|date',
            'libro_id' => 'required|exists:libri,id',
        ]);

        Report::findOrFail($id)->update($request->all());

        return redirect()->route('report.index')->with('success', 'Report aggiornato con successo.');
    }

    public function destroy($id)
    {
        Report::findOrFail($id)->delete();
        return redirect()->route('report.index')->with('success', 'Report eliminato con successo.');
    }

    public function autocompleteLibro(Request $request)
    {
        $search = $request->input('query');
    
        $libri = Libro::where(function ($query) use ($search) {
                $query->whereRaw("LOWER(titolo) LIKE ?", ['%' . strtolower($search) . '%'])
                      ->orWhereRaw("LOWER(isbn) LIKE ?", ['%' . strtolower($search) . '%']);
            })
            ->orderByRaw("CASE 
                WHEN LOWER(titolo) LIKE ? THEN 0 
                WHEN LOWER(titolo) LIKE ? THEN 1 
                ELSE 2 END", [strtolower($search) . '%', '%' . strtolower($search) . '%'])
            ->orderBy('titolo')
            ->select('id', 'titolo', 'isbn', 'prezzo')
            ->take(50)
            ->get();
    
        return response()->json($libri->map(function ($libro) {
            return [
                'id' => $libro->id,
                'titolo' => $libro->titolo,
                'isbn' => $libro->isbn,
                'prezzo' => $libro->prezzo,
            ];
        }));
    }
    
    
        

}