@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">Scarichi da approvare</h3>

    @if($richieste->isEmpty())
        <div class="alert alert-info">Nessuna richiesta di scarico in attesa.</div>
        <div class="text-center mt-3">
            <a href="{{ route('magazzini.index') }}" class="btn btn-secondary">← Torna a Magazzini</a>
        </div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Ordine</th>
                    <th>ISBN</th>
                    <th>Titolo</th>
                    <th>Quantità richiesta</th>
                    <th>Magazzino</th>
                    <th>Giacenza attuale</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($richieste as $r)
                    <tr>
                        <td>{{ $r->ordine->codice }}</td>
                        <td>{{ $r->libro->isbn }}</td>
                        <td>{{ $r->libro->titolo }}</td>
                        <td>{{ $r->quantita }}</td>
                        <td>{{ $r->magazzino_individuato?->nome ?? $r->magazzino_individuato?->anagrafica?->nome ?? 'N/D' }}</td>
                        <td>{{ $r->quantita_disponibile ?? 'N/D' }}</td>
                        <td>
                            <form action="{{ route('scarichi-richiesti.approva', $r->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm">Approva</button>
                            </form>
                            <form action="{{ route('scarichi-richiesti.rifiuta', $r->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-danger btn-sm">Rifiuta</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
