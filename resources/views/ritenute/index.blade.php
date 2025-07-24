@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Elenco Ritenute</h3>

    <a href="{{ route('ritenute.create') }}" class="btn btn-success mb-3">Nuova Ritenuta</a>

    <form action="{{ route('ritenute.index') }}" method="GET" class="d-flex flex-wrap gap-2 mb-4">
        <input type="text" name="autore" value="{{ request('autore') }}" class="form-control" placeholder="Cerca autore..." style="max-width:200px">
        <select name="anno" class="form-select" style="max-width:150px">
            <option value="">Tutti gli anni</option>
            @for($y = now()->year; $y >= 2020; $y--)
                <option value="{{ $y }}" {{ request('anno') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button class="btn btn-outline-primary">Filtra</button>
    </form>

    {{-- DESKTOP --}}
    <div class="d-none d-md-block">
        <table class="table table-bordered table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th>Nota</th>
                    <th>Data</th>
                    <th>Autore</th>
                    <th>Netto</th>
                    <th>R.A.</th>
                    <th>Pagamento netto</th>
                    <th>Pagamento ritenuta</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ritenute as $r)
                    <tr>
                        <td>{{ $r->numero }}</td>
                        <td>{{ $r->data_emissione->format('d/m/Y') }}</td>
                        <td>{{ $r->nome_autore }} {{ $r->cognome_autore }}</td>
                        <td>€ {{ number_format($r->netto_pagare, 2, ',', '.') }}</td>
                        <td>€ {{ number_format($r->ritenuta, 2, ',', '.') }}</td>

                        <td class="{{ $r->data_pagamento_netto ? 'bg-success text-white' : 'bg-warning' }}">
                            <input type="date" value="{{ $r->data_pagamento_netto ? $r->data_pagamento_netto->format('Y-m-d') : '' }}" class="form-control" onchange="salvaPagamento(this, {{ $r->id }}, 'netto')">
                        </td>

                        <td class="{{ $r->data_pagamento_ritenuta ? 'bg-success text-white' : 'bg-warning' }}">
                            <input type="date" value="{{ $r->data_pagamento_ritenuta ? $r->data_pagamento_ritenuta->format('Y-m-d') : '' }}" class="form-control" onchange="salvaPagamento(this, {{ $r->id }}, 'ritenuta')">
                        </td>

                        <td>
                            <a href="{{ route('ritenute.pdf', $r->id) }}" class="btn btn-sm btn-primary" target="_blank">PDF</a>
                            <a href="{{ route('ritenute.edit', $r) }}" class="btn btn-sm btn-warning">Modifica</a>
                            <form action="{{ route('ritenute.destroy', $r) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Elimina</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MOBILE --}}
    <div class="d-block d-md-none">
        @foreach($ritenute as $r)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Nota {{ $r->numero }} - {{ $r->nome_autore }} {{ $r->cognome_autore }}</h5>
                <p><strong>Data:</strong> {{ $r->data_emissione->format('d/m/Y') }}</p>
                <p><strong>Netto:</strong> € {{ number_format($r->netto_pagare, 2, ',', '.') }}</p>
                <p><strong>R.A.:</strong> € {{ number_format($r->ra_20, 2, ',', '.') }}</p>

                <div class="mb-2">
                    <label class="form-label">Pagamento netto</label>
                    <input type="date" value="{{ $r->data_pagamento_netto ? $r->data_pagamento_netto->format('Y-m-d') : '' }}" class="form-control {{ $r->data_pagamento_netto ? 'bg-success text-white' : 'bg-warning' }}" onchange="salvaPagamento(this, {{ $r->id }}, 'netto')">
                </div>

                <div class="mb-2">
                    <label class="form-label">Pagamento ritenuta</label>
                    <input type="date" value="{{ $r->data_pagamento_ritenuta ? $r->data_pagamento_ritenuta->format('Y-m-d') : '' }}" class="form-control {{ $r->data_pagamento_ritenuta ? 'bg-success text-white' : 'bg-warning' }}" onchange="salvaPagamento(this, {{ $r->id }}, 'ritenuta')">
                </div>

                <a href="{{ route('ritenute.pdf', $r->id) }}" class="btn btn-sm btn-primary" target="_blank">PDF</a>
                <a href="{{ route('ritenute.edit', $r) }}" class="btn btn-sm btn-warning">Modifica</a>
                <form action="{{ route('ritenute.destroy', $r) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Elimina</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
function salvaPagamento(input, id, tipo) {
    const data = input.value;
    fetch(`/ritenute/${id}/update-pagamento`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ tipo, data })
    }).then(r => r.json()).then(resp => {
        if (resp.success) input.classList.remove('bg-warning'), input.classList.add('bg-success', 'text-white');
    });
}
</script>
@endsection
