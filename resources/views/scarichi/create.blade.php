@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Crea Nuova Spedizione</h3>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('scarichi.store') }}" method="POST">
                @csrf

                <!-- Ordine associato -->
                <div class="mb-3">
                    <label class="form-label">Ordine Associato (facoltativo)</label>
                    <select name="ordine_id" id="ordine_id" class="form-select" style="width: 100%">
                        <option value="" selected>-- Nessun ordine associato --</option>
                    </select>
                </div>

                <!-- Altro ordine -->
                <div class="mb-3">
                    <label class="form-label">Altro Ordine</label>
                    <input type="text" name="altro_ordine" id="altro_ordine" class="form-control" autocomplete="off">
                </div>

                <!-- Destinatario -->
                <div class="mb-3">
                    <label class="form-label">Destinatario</label>
                    <input type="text" name="destinatario_nome" id="destinatario_nome" class="form-control">
                    <input type="hidden" name="anagrafica_id" id="anagrafica_id">
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success">Salva</button>
                    <a href="{{ route('scarichi.index') }}" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Select2 & jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    console.log("CREATE SCRIPT ATTIVO ✅");

    const ordineSelect = $('#ordine_id');
    const altroOrdineInput = document.getElementById('altro_ordine');
    const destinatarioNome = document.getElementById('destinatario_nome');
    const anagraficaId = document.getElementById('anagrafica_id');

    ordineSelect.select2({
        placeholder: 'Scrivi codice ordine o nome cliente...',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("scarichi.autocomplete-ordini") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { query: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (ordine) {
                        return {
                            id: ordine.id,
                            text: ordine.codice + " - " + ordine.nome_cliente,
                            nome_cliente: ordine.nome_cliente,
                            anagrafica_id: ordine.anagrafica_id
                        };
                    })
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        const data = e.params.data;

        altroOrdineInput.value = '';
        altroOrdineInput.disabled = true;

        destinatarioNome.value = data.nome_cliente || '';
        destinatarioNome.readOnly = true;

        anagraficaId.value = data.anagrafica_id || '';
    }).on('select2:clear', function () {
        altroOrdineInput.disabled = false;
        destinatarioNome.readOnly = false;

        destinatarioNome.value = '';
        anagraficaId.value = '';
    });

    altroOrdineInput.addEventListener('input', function () {
        if (this.value.trim() !== '') {
            ordineSelect.val(null).trigger('change');
            ordineSelect.prop('disabled', true);

            destinatarioNome.readOnly = false;
            destinatarioNome.value = '';
            anagraficaId.value = '';
        } else {
            ordineSelect.prop('disabled', false);
        }
    });
});
</script>
@endsection
