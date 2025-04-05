@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Ordini</h3>

    <div class="mb-3">
        <a href="{{ route('ordini.create') }}" class="btn btn-success">Aggiungi Nuovo</a>

    <form action="{{ route('ordini.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cerca per anagrafica...">
        <button class="btn btn-outline-primary">Cerca</button>
    </form>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Codice Ordine</th>
                <th>Data</th>
                <th>Tipo ordine</th>
                <th>Anagrafica</th>
                <th>Azioni</th>
                <th>Visualizza</th>
                <th>Stampa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordini as $ordine)
                <tr>
                    <td>{{ $ordine->codice }}</td>
                    <td>{{ $ordine->data }}</td>
                    <td>{{ ucfirst($ordine->tipo_ordine) }}</td> 
                    <td>{{ $ordine->anagrafica->nome }}</td>
                    <td>
                        <a href="{{ route('ordini.edit', $ordine->id) }}" class="btn btn-warning btn-sm">Modifica</a>
                        <form action="{{ route('ordini.destroy', $ordine->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Elimina</button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('ordini.gestione_libri', $ordine->id) }}" class="btn btn-info">Visualizza</a>
                    </td>

                    <td>
                        <a href="{{ route('ordini.stampa', $ordine->id) }}" class="btn btn-primary btn-sm">Stampa</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-4">
    {{ $ordini->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
</div>
@endsection
