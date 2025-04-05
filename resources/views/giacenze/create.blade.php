@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Gestione Giacenze - {{ $magazzino->anagrafica->nome ?? 'Sconosciuto' }}</h2>

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
    <button id="addRow" class="btn btn-success">Aggiungi Riga</button>
    <button id="saveTable" class="btn btn-primary">Salva</button>
    <a href="{{ route('giacenze.export', ['magazzino' => $magazzino->id]) }}" class="btn btn-outline-success">Esporta Excel</a>
    <form action="{{ route('giacenze.create', $magazzino->id) }}" method="GET" class="d-flex" style="max-width: 300px;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cerca per titolo...">
        <button class="btn btn-outline-primary">Cerca</button>
    </form>

    <div class="table-responsive">
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
            <tr data-id="{{ $giacenza->id }}">
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
                <td><input type="text" class="form-control note" value="{{ $giacenza->note }}"></td>
                <td><button class="btn btn-danger btn-sm deleteRow">Elimina</button></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
    {{ $giacenze->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>

    </div>
</div>

<style>
    #giacenzeTable th:nth-child(1), #giacenzeTable td:nth-child(1) { width: 140px; }
    #giacenzeTable th:nth-child(2), #giacenzeTable td:nth-child(2) { width: 140px; }
    #giacenzeTable th:nth-child(3), #giacenzeTable td:nth-child(3) { width: 280px; }
    #giacenzeTable th:nth-child(4), #giacenzeTable td:nth-child(4) { width: 80px; }
    #giacenzeTable th:nth-child(5), #giacenzeTable td:nth-child(5) { width: 90px; }
    #giacenzeTable th:nth-child(6), #giacenzeTable td:nth-child(6) { width: 90px; }
    #giacenzeTable th:nth-child(7), #giacenzeTable td:nth-child(7) { width: 110px; }
    #giacenzeTable th:nth-child(8), #giacenzeTable td:nth-child(8) { width: 220px; }
    #giacenzeTable th:nth-child(9), #giacenzeTable td:nth-child(9) { width: 100px; }

    .table-responsive {
        overflow-x: auto;
        width: 100%;
    }

    #giacenzeTable {
        width: 100%;
        table-layout: fixed;
    }

    .container {
        overflow-x: auto;
    }
</style>



<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">


<script>
document.addEventListener("DOMContentLoaded", function() {
    let libri = @json($libri);
    const categoria = "{{ $magazzino->anagrafica->categoria }}";

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
        row.innerHTML = `
            <td><input type="text" class="form-control marchio" readonly></td>
            <td><input type="text" class="form-control isbn" readonly></td>
            <td><input type="text" class="form-control titolo autocomplete-titolo" placeholder="Cerca titolo..."></td>
            <td><input type="number" class="form-control quantita"></td>
            <td><input type="text" class="form-control prezzo" readonly></td>
            <td><input type="text" class="form-control costo_sconto"></td>
            <td class="data-aggiornamento">-</td>
            <td><input type="text" class="form-control note"></td>
            <td><button class="btn btn-danger btn-sm deleteRow">Elimina</button></td>
        `;
        table.insertBefore(row, table.firstChild);

        $(row).find(".autocomplete-titolo").autocomplete({
            source: libri.map(libro => libro.titolo),
            select: function(event, ui) {
                let libroTrovato = libri.find(libro => libro.titolo === ui.item.value);
                let parentRow = $(this).closest("tr");

                if (libroTrovato) {
                    parentRow.find(".isbn").val(libroTrovato.isbn);
                    parentRow.find(".prezzo").val(libroTrovato.prezzo);
                    parentRow.find(".marchio").val(libroTrovato.marchio_editoriale ? libroTrovato.marchio_editoriale.nome : "N/D");
                    if (categoria === "magazzino editore") {
                        parentRow.find(".costo_sconto").val(libroTrovato.costo_produzione);
                    }
                }
            }
        });
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


@endsection