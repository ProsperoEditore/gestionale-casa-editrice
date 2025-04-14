@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Gestione Utenti</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('utenti.create') }}" class="btn btn-primary">➕ Crea nuovo utente</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Ruolo</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($utenti as $utente)
                        <tr>
                            <td>{{ $utente->name }}</td>
                            <td>{{ $utente->email }}</td>
                            <td>{{ ucfirst($utente->ruolo) }}</td>
                            <td class="text-end">
                            @if ($utente->id !== auth()->id())
                                <a href="{{ route('utenti.edit', $utente) }}" class="btn btn-sm btn-outline-primary">Modifica</a>
                                <form action="{{ route('utenti.destroy', $utente) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Sei sicuro?')">Elimina</button>
                                </form>
                            @else
                                <span>–</span>
                            @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
