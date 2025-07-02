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
                        <input type="date" name="data" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Anagrafica</label>
                        <select class="form-control select2" name="anagrafica_id" required>
                        <option></option>
                        @foreach ($anagrafiche as $anagrafica)
                            <option value="{{ $anagrafica->id }}">{{ $anagrafica->nome_completo }}</option>
                        @endforeach
                    </select>
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
                        <select name="canale" id="canale" class="form-control">
                            <option value="vendite indirette">Vendite Indirette</option>
                            <option value="vendite dirette">Vendite Dirette</option>
                            <option value="evento">Evento</option>
                        </select>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoOrdine = document.getElementById('tipo_ordine');
    const canaleContainer = document.getElementById('canale_container');

    function toggleCanale() {
        if (tipoOrdine.value === 'acquisto') {
            canaleContainer.style.display = 'block';
        } else {
            canaleContainer.style.display = 'none';
        }
    }

    tipoOrdine.addEventListener('change', toggleCanale);
    toggleCanale(); // eseguito all'avvio
});
</script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Cerca un'anagrafica",
        allowClear: true
    });
});
</script>



@endpush