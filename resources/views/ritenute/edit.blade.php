@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Modifica ritenuta d'acconto per Diritti d'Autore</h3>

    <div class="alert alert-warning mt-3" role="alert">
    <strong>Nota:</strong> per ottenere un <strong>Netto da pagare</strong> specifico, inserire nel campo <strong>Importo</strong> il valore risultante da:
    <ul class="mb-0 mt-2">
        <li><strong>Netto : 95%</strong> per autori extracomunitari</li>
        <li><strong>Netto : 88%</strong> per autori under 35</li>
        <li><strong>Netto : 85%</strong> per autori over 35</li>
        
    </ul>
</div>

    {{-- ⚠️ Mostra errori di validazione --}}
    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ritenute.update', $ritenuta->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="autore_search">Cerca autore</label>
            <input type="text" id="autore_search" name="autore_search" class="form-control" placeholder="Digita nome o pseudonimo">
        </div>


        <div class="row mb-3 g-3">
            <div class="col-md-4 col-12">
                <label>Nome</label>
                <input type="text" name="nome_autore" class="form-control" value="{{ $ritenuta->nome_autore }}" required>
            </div>
            <div class="col-md-4 col-12">
                <label>Cognome</label>
                <input type="text" name="cognome_autore" class="form-control" value="{{ $ritenuta->cognome_autore }}" required>
            </div>
            <div class="col-md-4 col-12">
                <label>Codice Fiscale</label>
                <input type="text" name="codice_fiscale" class="form-control" value="{{ $ritenuta->codice_fiscale }}" required>
            </div>
        </div>

        <div class="row mb-3 g-3">
            <div class="col-md-4 col-12">
                <label>Luogo di nascita</label>
                <input type="text" name="luogo_nascita" class="form-control" value="{{ $ritenuta->luogo_nascita }}" required>
            </div>
            <div class="col-md-4 col-12">
                <label>Data di nascita</label>
                <div class="input-group">
                    <input type="date" name="data_nascita" id="data_nascita" class="form-control" value="{{ $ritenuta->data_nascita->format('Y-m-d') }}" required>
                    <span class="input-group-text" id="etichetta_eta">—</span>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <label>IBAN</label>
                <input type="text" name="iban" class="form-control" value="{{ $ritenuta->iban }}">
            </div>
        </div>

        <div class="mb-3">
            <label>Indirizzo</label>
            <input type="text" name="indirizzo" class="form-control" value="{{ $ritenuta->indirizzo }}">
        </div>

        <div class="row mb-3 g-3">
            <div class="col-md-4 col-12">
                <label>Numero nota</label>
                <input type="text" name="numero_nota" class="form-control" value="{{ $ritenuta->numero }}">
            </div>
            <div class="col-md-4 col-12">
                <label>Luogo</label>
                <input type="text" name="luogo" class="form-control" value="{{ $ritenuta->luogo }}">
            </div>
            <div class="col-md-4 col-12">
                <label>Data emissione</label>
                <input type="date" name="data_emissione" id="data_emissione" class="form-control" value="{{ $ritenuta->data_emissione->format('Y-m-d') }}">
            </div>
        </div>

        <div class="mb-4">
            <h5>Prestazioni</h5>
            <div class="table-responsive">
                <table class="table" id="tabella-prestazioni">
                    <thead><tr><th>Descrizione / Titolo</th><th>Importo</th><th></th></tr></thead>
                    <tbody>
                        @foreach($ritenuta->prestazioni as $i => $p)
                        <tr>
                            <td>
                                <input name="prestazioni[{{ $i }}][descrizione]" class="form-control titolo-input" value="{{ $p['descrizione'] }}" oninput="suggestTitolo(this)">
                                <ul class="list-group mt-1 shadow-sm titolo-suggerimenti d-none"></ul>
                            </td>
                            <td>
                                <input name="prestazioni[{{ $i }}][importo]" class="form-control importo-prestazione" value="{{ $p['importo'] }}" oninput="calcolaRitenuta()">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); calcolaRitenuta()">✕</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="aggiungiPrestazione()">+ Aggiungi prestazione</button>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="manuale" onchange="calcolaRitenuta()">
            <label class="form-check-label" for="manuale">Disattiva calcolo automatico (inserimento manuale)</label>
        </div>

        <div class="row g-3 mb-3" id="manual-fields" style="display: none;">
            <div class="col-md-3">
                <label>% Imponibile</label>
                <input type="number" step="0.01" class="form-control" id="perc_imponibile" value="75">
            </div>
            <div class="col-md-3">
                <label>% R.A.</label>
                <input type="number" step="0.01" class="form-control" id="perc_ra" value="20">
            </div>
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
                <label>R.A.</label>
                <input type="text" id="ra" class="form-control" readonly>
            </div>
            <div class="col-md-3 col-6">
                <label>Netto da pagare</label>
                <input type="text" id="netto" class="form-control" readonly>
            </div>
        </div>

        <div class="row mb-3 g-3 mt-2">
            <div class="col-md-4 col-12">
                <label>Nota IVA</label>
                <input type="text" name="nota_iva" class="form-control" value="{{ $ritenuta->nota_iva }}">
            </div>
            <div class="col-md-4 col-12">
                <label>Marca da bollo</label>
                <input type="text" name="marca_bollo" class="form-control" value="{{ $ritenuta->marca_bollo }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Aggiorna</button>
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

function calcolaRitenuta() {
    let importi = document.querySelectorAll('.importo-prestazione');
    let totale = 0;
    importi.forEach(input => {
        let val = parseFloat(input.value.replace(',', '.'));
        if (!isNaN(val)) totale += val;
    });

    const auto = !document.getElementById('manuale').checked;
    let percQuotaEsente = 0.25;
    let percRA = 0.20;

    if (auto) {
        let fascia = 'Over 35';
        let nascita = document.getElementById('data_nascita').value;
        let nascitaFormattata = nascita;

        if (nascita && nascita.match(/^\d{2}-\d{2}-\d{4}$/)) {
            const [gg, mm, aaaa] = nascita.split("-");
            nascitaFormattata = `${aaaa}-${mm}-${gg}`; // formato compatibile con new Date()
        }


        const emissione = document.getElementById('data_emissione').value;
        if (nascita && emissione) {
            const n = new Date(nascitaFormattata);
            const e = new Date(emissione);
            let anni = e.getFullYear() - n.getFullYear();
            if (e.getMonth() < n.getMonth() || (e.getMonth() === n.getMonth() && e.getDate() < n.getDate())) anni--;
            if (anni < 35) {
                percQuotaEsente = 0.40;
                fascia = 'Under 35';
            }
        }
        document.getElementById('etichetta_eta').textContent = fascia;
    } else {
        percQuotaEsente = 1 - parseFloat(document.getElementById('perc_imponibile').value) / 100;
        percRA = parseFloat(document.getElementById('perc_ra').value) / 100;
    }

    document.getElementById('manual-fields').style.display = auto ? 'none' : 'flex';

    let quota_esente = totale * percQuotaEsente;
    let imponibile = totale - quota_esente;
    let ra = imponibile * percRA;
    let netto = totale - ra;

    document.getElementById('totale').value = totale.toFixed(2);
    document.getElementById('quota_esente').value = quota_esente.toFixed(2);
    document.getElementById('imponibile').value = imponibile.toFixed(2);
    document.getElementById('ra').value = ra.toFixed(2);
    document.getElementById('netto').value = netto.toFixed(2);
}

document.getElementById('data_emissione').addEventListener('change', calcolaRitenuta);
document.getElementById('data_nascita').addEventListener('change', calcolaRitenuta);
document.getElementById('manuale').addEventListener('change', calcolaRitenuta);
window.addEventListener('load', calcolaRitenuta);
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
                data = data.split('T')[0]; // rimuove tempo
            }

            // Se formato yyyy-mm-dd, converto in gg-mm-aaaa
            if (data && /^\d{4}-\d{2}-\d{2}$/.test(data)) {
                const [yyyy, mm, dd] = data.split('-');
                data = `${dd}-${mm}-${yyyy}`;
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
