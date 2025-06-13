@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Crea Nuovo Contratto</h3>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('contratti.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nome Contratto <span class="text-danger">*</span></label>
                    <input type="text" name="nome_contratto" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sconto Proprio Libro (%)</label>
                    <input type="number" name="sconto_proprio_libro" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Sconto Altri Libri (%)</label>
                    <input type="number" name="sconto_altri_libri" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Royalties Vendite Indirette (%) <span class="text-danger">*</span></label>
                    <input type="number" name="royalties_vendite_indirette" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="royalties_vendite_indirette_soglia_1">Soglia 1 (Quantità):</label>
                    <input type="number" name="royalties_vendite_indirette_soglia_1" id="royalties_vendite_indirette_soglia_1" value="{{ old('royalties_vendite_indirette_soglia_1', $contratto->royalties_vendite_indirette_soglia_1) }}">
                </div>

                <div class="form-group">
                    <label for="royalties_vendite_indirette_percentuale_1">Percentuale Soglia 1 (%):</label>
                    <input type="number" step="0.01" name="royalties_vendite_indirette_percentuale_1" id="royalties_vendite_indirette_percentuale_1" value="{{ old('royalties_vendite_indirette_percentuale_1', $contratto->royalties_vendite_indirette_percentuale_1) }}">
                </div>

                <div class="form-group">
                    <label for="royalties_vendite_indirette_soglia_2">Soglia 2 (Quantità):</label>
                    <input type="number" name="royalties_vendite_indirette_soglia_2" id="royalties_vendite_indirette_soglia_2" value="{{ old('royalties_vendite_indirette_soglia_2', $contratto->royalties_vendite_indirette_soglia_2) }}">
                </div>

                <div class="form-group">
                    <label for="royalties_vendite_indirette_percentuale_2">Percentuale Soglia 2 (%):</label>
                    <input type="number" step="0.01" name="royalties_vendite_indirette_percentuale_2" id="royalties_vendite_indirette_percentuale_2" value="{{ old('royalties_vendite_indirette_percentuale_2', $contratto->royalties_vendite_indirette_percentuale_2) }}">
                </div>

                <div class="form-group">
                    <label for="royalties_vendite_indirette_soglia_3">Soglia 3 (Quantità):</label>
                    <input type="number" name="royalties_vendite_indirette_soglia_3" id="royalties_vendite_indirette_soglia_3" value="{{ old('royalties_vendite_indirette_soglia_3', $contratto->royalties_vendite_indirette_soglia_3) }}">
                </div>

                <div class="form-group">
                    <label for="royalties_vendite_indirette_percentuale_3">Percentuale Soglia 3 (%):</label>
                    <input type="number" step="0.01" name="royalties_vendite_indirette_percentuale_3" id="royalties_vendite_indirette_percentuale_3" value="{{ old('royalties_vendite_indirette_percentuale_3', $contratto->royalties_vendite_indirette_percentuale_3) }}">
                </div>



                <div class="mb-3">
                    <label class="form-label">Royalties Vendite Dirette (%)</label>
                    <input type="number" name="royalties_vendite_dirette" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Royalties Eventi (%)</label>
                    <input type="number" name="royalties_eventi" class="form-control">
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Salva</button>
                    <a href="{{ route('contratti.index') }}" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
