@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="text-center mb-0">Crea Nuovo Ordine</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('ordini.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Data</label>
                        <input type="date" name="data" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Anagrafica</label>
                        <input type="text" id="anagrafica_autocomplete" class="form-control" placeholder="Digita un nome..." required>
                        <input type="hidden" name="anagrafica_id" id="anagrafica_id">
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Codice Ordine</label>
                        <input type="text" name="codice" class="form-control" required>
                    </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo Ordine</label>
                    <select name="tipo_ordine" id="tipo_ordine" class="form-control" required>
                        <option value="acquisto">Acquisto</option>
                        <option value="conto deposito">Conto Deposito</option>
                        <option value="omaggio">Omaggio</option>
                        <option value="acquisto autore">Acquisto Autore</option>
                    </select>
                </div>

                <div class="row" id="canale_container" style="display:none;">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Canale</label>
                        <select id="canale_select" class="form-control">
                            <option value="vendite indirette">Vendite Indirette</option>
                            <option value="vendite dirette">Vendite Dirette</option>
                            <option value="evento">Evento</option>
                        </select>
                        <input type="hidden" name="canale" id="canale_hidden">
                    </div>
                </div>
            </div>


                <!-- Tasti SALVA e ANNULLA -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('ordini.index') }}" class="btn btn-secondary">Annulla</a>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>


<script>
$(document).ready(function () {
    $("#anagrafica_autocomplete").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "{{ route('ordini.autocomplete-anagrafica') }}",
                data: {
                    query: request.term
                },
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            label: item.nome,
                            value: item.nome,
                            id: item.id
                        };
                    }));
                }
            });
        },
        select: function (event, ui) {
            $('#anagrafica_autocomplete').val(ui.item.label);
            $('#anagrafica_id').val(ui.item.id);
            return false;
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoOrdine = document.getElementById('tipo_ordine');
    const canaleContainer = document.getElementById('canale_container');
    const canaleSelect = document.getElementById('canale_select');
    const canaleHidden = document.getElementById('canale_hidden');

    function toggleCanale() {
        if (tipoOrdine.value === 'acquisto') {
            canaleContainer.style.display = 'block';
            canaleHidden.value = canaleSelect.value;
        } else {
            canaleContainer.style.display = 'none';
            canaleHidden.value = '';
        }
    }

    tipoOrdine.addEventListener('change', toggleCanale);
    canaleSelect.addEventListener('change', function () {
        canaleHidden.value = this.value;
    });

    toggleCanale(); // eseguito all'avvio
});
</script>

@endpush