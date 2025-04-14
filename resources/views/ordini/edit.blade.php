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
                        <label class="form-label">Canale</label>
                        <select name="canale" class="form-control" required>
                            <option value="vendite indirette" {{ $ordine->canale == 'vendite indirette' ? 'selected' : '' }}>Vendite Indirette</option>
                            <option value="vendite dirette" {{ $ordine->canale == 'vendite dirette' ? 'selected' : '' }}>Vendite Dirette</option>
                            <option value="eventi" {{ $ordine->canale == 'eventi' ? 'selected' : '' }}>Eventi</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo Ordine</label>
                        <select name="tipo_ordine" class="form-control" required>
                            <option value="acquisto" {{ $ordine->tipo_ordine == 'acquisto' ? 'selected' : '' }}>Acquisto</option>
                            <option value="conto deposito" {{ $ordine->tipo_ordine == 'conto deposito' ? 'selected' : '' }}>Conto Deposito</option>
                            <option value="omaggio" {{ old('tipo_ordine', $ordine->tipo_ordine) === 'omaggio' ? 'selected' : '' }}>Omaggio</option>
                        </select>
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


<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoOrdine = document.querySelector('select[name="tipo_ordine"]');
    const canale = document.querySelector('select[name="canale"]');

    function toggleCanale() {
        if (tipoOrdine.value === 'omaggio') {
            canale.setAttribute('disabled', 'disabled');
        } else {
            canale.removeAttribute('disabled');
        }
    }

    tipoOrdine.addEventListener('change', toggleCanale);
    toggleCanale(); // esegui al primo caricamento
});
</script>


@endsection