@extends('layouts.app')

@section('content')

<script>
    const categoria = "{{ $magazzino->anagrafica->categoria }}";
</script>

<div class="container mt-5">
    <h2 class="text-center mb-4">Gestione Giacenze - {{ $magazzino->anagrafica->nome ?? 'Sconosciuto' }}</h2>

    <div class="alert alert-warning text-center" role="alert">
    ⚠️ <strong>Attenzione:</strong> usa il pulsante <strong>Salva</strong> in alto per registrare le nuove righe aggiunte.<br>
    Per modificare una riga esistente, utilizza invece l'icona <i class="bi bi-save"></i> nella colonna <strong>Azioni</strong>.
</div>


    <a href="{{ route('magazzini.index') }}" class="btn btn-secondary">Torna ai Magazzini</a>

    <hr>

    <h4>Importa giacenze da Excel</h4>
    <form action="{{ route('giacenze.import', ['magazzino' => $magazzino->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit" class="btn btn-primary">Importa Excel</button>
    </form>

    <hr>

    <h4>Aggiungi manualmente</h4>
    <button id="addRow" class="btn btn-success">
    <i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Aggiungi Riga</span>
</button>
    <button id="saveTable" class="btn btn-primary">
    <i class="bi bi-save"></i> <span class="d-none d-md-inline">Salva</span>
</button>

    <a href="{{ route('giacenze.export', ['magazzino' => $magazzino->id]) }}" class="btn btn-outline-success">Esporta Excel</a>
    <form action="{{ route('giacenze.create', $magazzino->id) }}" method="GET" class="d-flex" style="max-width: 300px;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cerca per titolo...">
        <button class="btn btn-outline-primary">Cerca</button>
    </form>

        <hr>

    <h4>Aggiungi con penna ottica</h4>
    <div class="mt-3 mb-3" style="max-width: 400px;">
        <input type="text" id="barcode-scan-giacenze" class="form-control" placeholder="Scansiona codice a barre..." autofocus>
    </div>

<div id="riepilogo-totali">
    <strong>Riepilogo:</strong>
    <ul class="mb-0">
        <li>Marchi presenti: <span id="tot-marchi">0</span></li>
        <li>Totale titoli a magazzino: <span id="tot-titoli">0</span></li>
        <li>Quantità complessiva: <span id="tot-quantita">0</span></li>
        <li>Valore lordo complessivo: <span id="tot-valore-lordo">0.00</span> €</li>
        @if ($magazzino->anagrafica->categoria === 'magazzino editore')
        <li>Totale prezzo di costo: <span id="tot-costo">0.00</span> €</li>
        @endif
    </ul>
</div>


<div class="table-responsive" style="overflow-x: visible;">
<table id="giacenzeTable" class="table table-bordered mt-3">

        <thead>
            <tr>
                <th class="sortable" data-column="0">Marchio ▲▼</th>
                <th>ISBN</th>
                <th class="sortable" data-column="2">Titolo ▲▼</th>
                <th>Q.tà</th>
                <th>Prezzo</th>
                <th>{{ $magazzino->anagrafica->categoria === 'magazzino editore' ? 'Costo' : 'Sconto' }}</th>
                <th>Data Agg.</th>
                <th>Note</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody id="giacenzeTableBody">
        @foreach ($giacenze as $giacenza)
@php
    $categoria = $magazzino->anagrafica->categoria;
    $originalData = [
        'isbn' => $giacenza->isbn,
        'titolo' => $giacenza->titolo,
        'quantita' => $giacenza->quantita,
        'prezzo' => $giacenza->prezzo,
        'note' => $giacenza->note,
        $categoria === 'magazzino editore' ? 'costo_produzione' : 'sconto' =>
            $categoria === 'magazzino editore' ? $giacenza->costo_produzione : $giacenza->sconto,
    ];
@endphp

<tr data-id="{{ $giacenza->id }}" data-original='@json($originalData)'>

                <td><input type="text" class="form-control marchio" value="{{ $giacenza->libro->marchio_editoriale->nome ?? 'N/D' }}" readonly></td>
                <td><input type="text" class="form-control isbn" value="{{ $giacenza->isbn }}" readonly></td>
                <td><input type="text" class="form-control titolo" value="{{ $giacenza->titolo }}"></td>
                <td><input type="number" class="form-control quantita" value="{{ $giacenza->quantita }}"></td>
                <td><input type="text" class="form-control prezzo" value="{{ $giacenza->prezzo }}" readonly></td>
                <td><input type="text" class="form-control costo_sconto"
                    value="{{ $magazzino->anagrafica->categoria === 'magazzino editore' ? $giacenza->costo_produzione : $giacenza->sconto }}"></td>
                <td class="data-aggiornamento">
                    {{ $giacenza->data_ultimo_aggiornamento ? \Carbon\Carbon::parse($giacenza->data_ultimo_aggiornamento)->format('Y-m-d') : '-' }}
                </td>
                @php
                    $soloCodice = preg_replace('/^Sottratto con ordine\s*/i', '', $giacenza->note ?? '');
                @endphp
                <td><input type="text" class="form-control note" value="{{ $soloCodice }}"></td>
                <td>
                    <button class="btn btn-primary btn-sm salvaSingola" title="Salva riga">
                        <i class="bi bi-save"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm deleteRow" title="Elimina riga">
                        <i class="bi bi-trash"></i>
                    </button>
                        <div class="alert alert-success alert-salvata mt-1 d-none" role="alert" style="font-size: 12px; padding: 4px;">
                            Salvato!
                        </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    </div>
</div>

<style>
/* Imposta larghezza specifica per ogni colonna */
#giacenzeTable th:nth-child(1), #giacenzeTable td:nth-child(1) { width: 100px; } /* Marchio */
#giacenzeTable th:nth-child(2), #giacenzeTable td:nth-child(2) { width: 145px; } /* ISBN */
#giacenzeTable th:nth-child(3), #giacenzeTable td:nth-child(3) { width: 240px; } /* Titolo */
#giacenzeTable th:nth-child(4), #giacenzeTable td:nth-child(4) { width: 70px; }  /* Q.tà */
#giacenzeTable th:nth-child(5), #giacenzeTable td:nth-child(5) { width: 70px; }  /* Prezzo */
#giacenzeTable th:nth-child(6), #giacenzeTable td:nth-child(6) { width: 70px; }  /* Costo/Sconto */
#giacenzeTable th:nth-child(7), #giacenzeTable td:nth-child(7) { width: 90px; } /* Data */
#giacenzeTable th:nth-child(8), #giacenzeTable td:nth-child(8) { width: 230px; } /* Note */
#giacenzeTable th:nth-child(9), #giacenzeTable td:nth-child(9) {
    width: 90px;
    min-width: 90px;
    white-space: nowrap;
} /* Azioni */

/* Rende la tabella scrollabile se eccede lo schermo */
.table-responsive {
    overflow-x: auto;
    width: 100%;
}

/* Tabella più flessibile e adattabile */
#giacenzeTable {
    width: max-content;
    min-width: 100%;
    table-layout: auto;
}

/* Contenitore principale */
.container {
    max-width: 100% !important;
    width: 95%;
    margin: 0 auto;
    padding: 0 10px;
    overflow-x: auto;
}

/* Riepilogo grafico in alto */
#riepilogo-totali {
    float: right;
    margin-left: 20px;
    margin-bottom: 10px;
    background-color: #d9f7ff;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
}
#riepilogo-totali ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
#riepilogo-totali li {
    margin-bottom: 2px;
}

/* Layout mobile ottimizzato */
@media (max-width: 768px) {
    #giacenzeTable th,
    #giacenzeTable td {
        font-size: 12px;
        padding: 4px;
    }

    #giacenzeTable input.form-control {
        font-size: 12px;
        padding: 2px 4px;
    }

    .btn-sm {
        padding: 2px 6px;
        font-size: 12px;
    }

    h2, h4 {
        font-size: 18px;
        text-align: center;
    }

    .container {
        padding: 10px;
    }

    form.d-flex {
        flex-direction: column;
    }

    form.d-flex input,
    form.d-flex button {
        width: 100%;
        margin-bottom: 8px;
    }

    #riepilogo-totali {
        float: none;
        width: 100%;
        margin: 10px 0;
    }
}
</style>




<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const table = $('#giacenzeTable').DataTable({
        paging: true,
        pageLength: 20,
        info: false,
        searching: false,
        ordering: false,
        lengthChange: false,
        drawCallback: function () {
            aggiornaTotaliGiacenze();
            document.querySelectorAll(".quantita").forEach(input => coloraQuantitaInput(input));
        }
    });

    aggiornaTotaliGiacenze();
    let libri = @json($libri);

    // 🔥 NUOVA FUNZIONE
    function coloraQuantitaInput(input) {
        let value = parseInt(input.value);
        if (categoria === "magazzino editore") {
            if (value <= 5) {
                input.style.backgroundColor = "red";
                input.style.color = "white";
            } else if (value <= 10) {
                input.style.backgroundColor = "yellow";
                input.style.color = "black";
            } else {
                input.style.backgroundColor = "green";
                input.style.color = "white";
            }
        } else {
            input.style.backgroundColor = "";
            input.style.color = "";
        }
    }

    // 🔥 Applica colore alle righe esistenti al caricamento
    document.querySelectorAll(".quantita").forEach(input => coloraQuantitaInput(input));

    document.getElementById("addRow").addEventListener("click", function() {
        let table = document.getElementById("giacenzeTableBody");
        let row = document.createElement("tr");
        row.setAttribute("data-id", "");
        row.innerHTML = `
            <td><input type="text" class="form-control marchio" readonly></td>
            <td><input type="text" class="form-control isbn" readonly></td>
            <td><input type="text" class="form-control titolo autocomplete-titolo" placeholder="cerca/scansiona titolo..."></td>
            <td><input type="number" class="form-control quantita"></td>
            <td><input type="text" class="form-control prezzo" readonly></td>
            <td><input type="text" class="form-control costo_sconto"></td>
            <td class="data-aggiornamento">-</td>
            <td><input type="text" class="form-control note"></td>
            <td>
                <button class="btn btn-primary btn-sm salvaSingola" title="Salva riga">
                    <i class="bi bi-save"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm deleteRow" title="Elimina riga">
                    <i class="bi bi-trash"></i>
                </button>

                    <div class="alert alert-success alert-salvata mt-1 d-none" role="alert" style="font-size: 12px; padding: 4px;">
                        Salvato!
                    </div>
            </td>
        `;
        table.insertBefore(row, table.firstChild);

        $(row).find(".autocomplete-titolo").autocomplete({
            minLength: 2,
            source: function(request, response) {
                const matches = libri.filter(libro =>
                    libro.titolo.toLowerCase().includes(request.term.toLowerCase())
                );
            response(matches.map(libro => ({
                label: `${libro.titolo} [${libro.isbn}]`,
                value: libro.titolo,
                id: libro.id
            })));

            },
            select: function(event, ui) {
                const libroSelezionato = libri.find(libro =>
                    libro.titolo === ui.item.value && libro.id === ui.item.id
                );
                const parentRow = $(this).closest("tr");

                if (libroSelezionato) {
                    parentRow.find(".isbn").val(libroSelezionato.isbn);
                    parentRow.find(".prezzo").val(libroSelezionato.prezzo);
                    parentRow.find(".marchio").val(libroSelezionato.marchio_editoriale ? libroSelezionato.marchio_editoriale.nome : "N/D");
                    if (categoria === "magazzino editore") {
                        parentRow.find(".costo_sconto").val(libroSelezionato.costo_produzione);
                    } else {
                        parentRow.find(".costo_sconto").val('');
                    }
                }
            }
        });
        aggiornaTotaliGiacenze();
    });

    document.addEventListener("input", function(event) {
        if (event.target.classList.contains("quantita")) {
            coloraQuantitaInput(event.target);
        }
    });

    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("deleteRow")) {
            let row = event.target.closest("tr");
            let giacenzaId = row.getAttribute("data-id");

            if (giacenzaId) {
                fetch(`/giacenze/${giacenzaId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        row.remove();
                        alert("Giacenza eliminata con successo.");
                        aggiornaTotaliGiacenze();
                    } else {
                        alert("Errore nell'eliminazione: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Errore:", error);
                    alert("Errore nell'eliminazione!");
                });
            } else {
                row.remove();
                aggiornaTotaliGiacenze();
            }
        }
    });

    document.getElementById("saveTable").addEventListener("click", function() {
    let rows = [];
    document.querySelectorAll("#giacenzeTableBody tr").forEach(row => {
        const giacenzaId = row.getAttribute("data-id") || null;
        const isNew = !giacenzaId;

        const current = {
            id: giacenzaId,
            isbn: row.querySelector(".isbn")?.value || '',
            titolo: row.querySelector(".titolo")?.value || '',
            quantita: parseInt(row.querySelector(".quantita")?.value) || 0,
            prezzo: parseFloat(row.querySelector(".prezzo")?.value) || 0,
            note: row.querySelector(".note")?.value || null,
            ...(categoria === "magazzino editore"
                ? { costo_produzione: parseFloat(row.querySelector(".costo_sconto")?.value) || 0 }
                : { sconto: parseFloat(row.querySelector(".costo_sconto")?.value) || 0 }
            )
        };

        const original = row.dataset.original ? JSON.parse(row.dataset.original) : null;
        const isModified = isNew || !original || JSON.stringify(current) !== JSON.stringify(original);

        if (isModified) {
            rows.push(current);
        }
    });

    fetch("{{ route('giacenze.store', ['magazzino' => $magazzino->id]) }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        body: JSON.stringify({ giacenze: rows })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Risposta dal server:", data);
        if (data.success) {
            alert("Salvataggio riuscito!");
            data.saved_ids.forEach(match => {
                const row = document.querySelector(`#giacenzeTableBody tr[data-id="${match.id}"]`) ||
                    [...document.querySelectorAll("#giacenzeTableBody tr")].find(r => r.querySelector(".isbn")?.value === match.isbn);


                if (row) {
                    row.setAttribute("data-id", match.id);
                    row.querySelector(".data-aggiornamento").innerText = new Date().toISOString().split('T')[0];

                    // Salva lo stato attuale come originale
                    row.dataset.original = JSON.stringify({
                        isbn: row.querySelector(".isbn")?.value || '',
                        titolo: row.querySelector(".titolo")?.value || '',
                        quantita: parseInt(row.querySelector(".quantita")?.value) || 0,
                        prezzo: parseFloat(row.querySelector(".prezzo")?.value) || 0,
                        note: row.querySelector(".note")?.value || null,
                        ...(categoria === "magazzino editore"
                            ? { costo_produzione: parseFloat(row.querySelector(".costo_sconto")?.value) || 0 }
                            : { sconto: parseFloat(row.querySelector(".costo_sconto")?.value) || 0 }
                        )
                    });
                }
            });
        } else {
            alert("Errore nel salvataggio: " + data.message);
        }
    })
    .catch(error => {
        console.error("Errore:", error);
        alert("Errore nel salvataggio!");
    });
});

});

function aggiornaTotaliGiacenze() {
    const righe = Array.from($('#giacenzeTable').DataTable().rows().nodes());

    const marchiSet = new Set();
    const titoliSet = new Set();
    let sommaQuantita = 0;
    let sommaValoreLordo = 0;
    let sommaCosto = 0;

    righe.forEach(riga => {
        const marchio = riga.querySelector('.marchio')?.value?.trim();
        const titolo = riga.querySelector('.titolo')?.value?.trim();
        const quantita = parseInt(riga.querySelector('.quantita')?.value) || 0;
        const prezzo = parseFloat(riga.querySelector('.prezzo')?.value) || 0;
        const costo = parseFloat(riga.querySelector('.costo_sconto')?.value) || 0;

        if (marchio) marchiSet.add(marchio);
        if (titolo) titoliSet.add(titolo);

        sommaQuantita += quantita;
        sommaValoreLordo += quantita * prezzo;

        @if ($magazzino->anagrafica->categoria === 'magazzino editore')
            sommaCosto += quantita * costo;
        @endif
    });

    document.getElementById("tot-marchi").innerText = marchiSet.size;
    document.getElementById("tot-titoli").innerText = titoliSet.size;
    document.getElementById("tot-quantita").innerText = sommaQuantita;
    document.getElementById("tot-valore-lordo").innerText = sommaValoreLordo.toFixed(2);

    const costoSpan = document.getElementById("tot-costo");
    if (costoSpan) costoSpan.innerText = sommaCosto.toFixed(2);
}

</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let sortDirection = {}; // memorizza direzione per ogni colonna

    function sortTableByColumn(table, column, asc = true) {
        const dirModifier = asc ? 1 : -1;
        const tBody = table.tBodies[0];
        const rows = Array.from(tBody.querySelectorAll("tr"));

        const sortedRows = rows.sort((a, b) => {
            const aColText = a.cells[column].querySelector("input")?.value.toLowerCase() || "";
            const bColText = b.cells[column].querySelector("input")?.value.toLowerCase() || "";

            return aColText.localeCompare(bColText) * dirModifier;
        });

        // Rimuove righe attuali
        while (tBody.firstChild) {
            tBody.removeChild(tBody.firstChild);
        }

        // Aggiunge righe ordinate
        tBody.append(...sortedRows);
    }

    // Aggiungi evento click a colonne ordinabili
    document.querySelectorAll(".sortable").forEach(headerCell => {
        headerCell.addEventListener("click", () => {
            const tableElement = document.getElementById("giacenzeTable");
            const columnIndex = parseInt(headerCell.getAttribute("data-column"));
            const currentIsAscending = sortDirection[columnIndex] || false;

            sortTableByColumn(tableElement, columnIndex, !currentIsAscending);
            sortDirection[columnIndex] = !currentIsAscending;
        });
    });
});
</script>

<script>
document.getElementById('barcode-scan-giacenze').addEventListener('input', function(e) {
    const codice = e.target.value.trim();

    if (codice.length < 10) return;

    fetch('/api/libro-da-barcode?isbn=' + codice)
        .then(res => res.json())
        .then(libro => {
            if (!libro) {
                alert('Libro non trovato.');
                return;
            }

            // Cerca riga già esistente
            const righe = document.querySelectorAll('#giacenzeTableBody tr');
            let rigaEsistente = null;

            righe.forEach(riga => {
                const isbnCell = riga.querySelector('.isbn');
                if (isbnCell && isbnCell.value === libro.isbn) {
                    rigaEsistente = riga;
                }
            });

            if (rigaEsistente) {
                rigaEsistente.scrollIntoView({ behavior: 'smooth', block: 'center' });
                rigaEsistente.querySelector('.quantita')?.focus();
                rigaEsistente.style.backgroundColor = '#ffffcc'; // giallo chiaro
                setTimeout(() => rigaEsistente.style.backgroundColor = '', 2000);
                return;
            }

            // Altrimenti aggiunge nuova riga
            const table = document.getElementById("giacenzeTableBody");
            const categoria = "{{ $magazzino->anagrafica->categoria }}";
            const newRow = document.createElement("tr");
            newRow.setAttribute("data-id", "");
            newRow.innerHTML = ` 
                <td><input type="text" class="form-control marchio" value="${libro.marchio_editoriale?.nome || 'N/D'}" readonly></td>
                <td><input type="text" class="form-control isbn" value="${libro.isbn}" readonly></td>
                <td><input type="text" class="form-control titolo autocomplete-titolo" value="${libro.titolo}" placeholder="cerca/scansiona titolo..."></td>
                <td><input type="number" class="form-control quantita" value="1"></td>
                <td><input type="text" class="form-control prezzo" value="${libro.prezzo}" readonly></td>
                <td><input type="text" class="form-control costo_sconto" value="${categoria === 'magazzino editore' ? libro.costo_produzione : ''}"></td>
                <td class="data-aggiornamento">-</td>
                <td><input type="text" class="form-control note"></td>
                <td>
                    <button class="btn btn-primary btn-sm salvaSingola" title="Salva riga">
                        <i class="bi bi-save"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm deleteRow" title="Elimina riga">
                        <i class="bi bi-trash"></i>
                    </button>

                        <div class="alert alert-success alert-salvata mt-1 d-none" role="alert" style="font-size: 12px; padding: 4px;">
                            Salvato!
                        </div>
                </td>

            `;

            table.insertBefore(newRow, table.firstChild);
            newRow.querySelector('.quantita').focus();
            $(newRow).find(".autocomplete-titolo").autocomplete({
    minLength: 2,
    source: function(request, response) {
        const matches = libri.filter(libro =>
            libro.titolo.toLowerCase().includes(request.term.toLowerCase())
        );
        response(matches.map(libro => ({
            label: `${libro.titolo} [${libro.isbn}]`,
            value: libro.titolo,
            id: libro.id
        })));
    },
    select: function(event, ui) {
        const libroSelezionato = libri.find(libro =>
            libro.titolo === ui.item.value && libro.id === ui.item.id
        );
        const parentRow = $(this).closest("tr");

        if (libroSelezionato) {
            parentRow.find(".isbn").val(libroSelezionato.isbn);
            parentRow.find(".prezzo").val(libroSelezionato.prezzo);
            parentRow.find(".marchio").val(libroSelezionato.marchio_editoriale ? libroSelezionato.marchio_editoriale.nome : "N/D");
            if (categoria === "magazzino editore") {
                parentRow.find(".costo_sconto").val(libroSelezionato.costo_produzione);
            } else {
                parentRow.find(".costo_sconto").val('');
            }
        }
    }
});

        });

    e.target.value = '';
});
</script>

<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.salvaSingola');
        if (btn) {
            const row = btn.closest('tr');
            const id = row.dataset.id || null;

            const isbn = row.querySelector(".isbn")?.value.trim();
            const titolo = row.querySelector(".titolo")?.value.trim();

            if (!isbn || !titolo) {
                alert("Compila almeno il titolo e seleziona un libro valido.");
                return;
            }

            const payload = {
                isbn: isbn,
                titolo: titolo,
                quantita: parseInt(row.querySelector(".quantita")?.value) || 0,
                prezzo: parseFloat(row.querySelector(".prezzo")?.value) || 0,
                note: row.querySelector(".note")?.value || '',
                ...(categoria === "magazzino editore"
                    ? { costo_produzione: parseFloat(row.querySelector(".costo_sconto")?.value) || 0 }
                    : { sconto: parseFloat(row.querySelector(".costo_sconto")?.value) || 0 }
                )
            };


        const url = id
            ? `/giacenze/singola/${id}/{{ $magazzino->id }}`
            : `/giacenze/singola//{{ $magazzino->id }}`;

        const method = id ? "PUT" : "POST";

        fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ giacenza: payload })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false; // 🔓 riabilita il bottone

            if (data.success) {
                row.querySelector('.data-aggiornamento').innerText = new Date().toISOString().split('T')[0];
                if (data.id) row.dataset.id = data.id;
                row.dataset.original = JSON.stringify(payload);

                const alertBox = row.querySelector('.alert-salvata');
                if (alertBox) {
                    alertBox.classList.remove('d-none');
                    setTimeout(() => alertBox.classList.add('d-none'), 2000);
                }
            } else {
                alert('Errore nel salvataggio: ' + (data.message || ''));
            }
        })
        .catch(async err => {
            btn.disabled = false;
            const responseText = await err?.response?.text?.();
            console.error("Errore nella richiesta:", err, responseText);
            alert('Errore nella richiesta: ' + (responseText || err.message));
        });

    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.deleteRow').forEach(button => {
        button.addEventListener('click', function () {
            if (!confirm('Vuoi davvero eliminare questa riga?')) return;

            const row = this.closest('tr');
            const id = row.dataset.id;

            if (!id) {
                row.remove(); // Rimuove righe non ancora salvate
                return;
            }

            fetch(`/giacenze/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                } else {
                    alert("Errore: " + (data.message || "Impossibile eliminare la giacenza."));
                }
            })
            .catch(() => {
                alert("Errore nella richiesta al server.");
            });
        });
    });
});
</script>



@endsection