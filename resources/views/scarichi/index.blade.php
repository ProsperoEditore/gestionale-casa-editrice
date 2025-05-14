@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Spedizioni</h3>

    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('scarichi.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Aggiungi</span>
        </a>

        <form action="{{ route('scarichi.index') }}" method="GET" class="d-flex flex-wrap gap-2" style="max-width: 100%;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cerca destinatario...">
            <button class="btn btn-outline-primary">
                <i class="bi bi-search"></i> <span class="d-none d-md-inline">Cerca</span>
            </button>
        </form>
    </div>

    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Destinatario</th>
                    <th>Ordine Associato</th>
                    <th>Stato</th>
                    <th>Data stato/info</th>
                    <th style="width: 40%;">Info consegna</th>
                    <th style="width: 120px;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scarichi as $item)
                <tr>
                    <td>{{ $item->anagrafica->nome ?? $item->destinatario_nome }}</td>
                    <td>{{ $item->ordine->codice ?? $item->altro_ordine }}</td>

                    {{-- Stato --}}
                    <td>
                        <select name="stato"
                            class="form-select stato-scarico"
                            data-id="{{ $item->id }}"
                            style="background-color:
                                {{ $item->stato === 'Spedito' ? '#51cf66' :
                                ($item->stato === 'In attesa' ? '#ffe066' : '#ff6b6b') }};">
                            <option value="">selezionare uno stato</option>
                            <option value="In attesa" {{ $item->stato === 'In attesa' ? 'selected' : '' }}>In attesa</option>
                            <option value="Spedito" {{ $item->stato === 'Spedito' ? 'selected' : '' }}>Spedito</option>
                        </select>
                    </td>

                    {{-- Data stato --}}
                    <td>
                        {{ $item->data_stato_info ? \Carbon\Carbon::parse($item->data_stato_info)->format('d/m/Y') : '' }}
                    </td>

                    {{-- Info spedizione --}}
                    <td>
                        <form action="{{ route('scarichi.updateInfoSpedizione', $item->id) }}" method="POST" class="d-flex flex-wrap gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="info_spedizione" class="form-control me-2" value="{{ $item->info_spedizione }}">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save"></i>
                            </button>
                        </form>
                    </td>

                    {{-- Elimina --}}
                    <td>
                        <form action="{{ route('scarichi.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $scarichi->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>


<style>
@media (max-width: 768px) {
    .table th, .table td {
        font-size: 12px;
        padding: 6px;
    }

    .btn-sm {
        padding: 4px 6px;
        font-size: 12px;
    }

    .form-control {
        font-size: 14px;
    }

    h3 {
        font-size: 18px;
    }

    .container {
        padding: 10px;
    }

    form.d-flex {
        flex-direction: column;
    }

    form.d-flex input,
    form.d-flex button {
        width: 100%;
        margin-bottom: 10px;
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
                // Aggiorna solo il colore dello sfondo
                let bgColor = '#f8d7da'; // rosso
                if (stato === 'Spedito') bgColor = '#51cf66';
                else if (stato === 'In attesa') bgColor = '#ffe066';

                select.css('background-color', bgColor);

                // Aggiorna la data in tempo reale
                const row = select.closest('tr');
                const dataCell = row.find('td').eq(3); // la cella "Data stato/info"
                const today = new Date().toLocaleDateString('it-IT');
                dataCell.text(today);
            },
            error: function () {
                alert("Errore nel salvataggio dello stato.");
            }
        });
    });
});

</script>
@endsection
