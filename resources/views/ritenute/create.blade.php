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
                <input type="date" name="data_nascita" class="form-control" required>
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
            <div class="col-md-3 col-6">
                <label>Dal</label>
                <input type="date" name="dal" id="dal" class="form-control">
            </div>
            <div class="col-md-3 col-6">
                <label>Al</label>
                <input type="date" name="al" id="al" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label>Seleziona titoli</label>
            <select id="titoli" class="form-select" multiple></select>
            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="caricaDaReport()">Carica da report</button>
        </div>

        <div class="mb-4">
            <h5>Prestazioni</h5>
            <div class="table-responsive">
                <table class="table" id="tabella-prestazioni">
                    <thead><tr><th>Descrizione</th><th>Importo</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <input type="hidden" name="prestazioni[]" id="json-prestazioni">

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
                <input type="date" name="data_emissione" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Salva</button>
    </form>
</div>

<script>
function caricaDaReport() {
    let titoli = Array.from(document.querySelector('#titoli').selectedOptions).map(o => o.value);
    let dal = document.querySelector('#dal').value;
    let al = document.querySelector('#al').value;

    fetch('/ritenute/importi-report', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ titoli, dal, al })
    }).then(r => r.json()).then(data => {
        let tbody = document.querySelector('#tabella-prestazioni tbody');
        tbody.innerHTML = '';
        data.forEach(item => {
            let row = `<tr><td><input name="prestazioni[][descrizione]" class="form-control" value="${item.descrizione}"></td><td><input name="prestazioni[][importo]" class="form-control" value="${item.importo}"></td></tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    });
}
</script>
@endsection
