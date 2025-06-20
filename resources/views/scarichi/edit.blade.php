@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Modifica Spedizione</h3>

    <div class="card">
        <div class="card-body">
        <form action="{{ route('scarichi.update', ['scarichi' => $scarico->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Ordine associato -->
                <div class="mb-3">
                    <label class="form-label">Ordine Associato (facoltativo)</label>
                    <select name="ordine_id" id="ordine_id" class="form-select" style="width: 100%;">
                        @if($scarico->ordine)
                            <option value="{{ $scarico->ordine->id }}" selected
                                data-anagrafica-id="{{ $scarico->ordine->anagrafica->id }}"
                                data-nome-cliente="{{ $scarico->ordine->anagrafica->nome_completo }}">
                                {{ $scarico->ordine->codice }} - {{ $scarico->ordine->anagrafica->nome_completo }}
                            </option>
                        @endif
                    </select>
                </div>

                <!-- Altro ordine -->
                <div class="mb-3">
                    <label class="form-label">Altro Ordine</label>
                    <input type="text" name="altro_ordine" id="altro_ordine" class="form-control"
                           value="{{ $scarico->altro_ordine }}"
                           autocomplete="off"
                           @if($scarico->ordine_id) disabled @endif>
                </div>

                <!-- Destinatario -->
                <div class="mb-3">
                    <label class="form-label">Destinatario</label>
                    <input type="text" name="destinatario_nome" id="destinatario_nome" class="form-control"
                           value="{{ $scarico->anagrafica->nome_completo ?? $scarico->destinatario_nome }}"
                           @if($scarico->ordine_id) readonly @endif>
                    <input type="hidden" name="anagrafica_id" id="anagrafica_id" value="{{ old('anagrafica_id', $scarico->anagrafica_id ?? '') }}">
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success">Aggiorna</button>
                    <a href="{{ route('scarichi.index') }}" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    console.log("EDIT SCRIPT ATTIVO ✅");

    const ordineSelect = document.getElementById('ordine_id');
    const altroOrdineInput = document.getElementById('altro_ordine');
    const destinatarioNome = document.getElementById('destinatario_nome');
    const anagraficaId = document.getElementById('anagrafica_id');

    $('#ordine_id').select2({
        placeholder: '-- Cerca ordine esistente --',
        ajax: {
            url: '{{ route('scarichi.autocomplete-ordini') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { query: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.codice + ' - ' + item.nome_cliente,
                            anagrafica_id: item.anagrafica_id,
                            nome_cliente: item.nome_cliente
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1,
        width: '100%'
    }).on('select2:select', function (e) {
        const data = e.params.data;

        altroOrdineInput.value = '';
        altroOrdineInput.disabled = true;

        destinatarioNome.value = data.nome_cliente || '';
        destinatarioNome.readOnly = true;
        anagraficaId.value = data.anagrafica_id || '';

        console.log("✅ anagrafica_id aggiornato:", anagraficaId.value);
    });

altroOrdineInput.addEventListener('input', function () {
    if (this.value.trim() !== '') {
        $('#ordine_id').val(null).trigger('change');
        ordineSelect.disabled = true;

        destinatarioNome.readOnly = false;

        // Solo se destinatario è stato compilato automaticamente lo azzero
        // (es. se readonly o è uguale al vecchio ordine)
        if (destinatarioNome.readOnly || destinatarioNome.value === '{{ $scarico->ordine->anagrafica->nome_completo ?? '' }}') {
            destinatarioNome.value = '';
        }

        anagraficaId.value = '';
    } else {
        ordineSelect.disabled = false;
    }
});


    // Per ricaricare select2 con dati salvati
    @if($scarico->ordine)
        const option = new Option(
            '{{ $scarico->ordine->codice }} - {{ $scarico->ordine->anagrafica->nome_completo }}',
            '{{ $scarico->ordine->id }}',
            true,
            true
        );
        $(option).attr('data-anagrafica-id', '{{ $scarico->ordine->anagrafica->id }}');
        $(option).attr('data-nome-cliente', '{{ $scarico->ordine->anagrafica->nome_completo }}');
        $('#ordine_id').append(option).trigger('change');
    @endif

    // Debug submit
    $('form').on('submit', function (e) {
        console.log("🧪 Submit in corso con valori:");
        console.log("ordine_id:", $('#ordine_id').val());
        console.log("altro_ordine:", $('#altro_ordine').val());
        console.log("destinatario_nome:", $('#destinatario_nome').val());
        console.log("anagrafica_id:", $('#anagrafica_id').val());
    });

});
</script>
@endsection
