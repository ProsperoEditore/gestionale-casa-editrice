@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Spedizioni</h3>

    <a href="{{ route('scarichi.create') }}" class="btn btn-success mb-3">Aggiungi Nuovo</a>
    <form action="{{ route('scarichi.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cerca per destinatario...">
        <button class="btn btn-outline-primary">Cerca</button>
    </form>

    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>Destinatario</th>
                <th>Ordine Associato</th>
                <th style="width: 40%;">Info consegna</th>
                <th style="width: 120px;">Azioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scarichi as $item)
                <tr>
                    <td>{{ $item->anagrafica->nome ?? $item->destinatario_nome }}</td>
                    <td>{{ $item->ordine->codice ?? $item->altro_ordine }}</td>
                    <td>
                        <form action="{{ route('scarichi.updateInfoSpedizione', $item->id) }}" method="POST" class="d-flex">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="info_spedizione" class="form-control me-2" value="{{ $item->info_spedizione }}">
                            <button type="submit" class="btn btn-primary btn-sm">Salva</button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('scarichi.edit', $item->id) }}" class="btn btn-warning btn-sm">Modifica</a>
                        <form action="{{ route('scarichi.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Elimina</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-4">
    {{ $scarichi->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>

</div>
@endsection
