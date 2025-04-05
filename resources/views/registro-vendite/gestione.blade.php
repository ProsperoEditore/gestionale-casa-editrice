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

        <button type="submit" class="btn btn-primary">Salva</button>
        <h5>Elenco Vendite</h5>

        <form id="registroVenditeForm" action="{{ route('registro-vendite.store', ['registro_vendita' => $registroVendita->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="registro_vendita_id" value="{{ $registroVendita->id }}">

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
                            <tr>
                                <td><input type="date" name="data[]" value="{{ $dettaglio->data }}" class="form-control"></td>
                                <td><input type="text" name="periodo[]" value="{{ $dettaglio->periodo }}" class="form-control"></td>
                                <td><input type="text" name="isbn[]" value="{{ $dettaglio->isbn }}" class="form-control isbn"></td>
                                <td>
                                    <select name="titolo[]" class="form-control titolo-select">
                                        <option value="{{ $dettaglio->isbn }}" selected>{{ $dettaglio->titolo }}</option>
                                    </select>
                                </td>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<script>
$(document).ready(function() {
    function aggiornaValoreLordo(row) {
        let quantita = parseFloat(row.find(".quantita").val()) || 0;
        let prezzo = parseFloat(row.find(".prezzo").val()) || 0;
        row.find(".valore-lordo").val((quantita * prezzo).toFixed(2));
    }

    function initSelect2() {
        $(".titolo-select").select2({
            placeholder: "Cerca un libro...",
            ajax: {
                url: "{{ route('libri.autocomplete') }}",
                dataType: "json",
                delay: 250,
                data: params => ({ term: params.term }),
                processResults: data => ({
                    results: data.map(item => ({ id: item.isbn, text: item.titolo, prezzo: item.prezzo }))
                })
            }
        }).on("select2:select", function (e) {
            let data = e.params.data;
            let row = $(this).closest("tr");
            row.find(".isbn").val(data.id);
            row.find(".prezzo").val(data.prezzo);
            aggiornaValoreLordo(row);
        });
    }

    initSelect2();

    $(document).on("input", ".quantita, .prezzo", function() {
        aggiornaValoreLordo($(this).closest("tr"));
    });

    $("#addRow").click(function() {
        let newRow = `<tr>
            <td><input type="date" name="data[]" value="{{ date('Y-m-d') }}" class="form-control"></td>
            <td><input type="text" name="periodo[]" class="form-control"></td>
            <td><input type="text" name="isbn[]" class="form-control isbn"></td>
            <td><select name="titolo[]" class="form-control titolo-select"></select></td>
            <td><input type="number" name="quantita[]" value="0" class="form-control quantita"></td>
            <td><input type="number" name="prezzo[]" value="0.00" class="form-control prezzo" step="0.01"></td>
            <td><input type="number" name="valore_lordo[]" value="0.00" class="form-control valore-lordo" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm delete-row">Elimina</button></td>
        </tr>`;
        $("#registroVenditeBody").append(newRow);
        initSelect2();
    });

    $(document).on("click", ".delete-row", function() {
        $(this).closest("tr").remove();
    });
});
</script>

@endsection
