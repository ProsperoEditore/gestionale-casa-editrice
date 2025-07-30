@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Nuova ritenuta d'acconto per Diritti d'Autore</h3>

    <form action="{{ route('ritenute.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="autore_search">Cerca autore</label>
            <input type="text" id="autore_search" name="autore_search" class="form-control" placeholder="Digita nome o pseudonimo">
        </div>

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
                <div class="input-group">
                    <input type="text" name="data_nascita" id="data_nascita" class="form-control" placeholder="gg-mm-aaaa" required>
                    <span class="input-group-text" id="etichetta_eta">—</span>
                </div>
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
            <div class="col-md-4 col-12">
                <label>Numero nota</label>
                <input type="text" name="numero_nota" class="form-control" required>
            </div>
            <div class="col-md-4 col-12">
                <label>Luogo</label>
                <input type="text" name="luogo" class="form-control">
            </div>
            <div class="col-md-4 col-12">
                <label>Data emissione</label>
                <input type="date" name="data_emissione" id="data_emissione" class="form-control" required value="{{ now()->format('Y-m-d') }}">
            </div>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="modificaManuale" onchange="toggleManuale()">
            <label class="form-check-label" for="modificaManuale">
                Modifica manuale percentuali
            </label>
        </div>

        <div id="percentualiManuali" class="row g-3 mb-3 d-none">
            <div class="col-md-4 col-12">
                <label>Quota esente (%)</label>
                <input type="number" id="percentuale_quota_esente" name="percentuale_quota_esente" step="0.01" class="form-control" placeholder="Es. 40" oninput="calcolaRitenuta()">
            </div>
            <div class="col-md-4 col-12">
                <label>Ritenuta su imponibile (%)</label>
                <input type="number" id="percentuale_ritenuta" name="percentuale_ritenuta" step="0.01" class="form-control" placeholder="Es. 20" oninput="calcolaRitenuta()">
            </div>
        </div>

        <div class="mb-4">
            <h5>Prestazioni</h5>
            <div class="table-responsive">
                <table class="table" id="tabella-prestazioni">
                    <thead><tr><th>Descrizione / Titolo</th><th>Importo</th><th></th></tr></thead>
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
        </div>

        <button type="submit" class="btn btn-success">Salva</button>
    </form>
</div>

<script>
let titoli = @json(\App\Models\Libro::select('id', 'titolo', 'isbn')->get());

function aggiungiPrestazione() {
    let tbody = document.querySelector('#tabella-prestazioni tbody');
    let index = tbody.children.length;
    let row = `<tr>
        <td>
            <input name="prestazioni[${index}][descrizione]" class="form-control titolo-input" placeholder="Scrivi titolo o descrizione" oninput="suggestTitolo(this)">
            <ul class="list-group mt-1 shadow-sm titolo-suggerimenti d-none"></ul>
        </td>
        <td><input name="prestazioni[${index}][importo]" class="form-control importo-prestazione" oninput="calcolaRitenuta()"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); calcolaRitenuta()">✕</button></td>
    </tr>`;
    tbody.insertAdjacentHTML('beforeend', row);
}

function suggestTitolo(input) {
    const query = input.value.toLowerCase();
    const list = input.nextElementSibling;
    list.innerHTML = '';
    if (!query) {
        list.classList.add('d-none');
        return;
    }

    titoli.filter(l => l.titolo.toLowerCase().includes(query) || l.isbn.includes(query))
          .forEach(libro => {
              const li = document.createElement('li');
              li.className = 'list-group-item list-group-item-action';
              li.textContent = `${libro.titolo} (${libro.isbn})`;
              li.onclick = () => {
                  input.value = `${libro.titolo} (${libro.isbn})`;
                  list.classList.add('d-none');
              };
              list.appendChild(li);
          });

    list.classList.remove('d-none');
}

function toggleManuale() {
    const box = document.getElementById('percentualiManuali');
    box.classList.toggle('d-none');
    calcolaRitenuta();
}

function calcolaRitenuta() {
    let importi = document.querySelectorAll('.importo-prestazione');
    let totale = 0;
    importi.forEach(input => {
        let val = parseFloat(input.value.replace(',', '.'));
        if (!isNaN(val)) totale += val;
    });

    let nascita = document.getElementById('data_nascita').value;
    let nascitaFormattata = nascita;

    if (nascita && nascita.match(/^\d{2}-\d{2}-\d{4}$/)) {
        const [gg, mm, aaaa] = nascita.split("-");
        nascitaFormattata = `${aaaa}-${mm}-${gg}`; // formato compatibile con new Date()
    }


    const emissione = document.getElementById('data_emissione').value;
    let quotaPercent = 0.25;
    let fascia = 'Over 35';

    if (nascita && emissione) {
        const n = new Date(nascitaFormattata);
        const e = new Date(emissione);
        let anni = e.getFullYear() - n.getFullYear();
        if (e.getMonth() < n.getMonth() || (e.getMonth() === n.getMonth() && e.getDate() < n.getDate())) anni--;
        if (anni < 35) {
            quotaPercent = 0.40;
            fascia = 'Under 35';
        }
    }

    const manuale = document.getElementById('modificaManuale').checked;

    if (manuale) {
        const pQuota = parseFloat(document.getElementById('percentuale_quota_esente').value);
        if (!isNaN(pQuota)) quotaPercent = pQuota / 100;
    }

    document.getElementById('etichetta_eta').textContent = fascia;

    let imponibilePercent = 1 - quotaPercent;
    let quota_esente = totale * quotaPercent;
    let imponibile = totale * imponibilePercent;

    let raPercent = 0.20;
    if (manuale) {
        const pRa = parseFloat(document.getElementById('percentuale_ritenuta').value);
        if (!isNaN(pRa)) raPercent = pRa / 100;
    }

    let ra = imponibile * raPercent;
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

<!-- CSS jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<!-- jQuery (prima di jQuery UI!) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function () {
    $("#autore_search").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "{{ route('ritenute.autocomplete-autore') }}",
                dataType: "json",
                data: { term: request.term },
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        focus: function (event, ui) {
            $("#autore_search").val(ui.item.label);
            return false;
        },
        select: function (event, ui) {
            $("#autore_search").val(ui.item.label);

            $('input[name="nome_autore"]').val(ui.item.nome).prop('readonly', true);
            $('input[name="cognome_autore"]').val(ui.item.cognome).prop('readonly', true);
            $('input[name="codice_fiscale"]').val(ui.item.codice_fiscale).prop('readonly', true);
            let data = ui.item.data_nascita;

                if (data && data.includes('T')) {
                    data = data.split('T')[0]; // rimuove l'ora se presente
                }

                if (data) {
                    const [yyyy, mm, dd] = data.split('-');
                    data = `${dd}-${mm}-${yyyy}`; // converte da yyyy-mm-dd a gg-mm-aaaa
                }

                $('input[name="data_nascita"]').val(data).prop('readonly', true);

            $('input[name="luogo_nascita"]').val(ui.item.luogo_nascita).prop('readonly', true);
            $('input[name="iban"]').val(ui.item.iban).prop('readonly', true);
            $('input[name="indirizzo"]').val(ui.item.indirizzo).prop('readonly', true);

            calcolaRitenuta();
            return false;
        }
    });
});
</script>



<style>
.titolo-suggerimenti {
    position: absolute;
    z-index: 1000;
    width: 100%;
}
</style>
@endsection
