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
                    <form action="{{ route('scarichi.updateInfoSpedizione', $item->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <td>
                            <select name="stato" class="form-select"
                                    style="background-color:
                                        {{ $item->stato === 'Spedito' ? '#51cf66' :
                                           ($item->stato === 'In attesa' ? '#ffe066' : '#ff6b6b') }};
                                    ">
                                <option value="">selezionare uno stato</option>
                                <option value="In attesa" {{ $item->stato === 'In attesa' ? 'selected' : '' }}>In attesa</option>
                                <option value="Spedito" {{ $item->stato === 'Spedito' ? 'selected' : '' }}>Spedito</option>
                            </select>
                        </td>
                        <td>
                            {{ $item->data_stato_info ? \Carbon\Carbon::parse($item->data_stato_info)->format('d/m/Y') : '' }}
                        </td>
                        <td class="d-flex">
                            <input type="text" name="info_spedizione" class="form-control me-2" value="{{ $item->info_spedizione }}">
                            <button type="submit" class="btn btn-primary btn-sm">Salva</button>
                        </td>
                    </form>
                    <td>
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
            success: function () {
                let bgColor = '#f8d7da'; // rosso di default
                if (stato === 'Spedito') bgColor = '#d4edda';     // verde
                else if (stato === 'In attesa') bgColor = '#fff3cd'; // giallo

                select.css('background-color', bgColor);

                // Aggiorna la pagina per ricaricare la colonna data
                location.reload();
            },
            error: function () {
                alert("Errore nel salvataggio dello stato.");
            }
        });
    });
});
</script>

@endsection