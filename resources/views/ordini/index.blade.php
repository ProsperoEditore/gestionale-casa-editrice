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

    <div class="table-responsive">
    <table class="table table-bordered text-center align-middle" style="table-layout: fixed; width: 100%;">
        <thead class="table-dark">
        <tr>
            <th style="width: 8%;">Codice Ordine</th>
            <th style="width: 10%;">Data</th>
            <th style="width: 12%;">Tipo ordine</th>
            <th style="width: 24%;">Anagrafica</th>
            <th style="width: 14%;">Pagato</th>
            <th style="width: 14%;">Azioni</th>
            <th style="width: 9%;">Visualizza</th>
            <th style="width: 9%;">Stampa</th>
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
                            value="{{ $ordine->pagato ? \Carbon\Carbon::parse($ordine->pagato)->format('Y-m-d') : '' }}"
                            style="background-color: {{ $ordine->pagato ? '#28a745' : '#ffc107' }}; color: white; border: none;"
                        >
                        @else
                            <span class="text-muted">ND</span>
                        @endif
                    </td>

                    <td>
                        @if(in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
                            <button class="btn p-0 border-0 bg-transparent text-primary mb-1 salva-pagato" data-id="{{ $ordine->id }}" title="Salva">
                                <i class="bi bi-check-lg fs-5"></i>
                            </button>
                        @endif
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('ordini.edit', $ordine->id) }}" class="text-warning" title="Modifica">
                                <i class="bi bi-pencil fs-5"></i>
                            </a>
                            <form action="{{ route('ordini.destroy', $ordine->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn p-0 border-0 bg-transparent text-danger" title="Elimina">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </form>
                        </div>
                    </td>

                    <td>
                        <a href="{{ route('ordini.gestione_libri', $ordine->id) }}" class="text-info" title="Visualizza">
                            <i class="bi bi-eye fs-5"></i>
                        </a>
                    </td>

                    <td>
                        <a href="{{ route('ordini.stampa', $ordine->id) }}" class="text-dark" title="Stampa">
                            <i class="bi bi-printer fs-5"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>

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

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.salva-pagato').on('click', function () {
        const id = $(this).data('id');
        const input = $('input.pagato-input[data-id="' + id + '"]');
        const data = input.val();

        $.ajax({
            url: '/ordini/' + id + '/aggiorna-pagato',
            method: 'PUT',
            data: {
                pagato: data
            },
            success: function () {
                input.css('background-color', data ? '#28a745' : '#ffc107');
                input.css('color', 'white');
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
                alert('Errore nel salvataggio del campo Pagato');
            }
        });
    });
});
</script>
@endsection
