@extends('layouts.app')

@section('content')

@php
    $righe = session()->pull('righe_ambigue', []);
@endphp

<div class="container mt-5">
    <h3 class="text-center mb-4">Gestione Registro Vendite - {{ $registroVendita->anagrafica->nome }}</h3>

    <div class="mb-3">
        <a href="{{ route('registro-vendite.index') }}" class="btn btn-secondary">Torna ai Registri Vendite</a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Importa Vendite da Excel</h5>
            <form action="{{ route('registro-vendite.import', $registroVendita->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx" required class="form-control mb-2">
                <button type="submit" class="btn btn-primary">Importa</button>
            </form>
        </div>
    </div>

    @php
        $erroriImport = session()->pull('import_errori', []);
        $erroriPersistenti = session()->pull('import_errori_persistenti', []);
    @endphp

    @if($erroriImport || $erroriPersistenti)
        <div id="erroriImport" class="alert alert-danger mt-3 position-relative">
            <strong>⚠️ Alcune righe non sono state importate:</strong>
            <ul class="mb-0 mt-2">
                @foreach($erroriImport as $errore)
                    <li>{{ $errore }}</li>
                @endforeach
                @foreach($erroriPersistenti as $errore)
                    <li>{{ $errore }}</li>
                @endforeach
            </ul>
            <button id="chiudiErroriImport" class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-2">❌ Chiudi elenco errori</button>
        </div>
    @endif


    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    <!-- MODALE Bootstrap per righe ambigue -->
    <div class="modal fade" id="popupConflitti" tabindex="-1" aria-labelledby="popupLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST" action="{{ route('registro-vendite.risolviConflitti', $registroVendita->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Risolvi conflitti importazione</h5>
                    </div>
                    <div class="modal-body">
                        <p>Alcune righe hanno titoli ambigui. Seleziona il libro corretto:</p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Periodo</th>
                                        <th>Quantità</th>
                                        <th>Seleziona libro corretto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($righe as $index => $riga)
                                        <tr>
                                            <td><input type="date" name="righe[{{ $index }}][data]" value="{{ $riga['data'] }}" class="form-control"></td>
                                            <td><input type="text" name="righe[{{ $index }}][periodo]" value="{{ $riga['periodo'] ?? 'N/D' }}" class="form-control"></td>
                                            <td><input type="number" name="righe[{{ $index }}][quantita]" value="{{ $riga['quantita'] }}" class="form-control"></td>
                                            <td>
                                                @if (!empty($riga['opzioni']))
                                                    <select name="righe[{{ $index }}][isbn]" class="form-select libro-select" data-index="{{ $index }}" required>
                                                        <option value="">-- Seleziona --</option>
                                                        @foreach($riga['opzioni'] as $libro)
                                                            <option value="{{ $libro['isbn'] }}" data-titolo="{{ $libro['titolo'] }}">
                                                                {{ $libro['titolo'] }} ({{ $libro['isbn'] }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="righe[{{ $index }}][titolo]" id="titolo-hidden-{{ $index }}" value="">
                                                @else
                                                    <div class="text-danger mb-1">Nessuna corrispondenza trovata</div>
                                                    <input type="hidden" name="righe[{{ $index }}][isbn]" value="__SKIP__">
                                                    <input type="hidden" name="righe[{{ $index }}][titolo]" value="">
                                                    <span class="badge bg-warning text-dark">Riga ignorata</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Conferma e importa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="button" id="addRow" class="btn btn-success mb-3">Aggiungi Riga</button>
        <div class="mt-2 mb-3" style="max-width: 400px;">
            <input type="text" id="barcode-scan-registro" class="form-control" placeholder="Scansiona codice a barre...">
        </div>


        <form action="{{ route('registro-vendite.gestione', ['id' => $registroVendita->id]) }}" method="GET" class="d-flex flex-wrap gap-2 mb-3" style="max-width: 100%;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cerca per titolo...">
            <button class="btn btn-outline-primary">Cerca</button>
        </form>

        <form id="registroVenditeForm" action="{{ route('registro-vendite.salvaDettagli', ['id' => $registroVendita->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="registro_vendita_id" value="{{ $registroVendita->id }}">
            <button type="submit" class="btn btn-primary mb-3">Salva</button>

            <div class="text-end mb-3">
                <div class="d-inline-block me-4">
                    <label class="fw-bold">Totale valore venduto:</label><br>
                    <input type="text" class="form-control text-end fw-bold" style="max-width: 200px;" value="{{ number_format($totaleValoreLordo, 2, ',', '.') }}" readonly>
                </div>

                <div class="d-inline-block">
                    <label class="fw-bold">Totale copie vendute:</label><br>
                    <input type="text" class="form-control text-end fw-bold" style="max-width: 200px;" value="{{ $totaleQuantita }}" readonly>
                </div>
            </div>


        <div class="row justify-content-end align-items-end g-2 mt-4">
            <!-- Date + pulsanti -->
            <div class="col-auto">
                <label for="filtro-da" class="form-label mb-1">Da</label>
                <input type="date" id="filtro-da" class="form-control" value="{{ request('data_da') }}">
            </div>

            <div class="col-auto">
                <label for="filtro-a" class="form-label mb-1">A</label>
                <input type="date" id="filtro-a" class="form-control" value="{{ request('data_a') }}">
            </div>

            <div class="col-auto d-flex align-items-end">
                <button type="button" class="btn btn-secondary" id="calcola-parziali">Calcola parziali</button>
            </div>

            <!-- Totali parziali -->
            <div class="col-12 text-end mt-3">
                <div class="d-inline-block me-4">
                    <label class="fw-bold">Valore lordo (intervallo):</label><br>
                    <input type="text" id="valore-lordo-parziale" class="form-control text-end fw-bold" style="max-width: 200px;" readonly>
                </div>

                <div class="d-inline-block">
                    <label class="fw-bold">Copie vendute (intervallo):</label><br>
                    <input type="text" id="copie-vendute-parziale" class="form-control text-end fw-bold" style="max-width: 200px;" readonly>
                </div>
            </div>
        </div>

            <h5>Elenco Vendite</h5>

            @if($dettagli->isEmpty())
                <div class="alert alert-warning mt-3">
                    Nessuna riga da visualizzare. Aggiungi righe oppure verifica i filtri di ricerca.
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Data</th>
                            <th>Periodo</th>
                            <th>ISBN</th>
                            <th>Titolo</th>
                            <th>Quantità</th>
                            <th>Prezzo</th>
                            <th>Valore Lordo</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody id="registroVenditeBody">
                        @foreach($dettagli as $i => $dettaglio)
                        <tr data-id="{{ $dettaglio->id }}">
                            <td>
                                <input type="hidden" name="righe[{{ $i }}][id]" value="{{ $dettaglio->id }}">
                                <input type="date" name="righe[{{ $i }}][data]" value="{{ $dettaglio->data }}" class="form-control">
                            </td>
                            <td>
                                <input type="text" name="righe[{{ $i }}][periodo]" value="{{ $dettaglio->periodo }}" class="form-control">
                            </td>
                            <td>
                                <input type="text" name="righe[{{ $i }}][isbn]" value="{{ $dettaglio->isbn }}" class="form-control isbn">
                            </td>
                            <td>
                                <input type="text" name="righe[{{ $i }}][titolo]" value="{{ $dettaglio->titolo }}" class="form-control titolo">
                            </td>
                            <td>
                                <input type="number" name="righe[{{ $i }}][quantita]" value="{{ $dettaglio->quantita }}" class="form-control quantita">
                            </td>
                            <td>
                                <input type="number" name="righe[{{ $i }}][prezzo]" value="{{ $dettaglio->prezzo }}" class="form-control prezzo" step="0.01">
                            </td>
                            <td>
                                <input type="number" name="righe[{{ $i }}][valore_lordo]" value="{{ $dettaglio->valore_lordo }}" class="form-control valore-lordo" step="0.01">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm delete-row">Elimina</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $dettagli->links() }}
            </div>
    </div>

    </form> {{-- chiusura registroVenditeForm --}}

            <div class="text-end mt-3">
            <form action="{{ route('registro-vendite.stampa', $registroVendita->id) }}" method="GET" target="_blank">
                <div class="row justify-content-end align-items-end g-2 mt-4">
                    <div class="col-auto">
                        <label for="data_da" class="form-label mb-1">Da</label>
                        <input type="date" class="form-control" name="data_da" id="data_da" value="{{ request('data_da') }}">
                    </div>

                    <div class="col-auto">
                        <label for="data_a" class="form-label mb-1">A</label>
                        <input type="date" class="form-control" name="data_a" id="data_a" value="{{ request('data_a') }}">
                    </div>

                    <div class="col-auto d-flex align-items-end">
                        <button type="submit" class="btn btn-danger">📄 Stampa PDF</button>
                    </div>
                </div>
            </form>
        </div>
</div>

<style>
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }

    table.table thead {
        display: none;
    }

    table.table tbody tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 6px;
    }

    table.table tbody tr td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 8px;
        font-size: 14px;
        border: none;
        border-bottom: 1px solid #eee;
    }

    table.table tbody tr td::before {
        content: attr(data-label);
        font-weight: bold;
        flex-shrink: 0;
        margin-right: 10px;
        color: #555;
    }

    .btn-sm {
        padding: 4px 8px;
        font-size: 13px;
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


input[readonly] {
    background-color: #f0f0f0 !important;
    color: #6c757d !important;
}

input.valore-lordo {
    color: red !important;
}

</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    const righeAmbigue = {!! json_encode($righe ?? []) !!};
    const libri = @json($libri);

    function calcolaPeriodo(dateStr) {
        if (!dateStr) return '';
        const mesi = ["gennaio","febbraio","marzo","aprile","maggio","giugno","luglio","agosto","settembre","ottobre","novembre","dicembre"];
        const parti = dateStr.split('-');
        if (parti.length !== 3) return '';
        return mesi[parseInt(parti[1], 10) - 1] + dateStr.slice(2, 4);
    }

    function aggiornaValoreLordo(row) {
        let quantita = parseFloat($(row).find(".quantita").val() || 0);
        let prezzo = parseFloat($(row).find(".prezzo").val() || 0);
        $(row).find(".valore-lordo").val((quantita * prezzo).toFixed(2));
        aggiornaTotaleValoreVendita();
    }

    function aggiornaTotaleValoreVendita() {
        let totale = 0;
        $('.valore-lordo').each(function () {
            totale += parseFloat($(this).val()) || 0;
        });
        $('#totale-valore-vendita').val(totale.toFixed(2));
        aggiornaTotaleCopieVendute();
    }

    function aggiornaTotaleCopieVendute() {
        let totale = 0;
        $('.quantita').each(function () {
            totale += parseInt($(this).val()) || 0;
        });
        $('#totale-copie-vendute').val(totale);
    }

    function initAutocomplete(input) {
        $(input).autocomplete({
            source: libri.map(libro => ({
                label: `${libro.titolo} (${libro.isbn})`,
                value: libro.titolo,
                isbn: libro.isbn,
                prezzo: libro.prezzo
            })),
            select: function(event, ui) {
                let row = $(this).closest("tr");
                row.find(".isbn").val(ui.item.isbn);
                row.find(".prezzo").val(ui.item.prezzo);
                $(this).val(ui.item.value);
                aggiornaValoreLordo(row);
                return false;
            }
        }).autocomplete("instance")._renderItem = function(ul, item) {
            return $("<li>").append(`<div>${item.label}</div>`).appendTo(ul);
        };
    }

    $('.titolo').each(function () {
        initAutocomplete(this);
    });

    function gestisciEventiElimina() {
        $(".delete-row").off("click").on("click", function () {
            const row = $(this).closest("tr");
            const dettaglioId = row.data("id");
            if (dettaglioId) {
                if (confirm("Vuoi davvero eliminare questa riga?")) {
                    fetch(`/registro-vendite/dettaglio/${dettaglioId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                    }).then(res => res.json()).then(data => {
                        if (data.success) row.remove();
                        else alert("Errore nell'eliminazione.");
                    }).catch(() => alert("Errore nella richiesta."));
                }
            } else {
                row.remove();
            }
        });
    }

    gestisciEventiElimina();

    $(document).on('keydown', '#barcode-scan-registro', function(e) {
        if (e.key === 'Enter' || e.which === 13) {
            e.preventDefault();
            const scannedCode = $(this).val().trim();
            if (!scannedCode) return;

            const today = new Date().toISOString().slice(0, 10); // YYYY-MM-DD
            let rigaTrovata = null;

            $('#registroVenditeBody tr').each(function () {
                const data = $(this).find('input[name*="[data]"]').val();
                const isbn = $(this).find('.isbn').val().trim();
                if (data === today && isbn === scannedCode) {
                    rigaTrovata = $(this);
                    return false;
                }
            });

            if (rigaTrovata) {
                rigaTrovata.find('.quantita').focus();
                $('#barcode-scan-registro').val('');
                return;
            }

            if (!confirm(`Non esiste ancora una riga con ISBN "${scannedCode}" per la data odierna (${today}).\nVuoi aggiungerla?`)) {
                $('#barcode-scan-registro').val('');
                return;
            }

            const newIndex = $('#registroVenditeBody tr').length;
            const $newRow = $(`
                <tr>
                  <td><input type="hidden" name="righe[${newIndex}][id]" value="">
                      <input type="date" name="righe[${newIndex}][data]" value="${today}" class="form-control data-row"></td>
                  <td><input type="text" name="righe[${newIndex}][periodo]" class="form-control periodo-row"></td>
                  <td><input type="text" name="righe[${newIndex}][isbn]" class="form-control isbn" value="${scannedCode}" readonly></td>
                  <td><input type="text" name="righe[${newIndex}][titolo]" class="form-control titolo" placeholder="Cerca titolo..."></td>
                  <td><input type="number" name="righe[${newIndex}][quantita]" value="0" class="form-control quantita"></td>
                  <td><input type="number" name="righe[${newIndex}][prezzo]" value="0.00" class="form-control prezzo" step="0.01" readonly></td>
                  <td><input type="number" name="righe[${newIndex}][valore_lordo]" value="0.00" class="form-control valore-lordo" step="0.01"></td>
                  <td><button type="button" class="btn btn-danger btn-sm delete-row">Elimina</button></td>
                </tr>
            `);

            const libro = libri.find(l => l.isbn === scannedCode);
            if (libro) {
                $newRow.find('.titolo').val(libro.titolo);
                $newRow.find('.prezzo').val(parseFloat(libro.prezzo).toFixed(2));
                aggiornaValoreLordo($newRow);
            }

            $newRow.find('.data-row').on('change', function () {
                const nuovaData = $(this).val();
                $newRow.find('.periodo-row').val(calcolaPeriodo(nuovaData));
            }).trigger('change');

            initAutocomplete($newRow.find('.titolo'));
            $newRow.find('.quantita, .prezzo').on('input', function () {
                aggiornaValoreLordo($newRow);
            });

            $('#registroVenditeBody').prepend($newRow);
            gestisciEventiElimina();
            $newRow.find('.quantita').focus();
            $('#barcode-scan-registro').val('');
        }
    });

});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    function calcolaParziali() {
        const da = document.getElementById('filtro-da').value;
        const a = document.getElementById('filtro-a').value;

        let totaleValore = 0;
        let totaleCopie = 0;

        document.querySelectorAll('#registroVenditeBody tr').forEach(row => {
            const dataStr = row.querySelector('input[name="data[]"]').value;
            const data = new Date(dataStr);
            const dataDa = new Date(da);
            const dataA = new Date(a);

            if (!dataStr || isNaN(data.getTime())) return;
            if (da && data < dataDa) return;
            if (a && data > dataA) return;

            const quantita = parseInt(row.querySelector('.quantita')?.value) || 0;
            const valore = parseFloat(row.querySelector('.valore-lordo')?.value) || 0;

            totaleCopie += quantita;
            totaleValore += valore;
        });

        document.getElementById('valore-lordo-parziale').value = totaleValore.toFixed(2);
        document.getElementById('copie-vendute-parziale').value = totaleCopie.toString();
    }

    // Focus automatico all’apertura
    $('#barcode-scan-registro').val('').focus();

    // Calcolo parziali
    document.getElementById('calcola-parziali')?.addEventListener('click', calcolaParziali);
});


</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formStampa = document.getElementById('form-stampa');
    if (formStampa) {
        formStampa.addEventListener('submit', function (e) {
            const dataDa = document.getElementById('filtro-da')?.value;
            const dataA = document.getElementById('filtro-a')?.value;

            document.getElementById('dataDaHidden').value = dataDa;
            document.getElementById('dataAHidden').value = dataA;
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnChiudi = document.getElementById('chiudiErroriImport');
    const boxErrori = document.getElementById('erroriImport');

    if (btnChiudi && boxErrori) {
        btnChiudi.addEventListener('click', function () {
            boxErrori.style.display = 'none';
        });
    }
});
</script>

@endsection
