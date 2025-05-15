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

    @if(session('import_errori') || session('import_errori_persistenti'))
        <div id="erroriImport" class="alert alert-danger mt-3 position-relative">
            <strong>⚠️ Alcune righe non sono state importate:</strong>
            <ul class="mb-0 mt-2">
                @foreach(session('import_errori') ?? [] as $errore)
                    <li>{{ $errore }}</li>
                @endforeach
                @foreach(session('import_errori_persistenti') ?? [] as $errore)
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

        <form action="{{ route('registro-vendite.gestione', ['id' => $registroVendita->id]) }}" method="GET" class="d-flex flex-wrap gap-2 mb-3" style="max-width: 100%;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cerca per titolo...">
            <button class="btn btn-outline-primary">Cerca</button>
        </form>

        <form id="registroVenditeForm" action="{{ route('registro-vendite.salvaDettagli', ['id' => $registroVendita->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="registro_vendita_id" value="{{ $registroVendita->id }}">
            <button type="submit" class="btn btn-primary mb-3">Salva</button>

            <h5>Elenco Vendite</h5>

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
                        @foreach($dettagli as $dettaglio)
                            <tr data-id="{{ $dettaglio->id }}">
                                <input type="hidden" name="id[]" value="{{ $dettaglio->id }}">
                                    <td data-label="Data"><input type="date" name="data[]" value="{{ $dettaglio->data }}" class="form-control"></td>
                                    <td data-label="Periodo"><input type="text" name="periodo[]" value="{{ $dettaglio->periodo }}" class="form-control"></td>
                                    <td data-label="ISBN"><input type="text" name="isbn[]" value="{{ $dettaglio->isbn }}" class="form-control isbn"></td>
                                    <td data-label="Titolo"><input type="text" name="titolo[]" class="form-control titolo" value="{{ $dettaglio->titolo }}" placeholder="Cerca titolo..."></td>
                                    <td data-label="Quantità"><input type="number" name="quantita[]" value="{{ $dettaglio->quantita }}" class="form-control quantita"></td>
                                    <td data-label="Prezzo"><input type="number" name="prezzo[]" value="{{ $dettaglio->prezzo }}" class="form-control prezzo" step="0.01"></td>
                                    <td data-label="Valore Lordo"><input type="number" name="valore_lordo[]" value="{{ $dettaglio->quantita * $dettaglio->prezzo }}" class="form-control valore-lordo" readonly></td>
                                    <td data-label="Azioni"><button type="button" class="btn btn-danger btn-sm delete-row">Elimina</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $dettagli->links() }}
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
</style>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const righeAmbigue = {!! json_encode($righe ?? []) !!};
    const libri = @json($libri);

    if (righeAmbigue.length > 0) {
        let modal = new bootstrap.Modal(document.getElementById('popupConflitti'));
        modal.show();

        document.querySelectorAll('.libro-select').forEach(function(select) {
            select.addEventListener('change', function() {
                let index = this.dataset.index;
                let selected = this.options[this.selectedIndex];
                let titolo = selected.getAttribute('data-titolo') || '';
                document.getElementById('titolo-hidden-' + index).value = titolo;
            });
        });

        fetch("{{ route('registro-vendite.clear-conflitti-sessione') }}");
    }

    function aggiornaValoreLordo(row) {
        let quantita = parseFloat(row.querySelector(".quantita")?.value || 0);
        let prezzo = parseFloat(row.querySelector(".prezzo")?.value || 0);
        row.querySelector(".valore-lordo").value = (quantita * prezzo).toFixed(2);
    }

    function initAutocomplete(input) {
        $(input).autocomplete({
            source: libri.map(libro => ({
                label: libro.titolo + " (" + libro.isbn + ")",
                value: libro.titolo,
                isbn: libro.isbn,
                prezzo: libro.prezzo
            })),
            select: function(event, ui) {
                let parentRow = $(this).closest("tr");
                parentRow.find(".isbn").val(ui.item.isbn);
                parentRow.find(".prezzo").val(ui.item.prezzo);
                aggiornaValoreLordo(parentRow[0]);
            }
        });
    }

    document.querySelectorAll(".titolo").forEach(initAutocomplete);

    document.getElementById("addRow").addEventListener("click", function() {
        let newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td data-label="Data"><input type="date" name="data[]" value="{{ date('Y-m-d') }}" class="form-control" placeholder="Data"></td>
            <td data-label="Periodo"><input type="text" name="periodo[]" class="form-control" placeholder="Periodo"></td>
            <td data-label="ISBN"><input type="text" name="isbn[]" class="form-control isbn" placeholder="ISBN" readonly></td>
            <td data-label="Titolo"><input type="text" name="titolo[]" class="form-control titolo" placeholder="Cerca titolo..."></td>
            <td data-label="Quantità"><input type="number" name="quantita[]" value="0" class="form-control quantita" placeholder="Quantità"></td>
            <td data-label="Prezzo"><input type="number" name="prezzo[]" value="0.00" class="form-control prezzo" step="0.01" placeholder="Prezzo"></td>
            <td data-label="Valore Lordo"><input type="number" name="valore_lordo[]" value="0.00" class="form-control valore-lordo" readonly placeholder="Valore Lordo"></td>
            <td data-label="Azioni"><button type="button" class="btn btn-danger btn-sm delete-row">Elimina</button></td>
        `;

        document.getElementById("registroVenditeBody").prepend(newRow);
        initAutocomplete(newRow.querySelector(".titolo"));
    });

    document.querySelectorAll(".delete-row").forEach(btn => {
        btn.addEventListener("click", function() {
            let row = this.closest("tr");
            let dettaglioId = row.dataset.id;
            if (dettaglioId) {
                if (confirm("Vuoi davvero eliminare questa riga?")) {
                    fetch(`/registro-vendite/dettaglio/${dettaglioId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }).then(res => res.json()).then(data => {
                        if (data.success) row.remove();
                        else alert("Errore nell'eliminazione della riga.");
                    }).catch(() => alert("Errore nella richiesta."));
                }
            } else {
                row.remove();
            }
        });
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const btnChiudi = document.getElementById("chiudiErroriImport");
    const divErrori = document.getElementById("erroriImport");

    if (btnChiudi && divErrori) {
        btnChiudi.addEventListener("click", function () {
            // Nascondi visivamente
            divErrori.style.display = "none";

            // Cancella dalla sessione via AJAX
            fetch("{{ route('registro-vendite.clear-errori-sessione') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert("Errore nella rimozione dell'elenco errori.");
                }
            })
            .catch(() => alert("Errore nella richiesta."));

        });
    }
});
</script>

@endsection
