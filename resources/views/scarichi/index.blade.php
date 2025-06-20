@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Spedizioni</h3>

<div class="mb-3">
    <a href="{{ route('scarichi.create') }}" class="btn btn-success">Aggiungi Nuovo</a>

    <form action="{{ route('scarichi.index') }}" method="GET" style="min-width: 300px;" class="d-flex align-items-center mt-2">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cerca destinatario...">
        <button class="btn btn-outline-primary ms-2" type="submit">Cerca</button>
    </form>
</div>


<!-- VISUALIZZAZIONE DESKTOP -->
<div class="d-none d-md-block table-responsive">
    <table class="table table-bordered text-center align-middle text-nowrap">
        <thead class="table-dark">
            <tr>
                <th>Destinatario</th>
                <th>Ordine</th>
                <th>Stato</th>
                <th>Info consegna</th>
                <th>Data stato/info</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scarichi as $item)
                <tr>
                    <td>{{ $item->anagrafica->nome_completo ?? $item->destinatario_nome }}</td>
                    <td>{{ $item->ordine->codice ?? $item->altro_ordine }}</td>
                    <td>
                        <form action="{{ route('scarichi.updateInfoSpedizione', $item->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('PATCH')
                            <select name="stato" class="form-select form-select-sm stato-scarico"
                                    data-id="{{ $item->id }}"
                                    style="background-color:
                                        {{ $item->stato === 'Spedito' ? '#51cf66' :
                                           ($item->stato === 'In attesa' ? '#ffe066' : '#ff6b6b') }};">
                                <option value="">seleziona...</option>
                                <option value="In attesa" {{ $item->stato === 'In attesa' ? 'selected' : '' }}>In attesa</option>
                                <option value="Spedito" {{ $item->stato === 'Spedito' ? 'selected' : '' }}>Spedito</option>
                            </select>
                    </td>
                    <td>
                        <input type="text" name="info_spedizione" class="form-control form-control-sm" value="{{ $item->info_spedizione }}">
                    </td>
                    <td class="data-stato-info">
                        {{ $item->data_stato_info ? \Carbon\Carbon::parse($item->data_stato_info)->format('d/m/Y') : 'N.D.' }}
                    </td>
                        <td class="d-flex justify-content-center gap-2">
                            <a href="{{ route('scarichi.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save"></i>
                            </button>
                        </form>

                        <form action="{{ route('scarichi.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Eliminare la spedizione?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- VISUALIZZAZIONE MOBILE -->
<div class="d-block d-md-none">
    <div class="row">
        @foreach($scarichi as $item)
        <div class="col-12 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-2">{{ $item->anagrafica->nome_completo ?? $item->destinatario_nome }}</h5>

                    <p class="mb-1"><strong>Ordine:</strong> {{ $item->ordine->codice ?? $item->altro_ordine }}</p>

                    <form action="{{ route('scarichi.updateInfoSpedizione', $item->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-2">
                            <label class="form-label">Stato</label>
                            <select name="stato" class="form-select stato-scarico"
                                    data-id="{{ $item->id }}"
                                    style="background-color:
                                        {{ $item->stato === 'Spedito' ? '#51cf66' :
                                           ($item->stato === 'In attesa' ? '#ffe066' : '#ff6b6b') }};">
                                <option value="">seleziona...</option>
                                <option value="In attesa" {{ $item->stato === 'In attesa' ? 'selected' : '' }}>In attesa</option>
                                <option value="Spedito" {{ $item->stato === 'Spedito' ? 'selected' : '' }}>Spedito</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Info consegna</label>
                            <input type="text" name="info_spedizione" class="form-control" value="{{ $item->info_spedizione }}">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">Data:
                                <span class="data-stato-info">
                                    {{ $item->data_stato_info ? \Carbon\Carbon::parse($item->data_stato_info)->format('d/m/Y') : 'N.D.' }}
                                </span>
                            </small>

                            <div class="d-flex gap-2">
                                <a href="{{ route('scarichi.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-save"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <form action="{{ route('scarichi.destroy', $item->id) }}" method="POST" class="d-inline mt-2 ms-auto text-end">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>


    <div class="d-flex justify-content-center mt-4">
        {{ $scarichi->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>

<style>
@media (max-width: 768px) {
    .card-title {
        font-size: 16px;
    }

    .form-control, .form-select {
        font-size: 14px;
    }

    .btn-sm {
        font-size: 13px;
    }

    .container {
        padding: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('.stato-scarico').on('change', function () {
        const stato = $(this).val();
        const id = $(this).data('id');
        const select = $(this);

        $.ajax({
            url: `/scarichi/${id}/update-stato`,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                stato: stato
            },
            success: function (data) {
                let bgColor = '#f8d7da'; // rosso
                if (stato === 'Spedito') bgColor = '#51cf66';
                else if (stato === 'In attesa') bgColor = '#ffe066';

                select.css('background-color', bgColor);

                const parentCard = select.closest('.card');
                parentCard.find('.data-stato-info').text(new Date().toLocaleDateString('it-IT'));
            },
            error: function () {
                alert("Errore nel salvataggio dello stato.");
            }
        });
    });
});
</script>
@endsection
