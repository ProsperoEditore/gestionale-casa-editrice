@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Crea nuovo utente</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('utenti.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Ruolo</label>
                    <select name="ruolo" class="form-select" required>
                        <option value="utente">Utente</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <h4 class="mt-4 mb-2">Accesso al Menu</h4>
                    <div class="row">
                        @php
                            $voci = [
                                'access_anagrafiche' => 'Anagrafiche',
                                'access_contratti' => 'Contratti',
                                'access_marchi' => 'Marchi Editoriali',
                                'access_libri' => 'Libri',
                                'access_schede_libro' => 'Schede libro',
                                'access_magazzini' => 'Magazzini e Depositi',
                                'access_ordini' => 'Ordini',
                                'access_scarichi' => 'Scarichi',
                                'access_registro_tirature' => 'Registro Tirature',
                                'access_registro_vendite' => 'Registro vendite',
                                'access_report' => 'Report',
                                'access_ritenute' => 'Ritenute',
                                'access_backup' => 'Backup',
                            ];
                        @endphp

                        @foreach($voci as $campo => $etichetta)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="{{ $campo }}" id="{{ $campo }}" value="1"
                                        {{ old($campo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $campo }}">
                                        {{ $etichetta }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>


                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Salva</button>
                    <a href="{{ route('utenti.index') }}" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
