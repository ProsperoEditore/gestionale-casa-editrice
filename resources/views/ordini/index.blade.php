@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Ordini</h3>

    <div class="mb-3">
        <a href="{{ route('ordini.create') }}" class="btn btn-success">Aggiungi Nuovo</a>

        <form action="{{ route('ordini.index') }}" method="GET" style="min-width: 300px;" class="d-flex align-items-center mt-2">
            <select name="search" id="anagrafica_search" class="form-control select2" onchange="this.form.submit()">
                <option value="">Cerca per anagrafica...</option>
                @foreach($tutteAnagrafiche as $anagrafica)
                    <option value="{{ $anagrafica->id }}" {{ request('search') == $anagrafica->id ? 'selected' : '' }}>
                        {{ $anagrafica->nome }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th style="width: 8%;">Codice Ordine</th>
                <th style="width: 10%;">Data</th>
                <th>Tipo ordine</th>
                <th style="width: 22%;">Anagrafica</th>
                <th style="width: 12%;">Pagato</th>
                <th style="width: 14%;">Azioni</th>
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
                        @if(in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
                        <input 
                        type="date" 
                        name="pagato" 
                        class="form-control pagato-input" 
                        data-id="{{ $ordine->id }}" 
                        value="{{ $ordine->pagato }}"
                        style="background-color: {{ $ordine->pagato ? '#28a745' : '#ffc107' }}; color: white; border: none; width: 110px;"
                        onfocus="this.showPicker()" 
                        onmousedown="return false;"
                    >

                        @else
                            <span class="text-muted">ND</span>
                        @endif
                    </td>


                    <td class="align-middle" style="width: 16%;">
                        @if(in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
                            <div class="mb-1">
                                <button class="btn btn-sm btn-primary salva-pagato" data-id="{{ $ordine->id }}">Salva</button>
                            </div>
                        @endif
                        <div class="d-flex gap-1">
                            <a href="{{ route('ordini.edit', $ordine->id) }}" class="btn btn-warning btn-sm">Modifica</a>
                            <form action="{{ route('ordini.destroy', $ordine->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Elimina</button>
                            </form>
                        </div>
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
        {{ $ordini->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('#anagrafica_search').select2({
        placeholder: "Cerca per anagrafica...",
        allowClear: true
    });

    $('.salva-pagato').on('click', function () {
        const id = $(this).data('id');
        const input = $('input.pagato-input[data-id="' + id + '"]');
        const data = input.val();

        $.ajax({
            url: '/ordini/' + id + '/aggiorna-pagato',
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                pagato: data
            },
            success: function () {
                input.css('background-color', data ? '#28a745' : '#ffc107');
                input.css('color', 'white');
            }
            error: function () {
                alert('Errore nel salvataggio del campo Pagato');
            }
        });
    });
});
</script>
@endsection
