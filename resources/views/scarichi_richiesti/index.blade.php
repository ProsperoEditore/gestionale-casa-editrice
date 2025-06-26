@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">Scarichi da approvare</h3>

    @if($richieste->isEmpty())
        <div class="alert alert-info">Nessuna richiesta di scarico in attesa.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Ordine</th>
                    <th>ISBN</th>
                    <th>Titolo</th>
                    <th>Quantità</th>
                    <th>Magazzino</th>
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
                        <td>
    @if($r->magazzino)
        ID: {{ $r->magazzino->id }}<br>

        Anagrafica ID: {{ $r->magazzino->anagrafica_id ?? 'null' }}<br>

        Nome Anagrafica: {{ optional($r->magazzino->anagrafica)->nome ?? 'N/A' }}<br>

        Nome Magazzino: {{ $r->magazzino->nome ?? 'N/A' }}
    @else
        Magazzino non trovato
    @endif
</td>


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
