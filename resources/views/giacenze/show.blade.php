@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Giacenze</h3>

    <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Giacenze</h2>
    <a href="{{ route('giacenze.create', $magazzino->id) }}" class="btn btn-success">Aggiungi Giacenza</a>
    </div>

    
    <table class="table table-bordered text-center">
    <thead class="thead-dark">
        <tr>
            <th>ISBN</th>
            <th>Titolo</th>
            <th>Quantità</th>
            <th>Prezzo</th>
            @if($magazzino->anagrafica->categoria == 'magazzino editore')
                <th>Costo Produzione</th>
            @else
                <th>Sconto (%)</th>
            @endif
            <th>Data Ultimo Aggiornamento</th>
            <th>Note</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($giacenze as $giacenza)
            <tr>
                <td>{{ $giacenza->libro->isbn }}</td>
                <td>{{ $giacenza->libro->titolo }}</td>
                <td class="@if($giacenza->quantita <= 4) bg-danger 
                           @elseif($giacenza->quantita <= 9) bg-warning 
                           @else bg-success 
                           @endif">
                    {{ $giacenza->quantita }}
                </td>
                <td>{{ number_format($giacenza->prezzo, 2, ',', '.') }} €</td>
                @if($magazzino->anagrafica->categoria == 'magazzino editore')
                    <td>{{ number_format($giacenza->costo_produzione, 2, ',', '.') }} €</td>
                @else
                    <td>{{ $giacenza->sconto }}%</td>
                @endif
                <td>{{ $giacenza->data_ultimo_aggiornamento }}</td>
                <td>{{ $giacenza->note }}</td>
                <td>
                    <a href="{{ route('giacenze.create', ['magazzino' => $magazzino->id]) }}" class="btn btn-success">Aggiungi Giacenza</a>
                    <a href="{{ route('giacenze.edit', $giacenza->id) }}" class="btn btn-warning btn-sm">Modifica</a>
                    <form action="{{ route('giacenze.destroy', $giacenza->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro?')">Elimina</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</div>
@endsection
