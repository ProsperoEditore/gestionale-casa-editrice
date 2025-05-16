@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Modifica Utente</h2>

    <form method="POST" action="{{ route('utenti.update', $utente) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="name" class="form-control" value="{{ $utente->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $utente->email }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password (lascia vuoto per non modificare)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-4">
            <label class="form-label">Ruolo</label>
            <select name="ruolo" class="form-select" required>
                <option value="utente" {{ $utente->ruolo === 'utente' ? 'selected' : '' }}>Utente</option>
                <option value="admin" {{ $utente->ruolo === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <h4>Accesso al Menu</h4>
@php
    $voci = [
        'access_anagrafiche' => 'Anagrafiche',
        'access_contratti' => 'Contratti',
        'access_marchi' => 'Marchi Editoriali',
        'access_libri' => 'Libri',
        'access_schede_libro' => 'Schede libro',
        'access_magazzini' => 'Magazzini e Depositi',
        'access_ordini' => 'Ordini',
        'access_scarichi' => 'Spedizioni',
        'access_registro_vendite' => 'Registro vendite',
        'access_registro_tirature' => 'Registro tirature',
        'access_report' => 'Report',
        'access_backup' => 'Backup',
    ];
@endphp

@foreach($voci as $campo => $etichetta)
    <label class="d-block">
        <input type="checkbox" name="{{ $campo }}" value="1" {{ $utente->$campo ? 'checked' : '' }}>
        {{ $etichetta }}
    </label>
@endforeach


        <button type="submit" class="btn btn-primary mt-3">Aggiorna</button>
        <a href="{{ route('utenti.index') }}" class="btn btn-secondary mt-3">Annulla</a>
    </form>
</div>
@endsection
