@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Gestione Libri - Ordine {{ $ordine->codice }}</h3>

    @if($ordine->exists)
    <div class="alert alert-warning mt-3 text-center">
        ⚠️ Stai modificando un ordine esistente.<br>
        Ricorda di aggiornare manualmente le quantità in <strong>Magazzino</strong> se cambi le quantità dei libri.
    </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('ordini.index') }}" class="btn btn-secondary">Torna agli Ordini</a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Importa libri da Excel</h5>
            <form action="{{ route('ordini.import.libri', $ordine->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx" required class="form-control mb-2">
                <button type="submit" class="btn btn-primary">Importa</button>
            </form>
        </div>
    </div>

    <form action="{{ route('ordini.libri.store', $ordine->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary mt-3">Salva</button>

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
        <div class="mb-3 mt-4">
            <label for="specifiche_iva" class="form-label"><strong>Specifiche IVA</strong></label>
            <input type="text" name="specifiche_iva" maxlength="255" class="form-control" value="{{ old('specifiche_iva', $ordine->specifiche_iva ?? "IVA assolta all'origine dall'editore, ai sensi dell'art.74 co. 1 lett. c del DPR 633/72") }}">
        </div>
        <div class="mb-3">
            <label for="costo_spedizione" class="form-label"><strong>Costo spedizione</strong></label>
            <input type="text" name="costo_spedizione" maxlength="255" class="form-control" value="{{ old('costo_spedizione', $ordine->costo_spedizione) }}">
        </div>
        <div class="mb-3">
            <label for="altre_specifiche_iva" class="form-label"><strong>Altre specifiche IVA</strong></label>
            <input type="text" name="altre_specifiche_iva" maxlength="255" class="form-control" value="{{ old('altre_specifiche_iva', $ordine->altre_specifiche_iva) }}">
        </div>
        <div class="mb-3">
            <label for="totale_netto_compilato" class="form-label"><strong>Totale Netto da Pagare (modificabile)</strong></label>
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
            <button type="button" class="btn btn-success" id="addRow">Aggiungi Riga</button>
            <button type="submit" class="btn btn-primary">Salva</button>
        </div>

        <h5 class="mt-5">Elenco Libri</h5>
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
                    <td><input type="text" name="isbn[]" class="form-control isbn-field" value="{{ $libro->isbn }}" readonly></td>
                    <td>
                        <input type="text" name="titolo[]" class="form-control titolo-autocomplete" placeholder="Cerca titolo..." value="{{ $libro->titolo }}">
                        <input type="hidden" name="libro_id[]" class="libro-id" value="{{ $libro->id }}">
                        <input type="hidden" name="isbn[]" class="isbn-field" value="{{ $libro->isbn }}">
                        <input type="hidden" name="prezzo[]" class="prezzo-field" value="{{ $libro->prezzo }}">
                    </td>

                    <td><input type="number" name="quantita[]" class="form-control quantita-field" value="{{ $libro->pivot->quantita }}"></td>
                    <td><input type="text" name="prezzo[]" class="form-control prezzo-field" value="{{ $libro->prezzo }}" readonly></td>
                    <td><input type="text" name="valore_vendita_lordo[]" class="form-control valore_vendita_lordo" value="{{ $libro->pivot->valore_vendita_lordo }}" readonly></td>
                    <td><input type="text" name="sconto[]" class="form-control sconto-field" value="{{ $libro->pivot->sconto }}"></td>
                    <td><input type="text" name="netto_a_pagare[]" class="form-control netto_a_pagare" value="{{ $libro->pivot->netto_a_pagare }}" readonly></td>
                    <td>
                        <select name="info_spedizione[]" class="form-control">
                            <option value="">Seleziona...</option>
                            <option value="spedito da magazzino editore" {{ $libro->pivot->info_spedizione == 'spedito da magazzino editore' ? 'selected' : '' }}>Spedito da magazzino editore</option>
                            <option value="spedito da tipografia" {{ $libro->pivot->info_spedizione == 'spedito da tipografia' ? 'selected' : '' }}>Spedito da tipografia</option>
                            <option value="spedito da magazzino terzo" {{ $libro->pivot->info_spedizione == 'spedito da magazzino terzo' ? 'selected' : '' }}>Spedito da magazzino terzo</option>
                            <option value="fuori catalogo" {{ $libro->pivot->info_spedizione == 'fuori catalogo' ? 'selected' : '' }}>Fuori catalogo</option>
                            <option value="momentaneamente non disponibile" {{ $libro->pivot->info_spedizione == 'momentaneamente non disponibile' ? 'selected' : '' }}>Momentaneamente non disponibile</option>
                        </select>
                    </td>
                    <td><button type="button" class="btn btn-danger removeRow">Elimina</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<script>
    const libri = @json($libri);
</script>

<script>
$(function() {
    $(document).on("focus", ".titolo-autocomplete", function() {
        $(this).autocomplete({
            source: libri.map(libro => libro.titolo),
            select: function(event, ui) {
                const titolo = ui.item.value;
                const libroTrovato = libri.find(l => l.titolo === titolo);
                const row = $(this).closest("tr");

                if (libroTrovato) {
                    row.find(".isbn-field").val(libroTrovato.isbn);
                    row.find(".prezzo-field").val(libroTrovato.prezzo);
                    row.find(".libro-id").val(libroTrovato.id);
                }
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
    });

    $(document).on("click", ".removeRow", function() {
        $(this).closest("tr").remove();
    });

    $("#addRow").on("click", function() {
        const newRow = `<tr>
            <td><input type="text" name="isbn[]" class="form-control isbn-field" readonly></td>
            <td>
                <input type="text" name="titolo[]" class="form-control titolo-autocomplete" placeholder="Cerca titolo...">
                <input type="hidden" name="libro_id[]" class="libro-id">
                <input type="hidden" class="prezzo-field">
            </td>
            <td><input type="number" name="quantita[]" class="form-control quantita-field"></td>
            <td><input type="text" name="prezzo[]" class="form-control prezzo-field" readonly></td>
            <td><input type="text" name="valore_vendita_lordo[]" class="form-control valore_vendita_lordo" readonly></td>
            <td><input type="text" name="sconto[]" class="form-control sconto-field"></td>
            <td><input type="text" name="netto_a_pagare[]" class="form-control netto_a_pagare" readonly></td>
            <td>
                <select name="info_spedizione[]" class="form-control">
                    <option value="">Seleziona...</option>
                    <option value="spedito da magazzino editore">Spedito da magazzino editore</option>
                    <option value="spedito da tipografia">Spedito da tipografia</option>
                    <option value="spedito da magazzino terzo">Spedito da magazzino terzo</option>
                    <option value="fuori catalogo">Fuori catalogo</option>
                    <option value="momentaneamente non disponibile">Momentaneamente non disponibile</option>
                </select>
            </td>
            <td><button type="button" class="btn btn-danger removeRow">Elimina</button></td>
        </tr>`;
        $("#ordiniBody").append(newRow);
    });
});
</script>
@endsection
