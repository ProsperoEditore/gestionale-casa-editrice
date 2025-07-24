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

        <div class="row mb-3 g-3">
            <div class="col-md-6 col-12">
                <label>Seleziona titoli</label>
                    <input type="text" id="autocomplete-titoli" class="form-control" placeholder="Cerca per titolo o ISBN">
                    <ul id="lista-titoli" class="list-group mt-2"></ul>
                    <input type="hidden" name="titoli[]" id="titoli-selezionati">
            </div>

            <div class="col-md-6 col-12">
                <label>Oppure seleziona da report esistenti</label>
                    <div id="lista-report-selezionati"></div>

                    <input type="text" id="autocomplete-report" class="form-control" placeholder="Cerca per titolo o ISBN">
                    <ul id="suggerimenti-report" class="list-group mt-1"></ul>

                    <!-- Campo nascosto -->
                    <input type="hidden" name="report_ids[]" id="report_ids">

                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="caricaDaReport()">Carica da report</button>
                <button type="button" class="btn btn-sm btn-outline-success mb-2" onclick="aggiungiPrestazione()">+ Aggiungi prestazione</button>
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
        </div>


        <div class="row mt-3 g-3">
            <div class="col-md-3 col-6">
                <label>Totale</label>
                <input type="text" id="totale" class="form-control" readonly>
            </div>
            <div class="col-md-3 col-6">
                <label>Quota esente (€ 100,00)</label>
                <input type="text" id="quota_esente" class="form-control" value="100" readonly>
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
            let row = `<tr>
                <td><input name="prestazioni[][descrizione]" class="form-control" value="${item.descrizione}"></td>
                <td><input name="prestazioni[][importo]" class="form-control importo-prestazione" value="${item.importo}" oninput="calcolaRitenuta()"></td>
            </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
        calcolaRitenuta();
    });
}

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

    let quota_esente = 100;
    let imponibile = Math.max(totale - quota_esente, 0);
    let ra = imponibile * 0.20;
    let netto = totale - ra;

    document.getElementById('totale').value = totale.toFixed(2);
    document.getElementById('imponibile').value = imponibile.toFixed(2);
    document.getElementById('ra').value = ra.toFixed(2);
    document.getElementById('netto').value = netto.toFixed(2);
}

</script>

<script>
let titoli = @json(\App\Models\Libro::select('id', 'titolo', 'isbn')->get());

const input = document.getElementById('autocomplete-titoli');
const lista = document.getElementById('lista-titoli');
const hidden = document.getElementById('titoli-selezionati');
let selezionati = [];

input.addEventListener('input', function () {
    const valore = this.value.toLowerCase();
    lista.innerHTML = '';

    titoli.filter(libro =>
        libro.titolo.toLowerCase().includes(valore) ||
        (libro.isbn && libro.isbn.includes(valore))
    ).forEach(libro => {
        const voce = document.createElement('li');
        voce.classList.add('list-group-item', 'list-group-item-action');
        voce.textContent = `${libro.titolo} (${libro.isbn})`;

        voce.onclick = () => {
            if (!selezionati.includes(libro.id)) {
                selezionati.push(libro.id);
                aggiornaTitoliSelezionati();
            }
        };

        lista.appendChild(voce);
    });
});

function aggiornaTitoliSelezionati() {
    const html = selezionati.map(id => {
        const libro = titoli.find(l => l.id === id);
        return `<li class="list-group-item d-flex justify-content-between align-items-center">
            ${libro.titolo} (${libro.isbn})
            <button type="button" class="btn-close" onclick="rimuoviTitolo(${id})"></button>
        </li>`;
    }).join('');
    lista.innerHTML = html;
    hidden.value = JSON.stringify(selezionati);
}

function rimuoviTitolo(id) {
    selezionati = selezionati.filter(i => i !== id);
    aggiornaTitoliSelezionati();
}
</script>


<script>
let reports = @json($reportDisponibili); // deve includere id, titolo, isbn, created_at
let reportSelezionati = [];

document.getElementById('autocomplete-report').addEventListener('input', function () {
    const query = this.value.toLowerCase();
    const lista = document.getElementById('suggerimenti-report');
    lista.innerHTML = '';

    reports.filter(r =>
        r.titolo.toLowerCase().includes(query) || (r.isbn && r.isbn.includes(query))
    ).forEach(r => {
        const li = document.createElement('li');
        li.classList.add('list-group-item', 'list-group-item-action');
        li.innerHTML = `<strong>${r.titolo}</strong> (${r.isbn}) – ${new Date(r.created_at).toLocaleDateString()}`;
        li.onclick = () => aggiungiReport(r);
        lista.appendChild(li);
    });
});

function aggiungiReport(report) {
    if (!reportSelezionati.some(r => r.id === report.id)) {
        reportSelezionati.push(report);
        aggiornaReportSelezionati();
    }
    document.getElementById('autocomplete-report').value = '';
    document.getElementById('suggerimenti-report').innerHTML = '';
}

function aggiornaReportSelezionati() {
    const div = document.getElementById('lista-report-selezionati');
    div.innerHTML = reportSelezionati.map(r => `
        <div class="border rounded p-2 mb-2">
            <strong>${r.titolo}</strong> (${r.isbn}) – ${new Date(r.created_at).toLocaleDateString()}
            <div class="row g-2 mt-2">
                <div class="col-md-6 col-6">
                    <label>Dal</label>
                    <input type="date" class="form-control" name="report_intervalli[${r.id}][dal]">
                </div>
                <div class="col-md-6 col-6">
                    <label>Al</label>
                    <input type="date" class="form-control" name="report_intervalli[${r.id}][al]">
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="rimuoviReport(${r.id})">Rimuovi</button>
        </div>
    `).join('');

    // Aggiorna hidden con gli ID
    document.getElementById('report_ids').value = JSON.stringify(reportSelezionati.map(r => r.id));
}

function rimuoviReport(id) {
    reportSelezionati = reportSelezionati.filter(r => r.id !== id);
    aggiornaReportSelezionati();
}
</script>


@endsection
