@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Nuova Ritenuta d'Autore</h3>

    <form action="{{ route('ritenute.store') }}" method="POST">
        @csrf

        <div class="row mb-3 g-3">
            <div class="col-md-4 col-12">
                <label>Nome</label>
                <input type="text" name="nome_autore" class="form-control" required>
            </div>
            <div class="col-md-4 col-12">
                <label>Cognome</label>
                <input type="text" name="cognome_autore" class="form-control" required>
            </div>
            <div class="col-md-4 col-12">
                <label>Codice Fiscale</label>
                <input type="text" name="codice_fiscale" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3 g-3">
            <div class="col-md-4 col-12">
                <label>Luogo di nascita</label>
                <input type="text" name="luogo_nascita" class="form-control" required>
            </div>
            <div class="col-md-4 col-12">
                <label>Data di nascita</label>
                <input type="date" name="data_nascita" id="data_nascita" class="form-control" required>
            </div>
            <div class="col-md-4 col-12">
                <label>IBAN</label>
                <input type="text" name="iban" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label>Indirizzo</label>
            <input type="text" name="indirizzo" class="form-control">
        </div>

        <div class="row mb-3 g-3">
            <div class="col-md-6 col-12">
                <label>Marchio editoriale</label>
                <select name="marchio_id" class="form-select">
                    <option value="">-- Seleziona --</option>
                    @foreach($marchi as $marchio)
                        <option value="{{ $marchio->id }}">{{ $marchio->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-12">
                <label>Luogo</label>
                <input type="text" name="luogo" class="form-control">
            </div>
        </div>

        <div class="mb-4">
            <h5>Prestazioni</h5>
            <div class="table-responsive">
                <table class="table" id="tabella-prestazioni">
                    <thead><tr><th>Descrizione</th><th>Importo</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="aggiungiPrestazione()">+ Aggiungi prestazione</button>
        </div>

        <div class="row mt-3 g-3">
            <div class="col-md-3 col-6">
                <label>Totale</label>
                <input type="text" id="totale" class="form-control" readonly>
            </div>
            <div class="col-md-3 col-6">
                <label>Quota esente</label>
                <input type="text" id="quota_esente" class="form-control" readonly>
            </div>
            <div class="col-md-3 col-6">
                <label>Imponibile</label>
                <input type="text" id="imponibile" class="form-control" readonly>
            </div>
            <div class="col-md-3 col-6">
                <label>R.A. 20%</label>
                <input type="text" id="ra" class="form-control" readonly>
            </div>
            <div class="col-md-3 col-6">
                <label>Netto da pagare</label>
                <input type="text" id="netto" class="form-control" readonly>
            </div>
        </div>

        <div class="row mb-3 g-3">
            <div class="col-md-4 col-12">
                <label>Nota IVA</label>
                <input type="text" name="nota_iva" class="form-control" value="Esente I.V.A. ai sensi dell’art. 3, comma 4, lettera A, DPR 63371972 e successive modifiche.">
            </div>
            <div class="col-md-4 col-12">
                <label>Marca da bollo</label>
                <input type="text" name="marca_bollo" class="form-control" value="€ 2,00 (per importi superiori a 77,47)">
            </div>
            <div class="col-md-4 col-12">
                <label>Data emissione</label>
                <input type="date" name="data_emissione" id="data_emissione" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Salva</button>
    </form>
</div>

<script>
function aggiungiPrestazione() {
    let tbody = document.querySelector('#tabella-prestazioni tbody');
    let row = `<tr>
        <td><input name="prestazioni[][descrizione]" class="form-control" value=""></td>
        <td><input name="prestazioni[][importo]" class="form-control importo-prestazione" value="" oninput="calcolaRitenuta()"></td>
    </tr>`;
    tbody.insertAdjacentHTML('beforeend', row);
}

function calcolaRitenuta() {
    let importi = document.querySelectorAll('.importo-prestazione');
    let totale = 0;
    importi.forEach(input => {
        let val = parseFloat(input.value.replace(',', '.'));
        if (!isNaN(val)) totale += val;
    });

    // Calcola età
    const nascita = document.getElementById('data_nascita').value;
    const emissione = document.getElementById('data_emissione').value;
    let anni = 40; // default

    if (nascita && emissione) {
        const n = new Date(nascita);
        const e = new Date(emissione);
        anni = e.getFullYear() - n.getFullYear();
        if (e.getMonth() < n.getMonth() || (e.getMonth() === n.getMonth() && e.getDate() < n.getDate())) {
            anni--;
        }
    }

    let quotaPercent = anni < 35 ? 0.40 : 0.25;
    let imponibilePercent = 1 - quotaPercent;

    let quota_esente = totale * quotaPercent;
    let imponibile = totale * imponibilePercent;
    let ra = imponibile * 0.20;
    let netto = totale - ra;

    document.getElementById('totale').value = totale.toFixed(2);
    document.getElementById('quota_esente').value = quota_esente.toFixed(2);
    document.getElementById('imponibile').value = imponibile.toFixed(2);
    document.getElementById('ra').value = ra.toFixed(2);
    document.getElementById('netto').value = netto.toFixed(2);
}

document.getElementById('data_emissione').addEventListener('change', calcolaRitenuta);
document.getElementById('data_nascita').addEventListener('change', calcolaRitenuta);
</script>
@endsection
