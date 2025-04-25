@extends('layouts.app')

@section('content')
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
                        <th style="width:12%;">Data</th>
                        <th style="width:10%;">Periodo</th>
                        <th style="width:10%;">ISBN</th>
                        <th style="width:30%;">Titolo</th>
                        <th style="width:8%;">Quantità</th>
                        <th style="width:10%;">Prezzo</th>
                        <th style="width:10%;">Valore Lordo</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody id="registroVenditeBody">
                    @if($dettagli->count() > 0)
                        @foreach($dettagli as $dettaglio)
                            <tr data-id="{{ $dettaglio->id }}">
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
                    @else
                        <tr>
                            <td colspan="8" class="text-center">Nessuna vendita presente.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- 🔽 Paginazione -->
            <div class="d-flex justify-content-center">
                {{ $dettagli->links() }}
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">

<script>
$(document).ready(function() {
    let libri = @json($libri);

    function aggiornaValoreLordo(row) {
        let quantita = parseFloat(row.find(".quantita").val()) || 0;
        let prezzo = parseFloat(row.find(".prezzo").val()) || 0;
        row.find(".valore-lordo").val((quantita * prezzo).toFixed(2));
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
                aggiornaValoreLordo(parentRow);
            }
        });
    }

    // inizializza autocomplete per righe esistenti
    $(".titolo").each(function(){
        initAutocomplete(this);
    });

    $("#addRow").click(function() {
        let newRow = `<tr>
            <td><input type="date" name="data[]" value="{{ date('Y-m-d') }}" class="form-control"></td>
            <td><input type="text" name="periodo[]" class="form-control"></td>
            <td><input type="text" name="isbn[]" class="form-control isbn" readonly></td>
            <td><input type="text" name="titolo[]" class="form-control titolo" placeholder="Cerca titolo..."></td>
            <td><input type="number" name="quantita[]" value="0" class="form-control quantita"></td>
            <td><input type="number" name="prezzo[]" value="0.00" class="form-control prezzo" step="0.01"></td>
            <td><input type="number" name="valore_lordo[]" value="0.00" class="form-control valore-lordo" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm delete-row">Elimina</button></td>
        </tr>`;
        $("#registroVenditeBody").prepend(newRow);

        let addedRow = $("#registroVenditeBody tr").first();
        initAutocomplete(addedRow.find(".titolo"));
    });

    $(document).on("input", ".quantita, .prezzo", function() {
        aggiornaValoreLordo($(this).closest("tr"));
    });

    $(document).on("click", ".delete-row", function() {
    let row = $(this).closest("tr");
    let dettaglioId = row.data("id");

    if (dettaglioId) {
        if(confirm("Vuoi davvero eliminare questa riga?")) {
            $.ajax({
                url: `/registro-vendite/dettaglio/${dettaglioId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    if(result.success) {
                        row.remove();
                    } else {
                        alert('Errore nell\'eliminazione della riga.');
                    }
                },
                error: function() {
                    alert('Errore nella richiesta.');
                }
            });
        }
    } else {
        // Se la riga non è ancora salvata, rimuovila semplicemente
        row.remove();
    }
});

});
</script>


@endsection
