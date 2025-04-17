@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="text-center mb-0">Modifica Ordine</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('ordini.update', $ordine->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Data</label>
                        <input type="date" name="data" class="form-control" value="{{ $ordine->data }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Anagrafica</label>
                        <select name="anagrafica_id" class="form-control" required>
                            @foreach($anagrafiche as $anagrafica)
                                <option value="{{ $anagrafica->id }}" {{ $ordine->anagrafica_id == $anagrafica->id ? 'selected' : '' }}>
                                    {{ $anagrafica->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Codice Ordine</label>
                        <input type="text" name="codice" class="form-control" value="{{ $ordine->codice }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo Ordine</label>
                        <select name="tipo_ordine" id="tipo_ordine" class="form-control" required>
                            <option value="acquisto" {{ $ordine->tipo_ordine === 'acquisto' ? 'selected' : '' }}>Acquisto</option>
                            <option value="conto deposito" {{ $ordine->tipo_ordine === 'conto deposito' ? 'selected' : '' }}>Conto Deposito</option>
                            <option value="omaggio" {{ $ordine->tipo_ordine === 'omaggio' ? 'selected' : '' }}>Omaggio</option>
                            <option value="acquisto autore" {{ $ordine->tipo_ordine === 'acquisto autore' ? 'selected' : '' }}>Acquisto Autore</option>
                        </select>
                    </div>
                </div>

                <div class="row" id="canale_container" style="display:none;">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Canale</label>
                        <select id="canale_select" class="form-control">
                            <option value="vendite indirette" {{ $ordine->canale === 'vendite indirette' ? 'selected' : '' }}>Vendite Indirette</option>
                            <option value="vendite dirette" {{ $ordine->canale === 'vendite dirette' ? 'selected' : '' }}>Vendite Dirette</option>
                            <option value="evento" {{ $ordine->canale === 'evento' ? 'selected' : '' }}>Evento</option>
                        </select>
                        <input type="hidden" name="canale" id="canale_hidden" value="{{ $ordine->canale }}">
                    </div>
                </div>

                <!-- Tasti SALVA e ANNULLA -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('ordini.index') }}" class="btn btn-secondary">Annulla</a>
                    <button type="submit" class="btn btn-primary">Aggiorna</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
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

    toggleCanale(); // inizializzazione
});
</script>
@endpush
@endsection
