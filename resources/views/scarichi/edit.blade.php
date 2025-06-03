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
                            @if(isset($scarico) && $scarico->ordine)
                                <option value="{{ $scarico->ordine->id }}" selected
                                    data-anagrafica-id="{{ $scarico->ordine->anagrafica->id }}"
                                    data-nome-cliente="{{ $scarico->ordine->anagrafica->nome }}">
                                    {{ $scarico->ordine->codice }} - {{ $scarico->ordine->anagrafica->nome }}
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
                           value="{{ $scarico->anagrafica->nome ?? $scarico->destinatario_nome }}" 
                           @if($scarico->ordine_id) readonly @endif>
                    <input type="hidden" name="anagrafica_id" id="anagrafica_id" 
                           value="{{ $scarico->anagrafica_id }}">
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success">Aggiorna</button>
                    <a href="{{ route('scarichi.index') }}" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Select2 CSS & JS -->
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
    }) 
    .on('select2:select', function (e) {
        const data = e.params.data;

        altroOrdineInput.value = '';
        altroOrdineInput.disabled = true;

        // Popola destinatario e ID anagrafica
        if (data.nome_cliente && data.anagrafica_id) {
            destinatarioNome.value = data.nome_cliente;
            anagraficaId.value = data.anagrafica_id;
        } else {
            // Se non presenti in JSON, cerca nei data-attribute dell'option selezionata
            const selectedOption = $('#ordine_id').find('option:selected');
            destinatarioNome.value = selectedOption.data('nome-cliente') || '';
            anagraficaId.value = selectedOption.data('anagrafica-id') || '';
        }

        destinatarioNome.readOnly = true;

        console.log("✅ anagrafica_id aggiornato:", anagraficaId.value);
    });

    altroOrdineInput.addEventListener('input', function () {
        if (this.value.trim() !== '') {
            $('#ordine_id').val(null).trigger('change');
            ordineSelect.disabled = true;

            destinatarioNome.readOnly = false;
            destinatarioNome.value = '';
            anagraficaId.value = '';
        } else {
            ordineSelect.disabled = false;
        }
    });

    @if(isset($scarico) && $scarico->ordine)
        const option = new Option(
            '{{ $scarico->ordine->codice }} - {{ $scarico->ordine->anagrafica->nome }}',
            '{{ $scarico->ordine->id }}',
            true,
            true
        );
        $(option).attr('data-anagrafica-id', '{{ $scarico->ordine->anagrafica->id }}');
        $(option).attr('data-nome-cliente', '{{ $scarico->ordine->anagrafica->nome }}');
        $('#ordine_id').append(option).trigger('change');
        $('#anagrafica_id').val('{{ $scarico->ordine->anagrafica->id }}');
        $('#destinatario_nome').val('{{ $scarico->ordine->anagrafica->nome }}');
    @endif

});
</script>

@endsection
