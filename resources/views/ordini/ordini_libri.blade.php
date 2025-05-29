@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <h3 class="text-center mb-4">Gestione ordine {{ $ordine->codice }}</h3>

@if($ordine->exists)
<div class="alert alert-warning mt-3 text-center">
    <span class="text-danger">
        ℹ️ Se selezioni <strong>“spedito da magazzino editore”</strong> o <strong>“consegna a mano”</strong> nella colonna <em>Info</em>,<br>
        il sistema aggiorna automaticamente le giacenze del magazzino editore.
    </span>
    <br><br>
    ⚠️ Se stai modificando un ordine esistente, ricorda di aggiornare manualmente le quantità in <strong>Magazzino</strong>.
</div>
@endif


<div class="mb-3 d-flex gap-2">
    <a href="{{ route('ordini.index') }}" class="btn btn-secondary">Torna agli Ordini</a>

    @if($ordine->exists)
        <a href="{{ route('ordini.stampa', $ordine->id) }}" target="_blank" class="btn btn-danger">
            📄 Stampa
        </a>
    @endif
</div>


<div class="card">
    <div class="card-body">
        <h5 class="card-title">Importa libri da Excel</h5>
@php
$righeAmbigue = session()->pull('righe_ambigue_ordini', []);
@endphp


@if(session('import_errori'))
    <div class="alert alert-danger mt-3">
        <strong>⚠️ Errori durante l'importazione:</strong>
        <ul>
            @foreach(session('import_errori') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif


@if(count($righeAmbigue) > 0)
    @include('ordini.partials.popup_conflitti', ['righeAmbigue' => $righeAmbigue])
@endif

        <form action="{{ route('ordini.import.libri', $ordine->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" accept=".xlsx" required class="form-control mb-2">
            <button type="submit" class="btn btn-primary">Importa</button>
        </form>
    </div>
</div>

<form action="{{ route('ordini.libri.store', $ordine->id) }}" method="POST">
    @csrf

    @if($ordine->tipo_ordine === 'conto deposito')
    <div class="mb-3 mt-4">
        <label for="causale" class="form-label"><strong>Causale</strong></label>
        <select name="causale" class="form-control" id="causale">
            <option value="">Seleziona...</option>
            <option value="Assortimento" {{ old('causale', $ordine->causale) === 'Assortimento' ? 'selected' : '' }}>Assortimento</option>
            <option value="Presentazione" {{ old('causale', $ordine->causale) === 'Presentazione' ? 'selected' : '' }}>Presentazione</option>
        </select>
    </div>

    <div class="mb-4">
        <label for="condizioni_conto_deposito" class="form-label"><strong>Condizioni conto deposito</strong></label>
        <textarea name="condizioni_conto_deposito" id="condizioni_conto_deposito" rows="5" class="form-control">{{ old('condizioni_conto_deposito', $ordine->condizioni_conto_deposito ?? "Consegna da parte della Casa Editrice.\nPer gli ordini con causale \"presentazione\" si prega di eseguire rendicontazione e resa entro 7 giorni dall'evento.\nPer gli ordini con causale \"assortimento\" si prega di inviare rendiconto del conto deposito trimestralmente, entro il 15 aprile, 15 luglio, 15 ottobre e 15 gennaio.\nRestituzione a carico della Libreria verso indirizzo \"Sede operativa\".") }}</textarea>
    </div>
    @endif

    @if(in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
    <div class="mb-3">
        <label for="pagato" class="form-label"><strong>Pagato</strong></label>
        <input type="text" name="pagato" class="form-control" maxlength="250" value="{{ old('pagato', $ordine->pagato) }}">
    </div>
    <div class="mb-3 mt-4">
        <label for="specifiche_iva" class="form-label"><strong>Specifiche IVA</strong></label>
        <input type="text" name="specifiche_iva" maxlength="255" class="form-control" value="{{ old('specifiche_iva', $ordine->specifiche_iva ?? "IVA assolta all'origine dall'editore, ai sensi dell'art.74 co. 1 lett. c del DPR 633/72") }}">
    </div>
    <div class="mb-3">
        <label for="costo_spedizione" class="form-label"><strong>Costo spedizione</strong></label>
        <input type="text" name="costo_spedizione" maxlength="255" class="form-control" value="{{ old('costo_spedizione', $ordine->costo_spedizione) }}">
    </div>
    <div class="mb-3">
        <label for="altre_specifiche_iva" class="form-label"><strong>Altre specifiche</strong></label>
        <input type="text" name="altre_specifiche_iva" maxlength="500" class="form-control" value="{{ old('altre_specifiche_iva', $ordine->altre_specifiche_iva) }}">
    </div>
    <div class="mb-3">
        <label for="totale_netto_compilato" class="form-label"><strong>Totale netto da pagare (modificabile)</strong></label>
        <input type="number" step="0.01" name="totale_netto_compilato" id="totale_netto_compilato" class="form-control" value="{{ old('totale_netto_compilato', $ordine->totale_netto_compilato) }}">
    </div>
    <div class="mt-4 mb-3">
        <label for="tempi_pagamento" class="form-label"><strong>Tempi di pagamento</strong></label>
        <input type="text" name="tempi_pagamento" class="form-control" id="tempi_pagamento" value="{{ old('tempi_pagamento', $ordine->tempi_pagamento ?? 'A vista del presente documento') }}">
    </div>
    <div class="mb-4">
        <label for="modalita_pagamento" class="form-label"><strong>Modalità di pagamento</strong></label>
        <textarea name="modalita_pagamento" id="modalita_pagamento" rows="7" class="form-control">{{ old('modalita_pagamento', $ordine->modalita_pagamento ?? "Bonifico bancario\nDenominazione conto: Prospero Editore\nBanca e Agenzia: Banca Popolare di Sondrio, Agenzia 185 \n(via Milano, 47 – 22063, Cantù, CO)\nIBAN: IT57B0569651060000003194X56\nBIC-SWIFT: POSOIT22XXX / ABI: 05696 / CAB: 51060\n\nPayPal\nprosperoeditore@gmail.com\n\nCarta di credito\nhttps://www.paypal.me/prosperoeditore") }}</textarea>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" class="btn btn-success" id="addRow">Aggiungi riga</button>
        <button type="submit" class="btn btn-primary">Salva</button>
    </div>

    <div class="mb-3">
        <label for="barcode-scan-ordini" class="form-label">Scansiona ISBN</label>
        <input type="text" id="barcode-scan-ordini" class="form-control" placeholder="Scansiona codice a barre..." autofocus>
    </div>

    <h5 class="mt-5">Elenco libri</h5>
    <div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ISBN</th>
                <th>Titolo</th>
                <th>Quantità</th>
                <th>Prezzo Copertina</th>
                <th>Valore Lordo</th>
                <th>Sconto (%)</th>
                <th>Valore Scontato</th>
                <th>Info</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody id="ordiniBody">
            @foreach($ordine->libri as $libro)
            <tr>
                <td data-label="ISBN">
                    <input type="text" name="isbn[]" class="form-control isbn-field" value="{{ $libro->isbn }}" readonly>
                </td>
                <td hidden>
                    <input type="hidden" name="libro_id[]" class="libro-id" value="{{ $libro->id }}">
                </td>
                <td data-label="Titolo">
                    <input type="text" name="titolo[]" class="form-control titolo-autocomplete" value="{{ $libro->titolo }}" placeholder="cerca/scansiona titolo...">
                </td>
                <td data-label="Quantità">
                    <input type="number" name="quantita[]" class="form-control quantita-field" value="{{ $libro->pivot->quantita }}">
                    <div class="stock-info text-muted" style="font-size: 0.8em; display: none; opacity: 0.6;"></div>
                </td>
                <td data-label="Prezzo Copertina">
                    <input type="text" name="prezzo[]" class="form-control prezzo-field" value="{{ $libro->prezzo }}" readonly>
                </td>
                <td data-label="Valore Lordo">
                    <input type="text" name="valore_vendita_lordo[]" class="form-control valore_vendita_lordo" value="{{ $libro->pivot->valore_vendita_lordo }}" readonly>
                </td>
                <td data-label="Sconto (%)">
                    <input type="text" name="sconto[]" class="form-control sconto-field" value="{{ $libro->pivot->sconto }}">
                </td>
                <td data-label="Valore Scontato">
                    <input type="text" name="netto_a_pagare[]" class="form-control netto_a_pagare" value="{{ $libro->pivot->netto_a_pagare }}" readonly>
                </td>
                <td data-label="Info">
                    <select name="info_spedizione[]" class="form-control">
                        <option value="">Seleziona...</option>
                        <option value="spedito da magazzino editore" {{ $libro->pivot->info_spedizione == 'spedito da magazzino editore' ? 'selected' : '' }}>Spedito da magazzino editore</option>
                        <option value="consegna a mano" {{ $libro->pivot->info_spedizione == 'consegna a mano' ? 'selected' : '' }}>Consegna a mano</option>
                        <option value="spedito da tipografia" {{ $libro->pivot->info_spedizione == 'spedito da tipografia' ? 'selected' : '' }}>Spedito da tipografia</option>
                        <option value="spedito da magazzino terzo" {{ $libro->pivot->info_spedizione == 'spedito da magazzino terzo' ? 'selected' : '' }}>Spedito da magazzino terzo</option>
                        <option value="fuori catalogo" {{ $libro->pivot->info_spedizione == 'fuori catalogo' ? 'selected' : '' }}>Fuori catalogo</option>
                        <option value="momentaneamente non disponibile" {{ $libro->pivot->info_spedizione == 'momentaneamente non disponibile' ? 'selected' : '' }}>Momentaneamente non disponibile</option>
                    </select>
                </td>
                <td data-label="Azioni">
                    <button type="button" class="btn btn-danger removeRow" title="Elimina">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>

            <tfoot class="table-secondary">
                <tr>
                    <td></td>
                    <td><strong>Totali</strong></td>
                    <td><span id="totale_quantita">0</span></td>
                    <td></td>
                    <td><span id="totale_lordo">0.00</span> €</td>
                    <td></td>
                    <td><span id="totale_netto">0.00</span> €</td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
    </table>

        <!-- Totali visibili SOLO su mobile -->
        <div class="d-md-none mt-3">
            <div class="d-flex justify-content-between px-3 py-2 bg-light border-top border-bottom">
                <strong>Totale Quantità:</strong>
                <span id="totale_quantita_mobile">0</span>
            </div>
            <div class="d-flex justify-content-between px-3 py-2 bg-light border-bottom">
                <strong>Totale Lordo:</strong>
                <span id="totale_lordo_mobile">0.00 €</span>
            </div>
            <div class="d-flex justify-content-between px-3 py-2 bg-light border-bottom">
                <strong>Totale Netto:</strong>
                <span id="totale_netto_mobile">0.00 €</span>
            </div>
        </div>

    </div>
</form>

</div>

<style>
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }

    .card, .container, .table-responsive {
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    table.table thead,
    table.table tfoot {
        display: none;
    }

    table.table tbody tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 6px;
        background-color: #fff;
    }

    table.table tbody tr td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 8px;
        font-size: 14px;
        border: none;
        border-bottom: 1px solid #eee;
        word-break: break-word;
    }

    table.table tbody tr td::before {
        content: attr(data-label);
        font-weight: bold;
        flex-shrink: 0;
        margin-right: 10px;
        color: #555;
        min-width: 90px;
    }

    table.table tbody tr td select.form-control,
    table.table tbody tr td select.form-select {
        min-width: 120px;
        flex: 1;
    }

    .form-control,
    .form-select {
        font-size: 14px;
    }

    h3, h5 {
        font-size: 18px;
    }

    .container {
        padding: 10px;
    }
}
</style>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<script>
    const libri = @json($libri);
</script>

<script>
    const disponibilitaEditore = @json($quantitaMagazzinoEditore);
</script>

<script>
$(function() {
    $(document).on("focus", ".titolo-autocomplete", function() {
                $(this).autocomplete({
            source: libri.map(libro => ({
                label: libro.titolo + ' — ' + libro.isbn,
                value: libro.titolo,
                id: libro.id,
                isbn: libro.isbn,
                prezzo: libro.prezzo
            })),
            select: function(event, ui) {
                const row = $(this).closest("tr");
                row.find(".isbn-field").val(ui.item.isbn);
                row.find(".prezzo-field").val(ui.item.prezzo);
                row.find(".libro-id").val(ui.item.id);
            }
        });
    });

    $(document).on("input", ".quantita-field, .sconto-field", function() {
        const row = $(this).closest("tr");
        const quantita = parseFloat(row.find(".quantita-field").val()) || 0;
        const prezzo = parseFloat(row.find(".prezzo-field").val()) || 0;
        const valoreLordo = quantita * prezzo;
        row.find(".valore_vendita_lordo").val(valoreLordo.toFixed(2));

        const sconto = parseFloat(row.find(".sconto-field").val()) || 0;
        const netto = valoreLordo - (valoreLordo * sconto / 100);
        row.find(".netto_a_pagare").val(netto.toFixed(2));

        aggiornaTotaliOrdine();
    });


    $(document).on("focus", ".quantita-field", function () {
    const row = $(this).closest("tr");
    const libroId = row.find(".libro-id").val();
    const stockDiv = row.find(".stock-info");

    if (libroId) {
        const quantitaDisponibile = disponibilitaEditore[libroId] || 0;
        stockDiv.text(`Disponibili: ${quantitaDisponibile}`);
        stockDiv.show();
    } else {
        stockDiv.hide();
    }
    });


    $(document).on("blur", ".quantita-field", function () {
        const row = $(this).closest("tr");
        row.find(".stock-info").fadeOut();
    });


    $(document).on("click", ".removeRow", function() {
        $(this).closest("tr").remove();
    });

    $("#addRow").on("click", function() {
    const newRow = `<tr>
        <td data-label="ISBN"><input type="text" name="isbn[]" class="form-control isbn-field" readonly></td>
        <td data-label="Titolo">
            <input type="text" name="titolo[]" class="form-control titolo-autocomplete" placeholder="Cerca titolo...">
            <input type="hidden" name="libro_id[]" class="libro-id">
            <input type="hidden" class="prezzo-field">
        </td>
        <td data-label="Quantità">
            <input type="number" name="quantita[]" class="form-control quantita-field">
            <div class="stock-info text-muted" style="font-size: 0.8em; display: none; opacity: 0.6;"></div>
        </td>
        <td data-label="Prezzo Copertina"><input type="text" name="prezzo[]" class="form-control prezzo-field" readonly></td>
        <td data-label="Valore Lordo"><input type="text" name="valore_vendita_lordo[]" class="form-control valore_vendita_lordo" readonly></td>
        <td data-label="Sconto (%)"><input type="text" name="sconto[]" class="form-control sconto-field"></td>
        <td data-label="Valore Scontato"><input type="text" name="netto_a_pagare[]" class="form-control netto_a_pagare" readonly></td>
        <td data-label="Info">
            <select name="info_spedizione[]" class="form-control">
                <option value="">Seleziona...</option>
                <option value="spedito da magazzino editore">Spedito da magazzino editore</option>
                <option value="consegna a mano">Consegna a mano</option>
                <option value="spedito da tipografia">Spedito da tipografia</option>
                <option value="spedito da magazzino terzo">Spedito da magazzino terzo</option>
                <option value="fuori catalogo">Fuori catalogo</option>
                <option value="momentaneamente non disponibile">Momentaneamente non disponibile</option>
            </select>
        </td>
        <td data-label="Azioni">
            <button type="button" class="btn btn-danger removeRow" title="Elimina">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>`;

        $("#ordiniBody").append(newRow);
    });
    aggiornaTotaliOrdine();
});
</script>

<script>
document.getElementById('barcode-scan-ordini').addEventListener('input', function(e) {
    const codice = e.target.value.trim();

    if (codice.length < 10) return; // evita trigger su input incompleti

    fetch('/api/libro-da-barcode?isbn=' + codice)
        .then(res => res.json())
        .then(libro => {
            if (!libro) {
                alert('Libro non trovato.');
            } else {
                aggiungiRigaOrdine(libro);
            }
        });

    e.target.value = ''; // resetta per permettere una nuova scansione
});
</script>

<script>
function aggiungiRigaOrdine(libro) {
    const isbnEsistente = Array.from(document.querySelectorAll('input[name="isbn[]"]'))
        .some(input => input.value === libro.isbn);
    if (isbnEsistente) {
        alert("Questo libro è già presente nell'ordine.");
        return;
    }

    const newRow = `
    <tr>
        <td data-label="ISBN">
            <input type="text" name="isbn[]" class="form-control isbn-field" value="${libro.isbn}" readonly>
        </td>
        <td data-label="Titolo">
            <input type="text" name="titolo[]" class="form-control titolo-autocomplete" value="${libro.titolo}" placeholder="cerca/scansiona titolo...">
            <input type="hidden" name="libro_id[]" class="libro-id" value="${libro.id}">
            <input type="hidden" name="isbn[]" class="isbn-field" value="${libro.isbn}">
            <input type="hidden" name="prezzo[]" class="prezzo-field" value="${libro.prezzo}">
        </td>
        <td data-label="Quantità">
            <input type="number" name="quantita[]" class="form-control quantita-field" value="1">
            <div class="stock-info text-muted" style="font-size: 0.8em; display: none; opacity: 0.6;"></div>
        </td>
        <td data-label="Prezzo Copertina">
            <input type="text" name="prezzo[]" class="form-control prezzo-field" value="${libro.prezzo}" readonly>
        </td>
        <td data-label="Valore Lordo">
            <input type="text" name="valore_vendita_lordo[]" class="form-control valore_vendita_lordo" value="${parseFloat(libro.prezzo).toFixed(2)}" readonly>
        </td>
        <td data-label="Sconto (%)">
            <input type="text" name="sconto[]" class="form-control sconto-field" value="0">
        </td>
        <td data-label="Valore Scontato">
            <input type="text" name="netto_a_pagare[]" class="form-control netto_a_pagare" value="${parseFloat(libro.prezzo).toFixed(2)}" readonly>
        </td>
        <td data-label="Info">
            <select name="info_spedizione[]" class="form-control">
                <option value="">Seleziona...</option>
                <option value="spedito da magazzino editore">Spedito da magazzino editore</option>
                <option value="consegna a mano">Consegna a mano</option>
                <option value="spedito da tipografia">Spedito da tipografia</option>
                <option value="spedito da magazzino terzo">Spedito da magazzino terzo</option>
                <option value="fuori catalogo">Fuori catalogo</option>
                <option value="momentaneamente non disponibile">Momentaneamente non disponibile</option>
            </select>
        </td>
        <td data-label="Azioni">
            <button type="button" class="btn btn-danger removeRow" title="Elimina">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>`;

    $('#ordiniBody').append(newRow);
    $('#ordiniBody tr:last-child input[name="quantita[]"]').focus();
    aggiornaTotaliOrdine();

}

function aggiornaTotaliOrdine() {
    let totaleQuantita = 0;
    let totaleLordo = 0;
    let totaleNetto = 0;

    $('#ordiniBody tr').each(function() {
        const q = parseFloat($(this).find('.quantita-field').val()) || 0;
        const l = parseFloat($(this).find('.valore_vendita_lordo').val()) || 0;
        const n = parseFloat($(this).find('.netto_a_pagare').val()) || 0;

        totaleQuantita += q;
        totaleLordo += l;
        totaleNetto += n;
    });

    $('#totale_quantita').text(totaleQuantita);
    $('#totale_lordo').text(totaleLordo.toFixed(2));
    $('#totale_netto').text(totaleNetto.toFixed(2));

    document.getElementById('totale_quantita_mobile').innerText = totaleQuantita;
    document.getElementById('totale_lordo_mobile').innerText = totaleLordo.toFixed(2) + " €";
    document.getElementById('totale_netto_mobile').innerText = totaleNetto.toFixed(2) + " €";


}

</script>

@endsection
