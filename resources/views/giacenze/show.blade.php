@extends('layouts.app')

@section('content')
<div class="container mt-5">
    {{-- TITOLI --}}
    <h3 class="text-center mb-4 d-none d-md-block">Giacenze</h3>
    <h4 class="text-center mb-4 d-block d-md-none">Giacenze</h4>

    {{-- HEADER AZIONI --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="d-none d-md-block">Giacenze</h2>
        <a href="{{ route('giacenze.create', $magazzino->id) }}" class="btn btn-success">Aggiungi Giacenza</a>
    </div>

    {{-- VERSIONE DESKTOP --}}
    <div class="d-none d-md-block">
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
                                   @endif text-white">
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
                            <a href="{{ route('giacenze.edit', $giacenza->id) }}" class="btn btn-warning btn-sm">Modifica</a>
                            <form action="{{ route('giacenze.destroy', $giacenza->id) }}" method="POST" class="d-inline">
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

    {{-- VERSIONE MOBILE --}}
    <div class="d-block d-md-none">
        @foreach($giacenze as $giacenza)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ $giacenza->libro->titolo }}</h5>
                    <p><strong>ISBN:</strong> {{ $giacenza->libro->isbn }}</p>
                    <p>
                        <strong>Quantità:</strong>
                        <span class="badge 
                            @if($giacenza->quantita <= 4) bg-danger
                            @elseif($giacenza->quantita <= 9) bg-warning
                            @else bg-success
                            @endif text-white">
                            {{ $giacenza->quantita }}
                        </span>
                    </p>
                    <p><strong>Prezzo:</strong> {{ number_format($giacenza->prezzo, 2, ',', '.') }} €</p>
                    @if($magazzino->anagrafica->categoria == 'magazzino editore')
                        <p><strong>Costo Produzione:</strong> {{ number_format($giacenza->costo_produzione, 2, ',', '.') }} €</p>
                    @else
                        <p><strong>Sconto:</strong> {{ $giacenza->sconto }}%</p>
                    @endif
                    <p><strong>Aggiornamento:</strong> {{ $giacenza->data_ultimo_aggiornamento }}</p>
                    <p><strong>Note:</strong> {{ $giacenza->note }}</p>

                    <div class="d-flex gap-2 flex-wrap mt-2">
                        <a href="{{ route('giacenze.edit', $giacenza->id) }}" class="btn btn-warning btn-sm">Modifica</a>
                        <form action="{{ route('giacenze.destroy', $giacenza->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro?')">Elimina</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
