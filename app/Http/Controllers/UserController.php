<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;



class UserController extends Controller
{
    // Solo per admin
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->ruolo !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $utenti = User::all();
        return view('utenti.index', compact('utenti'));
    }

    public function create()
    {
        return view('utenti.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'ruolo' => 'required',
        ]);
    
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->ruolo = $request->ruolo;
    
        // Imposta i permessi personalizzati (se ci sono nel form)
        $user->access_anagrafiche = $request->has('access_anagrafiche');
        $user->access_contratti = $request->has('access_contratti');
        $user->access_marchi = $request->has('access_marchi');
        $user->access_libri = $request->has('access_libri');
        $user->access_magazzini = $request->has('access_magazzini');
        $user->access_ordini = $request->has('access_ordini');
        $user->access_scarichi = $request->has('access_scarichi');
        $user->access_registro_vendite = $request->has('access_registro_vendite');
        $user->access_report = $request->has('access_report');
    
        $user->save();
    
        return redirect()->route('utenti.index')->with('success', 'Utente creato');
    }
    

    public function destroy(User $utente)
    {
        $utente->delete();
        return redirect()->route('utenti.index')->with('success', 'Utente eliminato');
    }

    public function edit(User $utente)
{
    return view('utenti.edit', compact('utente'));
}

public function update(Request $request, User $utente)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email,' . $utente->id,
        'ruolo' => 'required',
    ]);

    $utente->name = $request->name;
    $utente->email = $request->email;
    $utente->ruolo = $request->ruolo;

    if ($request->filled('password')) {
        $utente->password = Hash::make($request->password);
    }

    $permessi = [
        'access_anagrafiche',
        'access_contratti',
        'access_marchi',
        'access_libri',
        'access_magazzini',
        'access_ordini',
        'access_scarichi',
        'access_registro_vendite',
        'access_registro_tirature',
        'access_report',
    ];

    foreach ($permessi as $campo) {
        $utente->$campo = $request->has($campo);
    }

    $utente->save();

    return redirect()->route('utenti.index')->with('success', 'Utente aggiornato');
}

}
