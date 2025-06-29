@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">Scarichi da approvare</h3>
    <a href="{{ route('scarichi-richiesti.exportPdf') }}" class="btn btn-primary mb-3">🖨️ Stampa</a>


    @if($richieste->isEmpty())
        <div class="alert alert-info">Nessuna richiesta di scarico in attesa.</div>
        <div class="text-center mt-3">
            <a href="{{ route('magazzini.index') }}" class="btn btn-secondary">← Torna a Magazzini</a>
        </div>
@else

    {{-- DESKTOP --}}
    <div class="d-none d-md-block">
        <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Ordine</th>
                    <th>Destinatario</th>
                    <th>ISBN</th>
                    <th>Titolo</th>
                    <th>Magazzino</th>
                    <th>Giacenza attuale</th>
                    <th>Quantità richiesta</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($richieste as $r)
                    <tr>
                        <td>{{ $r->ordine->codice }}</td>
                        <td>{{ $r->destinatario ?? 'N/D' }}</td>
                        <td>{{ $r->libro->isbn }}</td>
                        <td>{{ $r->libro->titolo }}</td>
                        <td>{{ $r->magazzino_nome ?? 'N/D' }}</td>
                        <td>{{ $r->quantita_disponibile ?? 'N/D' }}</td>
                        <td><strong style="color: red">{{ $r->quantita }}</strong></td>
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
    </div>

    {{-- MOBILE --}}
    <div class="d-md-none">
        @foreach($richieste as $r)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Ordine: {{ $r->ordine->codice }}</h5>
                    <p class="mb-1"><strong>Destinatario:</strong> {{ $r->destinatario ?? 'N/D' }}</p>
                    <p class="mb-1"><strong>ISBN:</strong> {{ $r->libro->isbn }}</p>
                    <p class="mb-1"><strong>Titolo:</strong> {{ $r->libro->titolo }}</p>
                    <p class="mb-1"><strong>Magazzino:</strong> {{ $r->magazzino_nome ?? 'N/D' }}</p>
                    <p class="mb-1"><strong>Giacenza attuale:</strong> {{ $r->quantita_disponibile ?? 'N/D' }}</p>
                    <p class="mb-2"><strong>Quantità richiesta:</strong> <span style="color: red">{{ $r->quantita }}</span></p>

                    <div class="d-flex justify-content-between">
                        <form action="{{ route('scarichi-richiesti.approva', $r->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-success btn-sm">✅ Approva</button>
                        </form>
                        <form action="{{ route('scarichi-richiesti.rifiuta', $r->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-danger btn-sm">❌ Rifiuta</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endif

</div>
@endsection
