@extends('layouts.app')

@section('content')

@php
    $righe = session('righe_ambigue', []);
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

    @if(session('import_errori'))
        <div class="alert alert-danger mt-3">
            <strong>Alcune righe non sono state importate:</strong>
            <ul class="mb-0">
                @foreach(session('import_errori') as $errore)
                    <li>{{ $errore }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
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
                                            <select name="righe[{{ $index }}][isbn]" class="form-select libro-select" data-index="{{ $index }}" required>
                                                <option value="">-- Seleziona --</option>
                                                @foreach($riga['opzioni'] as $libro)
                                                    <option value="{{ $libro['isbn'] }}" data-titolo="{{ $libro['titolo'] }}">
                                                        {{ $libro['titolo'] }} ({{ $libro['isbn'] }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="righe[{{ $index }}][titolo]" id="titolo-hidden-{{ $index }}" value="">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Conferma e importa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="button" id="addRow" class="btn btn-success">Aggiungi Riga</button>
        <form action="{{ route('registro-vendite.gestione', ['id' => $registroVendita->id]) }}" method="GET" class="d-flex" style="max-width: 300px;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cerca per titolo...">
            <button class="btn btn-outline-primary">Cerca</button>
        </form>
        <form id="registroVenditeForm" action="{{ route('registro-vendite.salvaDettagli', ['id' => $registroVendita->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="registro_vendita_id" value="{{ $registroVendita->id }}">
            <button type="submit" class="btn btn-primary">Salva</button>

            <h5>Elenco Vendite</h5>

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
                            <td><input type="date" name="data[]" value="{{ $dettaglio->data }}" class="form-control"></td>
                            <td><input type="text" name="periodo[]" value="{{ $dettaglio->periodo }}" class="form-control"></td>
                            <td><input type="text" name="isbn[]" value="{{ $dettaglio->isbn }}" class="form-control isbn"></td>
                            <td><input type="text" name="titolo[]" class="form-control titolo" value="{{ $dettaglio->titolo }}" placeholder="Cerca titolo..."></td>
                            <td><input type="number" name="quantita[]" value="{{ $dettaglio->quantita }}" class="form-control quantita"></td>
                            <td><input type="number" name="prezzo[]" value="{{ $dettaglio->prezzo }}" class="form-control prezzo" step="0.01"></td>
                            <td><input type="number" name="valore_lordo[]" value="{{ $dettaglio->quantita * $dettaglio->prezzo }}" class="form-control valore-lordo" readonly></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm delete-row">Elimina</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {{ $dettagli->links() }}
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const righeAmbigue = {!! json_encode($righe ?? []) !!};
    const libri = @json($libri);

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
            <td><input type="date" name="data[]" value="{{ date('Y-m-d') }}" class="form-control"></td>
            <td><input type="text" name="periodo[]" class="form-control"></td>
            <td><input type="text" name="isbn[]" class="form-control isbn" readonly></td>
            <td><input type="text" name="titolo[]" class="form-control titolo" placeholder="Cerca titolo..."></td>
            <td><input type="number" name="quantita[]" value="0" class="form-control quantita"></td>
            <td><input type="number" name="prezzo[]" value="0.00" class="form-control prezzo" step="0.01"></td>
            <td><input type="number" name="valore_lordo[]" value="0.00" class="form-control valore-lordo" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm delete-row">Elimina</button></td>
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

    // Mostra popup se ci sono righe ambigue
    if (Array.isArray(righeAmbigue) && righeAmbigue.length > 0) {
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
});
</script>
@endsection
