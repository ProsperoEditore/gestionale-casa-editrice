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
                        <select name="anagrafica_id" id="anagrafica_id" class="form-control select2" required>
                            <option value="">-- Seleziona Anagrafica --</option>
                            @foreach ($anagrafiche as $anagrafica)
                                <option value="{{ $anagrafica->id }}">{{ $anagrafica->nome }}</option>
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
                        <label class="form-label">Canale</label>
                        <select name="canale" class="form-control" required>
                            <option value="vendite indirette">Vendite Indirette</option>
                            <option value="vendite dirette">Vendite Dirette</option>
                            <option value="eventi">Eventi</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo Ordine</label>
                        <select name="tipo_ordine" class="form-control" required>
                            <option value="acquisto">Acquisto</option>
                            <option value="conto deposito">Conto Deposito</option>
                            <option value="conto deposito">Omaggio</option>
                        </select>
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

<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inizializza Select2 con opzione di ricerca
        $('#anagrafica_id').select2({
            placeholder: 'Cerca Anagrafica...',
            allowClear: true,
            width: '100%'
        });
    });
</script>


@endsection
