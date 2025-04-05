<?php

namespace App\Http\Controllers;

use App\Models\MarchioEditoriale;
use Illuminate\Http\Request;

class MarchioEditorialeController extends Controller
{
    public function index()
    {
        $items = MarchioEditoriale::all();
        return view('marchi-editoriali.index', compact('items'));
    }

    public function create()
    {
        return view('marchi-editoriali.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
        ]);

        MarchioEditoriale::create($request->all());

        return redirect()->route('marchi-editoriali.index')->with('success', 'Marchio editoriale aggiunto con successo.');
    }

    public function edit($id)
    {
        $marchio = MarchioEditoriale::findOrFail($id);
        return view('marchi-editoriali.edit', compact('marchio'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required',
        ]);

        MarchioEditoriale::findOrFail($id)->update($request->all());

        return redirect()->route('marchi-editoriali.index')->with('success', 'Marchio editoriale aggiornato con successo.');
    }

    public function destroy($id)
    {
        MarchioEditoriale::findOrFail($id)->delete();
        return redirect()->route('marchi-editoriali.index')->with('success', 'Marchio editoriale eliminato con successo.');
    }
}