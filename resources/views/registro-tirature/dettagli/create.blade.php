@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Aggiungi Dettaglio Tiratura</h1>

    <form action="{{ route('registro-tirature.dettagli.store', $registro->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="data" class="form-label">Data</label>
            <input type="date" name="data" id="data" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="titolo_autocomplete" class="form-label">Titolo e testata</label>
            <input type="text" id="titolo_autocomplete" class="form-control" placeholder="Digita il titolo..." required>
            <input type="hidden" name="titolo_id" id="titolo_id">
        </div>

        <div class="mb-3">
            <label for="copie_stampate" class="form-label">Copie stampate</label>
            <input type="number" name="copie_stampate" id="copie_stampate" class="form-control" min="0" required>
        </div>

        <div class="mb-3">
            <label for="prezzo_vendita_iva" class="form-label">Prezzo di Vendita IVA Compresa</label>
            <input type="text" name="prezzo_vendita_iva" id="prezzo_vendita_iva" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="imponibile_relativo" class="form-label">Imponibile Relativo</label>
            <input type="text" name="imponibile_relativo" id="imponibile_relativo" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label for="imponibile" class="form-label">Imponibile</label>
            <input type="text" name="imponibile" id="imponibile" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label for="iva_4percento" class="form-label">IVA 4%</label>
            <input type="text" name="iva_4percento" id="iva_4percento" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-success">Salva</button>
        <a href="{{ route('registro-tirature.show', $registro->id) }}" class="btn btn-secondary">Annulla</a>
    </form>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css">

<script>
    const libri = @json($libri);
    const prezzoVenditaInput = document.getElementById('prezzo_vendita_iva');
    const copieStampateInput = document.getElementById('copie_stampate');
    const imponibileRelativoInput = document.getElementById('imponibile_relativo');
    const imponibileInput = document.getElementById('imponibile');
    const iva4Input = document.getElementById('iva_4percento');

    function formattaValore(numero) {
        return numero.toFixed(3).replace('.', ',');
    }

    function calcola() {
        const copie = parseFloat(copieStampateInput.value) || 0;
        const prezzo = parseFloat(prezzoVenditaInput.value.replace(',', '.')) || 0;

        const imponibileRelativo = copie * prezzo * 0.3;
        const imponibile = imponibileRelativo / 1.04;
        const iva = imponibileRelativo - imponibile;

        imponibileRelativoInput.value = formattaValore(imponibileRelativo);
        imponibileInput.value = formattaValore(imponibile);
        iva4Input.value = formattaValore(iva);
    }

    $('#titolo_autocomplete').autocomplete({
        source: libri.map(libro => ({
            label: libro.titolo,
            value: libro.titolo,
            id: libro.id,
            prezzo: libro.prezzo
        })),
        select: function (event, ui) {
            $('#titolo_id').val(ui.item.id);
            $('#prezzo_vendita_iva').val(formattaValore(parseFloat(ui.item.prezzo)));
            calcola();
        }
    });

    [copieStampateInput, prezzoVenditaInput].forEach(el => {
        el.addEventListener('input', calcola);
    });
</script>
@endsection
