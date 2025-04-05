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
                    <select name="ordine_id" id="ordine_id" class="form-select">
                        <option value="">-- Nessun ordine associato --</option>
                        @foreach($ordini as $ordine)
                            <option 
                                value="{{ $ordine->id }}"
                                data-destinatario="{{ $ordine->anagrafica->nome }}"
                                data-anagrafica-id="{{ $ordine->anagrafica->id }}"
                            >
                                {{ $ordine->codice }} - {{ $ordine->anagrafica->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Altro ordine -->
                <div class="mb-3">
                    <label class="form-label">Altro Ordine</label>
                    <input type="text" name="altro_ordine" id="altro_ordine" class="form-control">
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

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log("SCRIPT ATTIVO ✅");

    const ordineSelect = document.getElementById('ordine_id');
    const altroOrdineInput = document.getElementById('altro_ordine');
    const destinatarioNome = document.getElementById('destinatario_nome');
    const anagraficaId = document.getElementById('anagrafica_id');

    // Select2
    $('#ordine_id').select2({
    placeholder: 'Scrivi o seleziona un ordine...',
    allowClear: true,
    width: '100%'
}).on('select2:select', function (e) {
    ordineSelect.dispatchEvent(new Event('change')); // forza trigger evento
});

    ordineSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const nomeDestinatario = selectedOption.getAttribute('data-destinatario');
        const idAnagrafica = selectedOption.getAttribute('data-anagrafica-id');

        console.log("Destinatario:", nomeDestinatario);
        console.log("ID Anagrafica:", idAnagrafica);

        if (this.value) {
            altroOrdineInput.value = '';
            altroOrdineInput.disabled = true;

            destinatarioNome.value = nomeDestinatario || '';
            destinatarioNome.readOnly = true;

            anagraficaId.value = idAnagrafica || '';
        } else {
            altroOrdineInput.disabled = false;
            destinatarioNome.readOnly = false;

            destinatarioNome.value = '';
            anagraficaId.value = '';
        }
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
});
</script>

@endsection
