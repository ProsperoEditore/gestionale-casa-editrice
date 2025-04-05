@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifica Dettaglio Tiratura</h1>

    <form action="{{ route('registro-tirature.dettagli.update', ['registroTirature' => $registro->id, 'dettaglio' => $dettaglio->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="data" class="form-label">Data</label>
            <input type="date" name="data" id="data" class="form-control" value="{{ $dettaglio->data }}" required>
        </div>

        <div class="mb-3">
            <label for="titolo_id" class="form-label">Titolo e testata</label>
            <select name="titolo_id" id="titolo_id" class="form-control" required>
                @foreach($libri as $libro)
                    <option value="{{ $libro->id }}" data-prezzo="{{ $libro->prezzo }}" {{ $dettaglio->titolo_id == $libro->id ? 'selected' : '' }}>
                        {{ $libro->titolo }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="copie_stampate" class="form-label">Copie stampate</label>
            <input type="number" name="copie_stampate" id="copie_stampate" class="form-control" min="0" value="{{ $dettaglio->copie_stampate }}" required>
        </div>

        <div class="mb-3">
            <label for="prezzo_vendita_iva" class="form-label">Prezzo di Vendita IVA Compresa</label>
            <input type="text" name="prezzo_vendita_iva" id="prezzo_vendita_iva" class="form-control" value="{{ number_format($dettaglio->prezzo_vendita_iva, 3, ',', '') }}" required>
        </div>

        <div class="mb-3">
            <label for="imponibile_relativo" class="form-label">Imponibile Relativo</label>
            <input type="text" name="imponibile_relativo" id="imponibile_relativo" class="form-control" value="{{ number_format($dettaglio->imponibile_relativo, 3, ',', '') }}" readonly>
        </div>

        <div class="mb-3">
            <label for="imponibile" class="form-label">Imponibile</label>
            <input type="text" name="imponibile" id="imponibile" class="form-control" value="{{ number_format($dettaglio->imponibile, 3, ',', '') }}" readonly>
        </div>

        <div class="mb-3">
            <label for="iva_4percento" class="form-label">IVA 4%</label>
            <input type="text" name="iva_4percento" id="iva_4percento" class="form-control" value="{{ number_format($dettaglio->iva_4percento, 3, ',', '') }}" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Salva modifiche</button>
        <a href="{{ route('registro-tirature.show', $registro->id) }}" class="btn btn-secondary">Annulla</a>
    </form>
</div>

<script>
    const titoloSelect = document.getElementById('titolo_id');
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

    titoloSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const prezzo = selectedOption.getAttribute('data-prezzo');
        if (prezzo) {
            prezzoVenditaInput.value = formattaValore(parseFloat(prezzo));
            calcola();
        }
    });

    [copieStampateInput, prezzoVenditaInput].forEach(el => {
        el.addEventListener('input', calcola);
    });
</script>
@endsection
