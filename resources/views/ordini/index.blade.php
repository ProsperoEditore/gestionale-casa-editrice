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

<!-- VISUALIZZAZIONE DESKTOP -->
<div class="d-none d-md-block table-responsive">
    <table class="table table-bordered text-center align-middle text-nowrap">
        <thead class="table-dark">
        <tr>
            <th>Codice Ordine</th>
            <th>Data</th>
            <th>Tipo ordine</th>
            <th>Anagrafica</th>
            <th>Pagato</th>
            <th>Azioni</th>
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

                    <td class="align-middle">
                        <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap text-nowrap">

                            @if(in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
                                <button class="btn p-0 border-0 bg-transparent text-primary salva-pagato" data-id="{{ $ordine->id }}" title="Salva">
                                    <i class="bi bi-save fs-4"></i>
                                </button>
                            @endif

                            <a href="{{ route('ordini.edit', $ordine->id) }}" class="text-warning" title="Modifica">
                                <i class="bi bi-pencil fs-4"></i>
                            </a>

                            <form action="{{ route('ordini.destroy', $ordine->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn p-0 border-0 bg-transparent text-danger" title="Elimina">
                                    <i class="bi bi-trash fs-4"></i>
                                </button>
                            </form>

                            <a href="{{ route('ordini.gestione_libri', $ordine->id) }}" class="text-info" title="Visualizza">
                                <i class="bi bi-eye fs-4"></i>
                            </a>

                            <a href="{{ route('ordini.stampa', $ordine->id) }}" class="text-dark" title="Stampa">
                                <i class="bi bi-printer fs-4"></i>
                            </a>

                            @if(in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
                            <a href="{{ route('ordini.esportaXML', $ordine->id) }}" class="text-primary" title="Esporta XML" target="_blank">
                                <i class="bi bi-file-earmark-code fs-4"></i>
                            </a>
                            @endif
                        </div>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
    </div>


<!-- VISUALIZZAZIONE MOBILE -->
<div class="d-md-none">
    @foreach($ordini as $ordine)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $ordine->codice }} - {{ ucfirst($ordine->tipo_ordine) }}</h5>
                <p class="mb-1"><strong>Data:</strong> {{ $ordine->data }}</p>
                <p class="mb-1"><strong>Anagrafica:</strong> {{ $ordine->anagrafica->nome }}</p>
                <p class="mb-1"><strong>Pagato:</strong>
                    @if(in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
                        <input type="date" class="form-control pagato-input"
                            data-id="{{ $ordine->id }}"
                            value="{{ $ordine->pagato ? \Carbon\Carbon::parse($ordine->pagato)->format('Y-m-d') : '' }}"
                            style="background-color: {{ $ordine->pagato ? '#28a745' : '#ffc107' }}; color: white; border: none;" />
                    @else
                        <span class="text-muted">ND</span>
                    @endif
                </p>
                <div class="d-flex flex-wrap gap-3 mt-2">
                    @if(in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
                        <button class="btn btn-sm btn-primary salva-pagato" data-id="{{ $ordine->id }}" title="Salva">
                            <i class="bi bi-save"></i>
                        </button>
                    @endif

                    <a href="{{ route('ordini.edit', $ordine->id) }}" class="btn btn-sm btn-warning" title="Modifica">
                        <i class="bi bi-pencil"></i>
                    </a>

                    <form action="{{ route('ordini.destroy', $ordine->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Elimina">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>

                    <a href="{{ route('ordini.gestione_libri', $ordine->id) }}" class="btn btn-sm btn-info" title="Visualizza">
                        <i class="bi bi-eye"></i>
                    </a>

                    <a href="{{ route('ordini.stampa', $ordine->id) }}" class="btn btn-sm btn-dark" title="Stampa">
                        <i class="bi bi-printer"></i>
                    </a>
      
                    @if(in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
                    <a href="{{ route('ordini.esportaXML', $ordine->id) }}" class="btn btn-sm btn-primary" title="Esporta XML" target="_blank">
                        <i class="bi bi-file-earmark-code"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
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
