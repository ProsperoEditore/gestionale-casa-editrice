@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Modifica Contratto</h3>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('contratti.update', $contratto->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nome Contratto</label>
                    <input type="text" name="nome_contratto" value="{{ old('nome_contratto', $contratto->nome_contratto) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sconto Proprio Libro (%)</label>
                    <input type="number" name="sconto_proprio_libro" value="{{ old('sconto_proprio_libro', $contratto->sconto_proprio_libro) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Sconto Altri Libri (%)</label>
                    <input type="number" name="sconto_altri_libri" value="{{ old('sconto_altri_libri', $contratto->sconto_altri_libri) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Royalties Vendite Indirette (%)</label>
                    <input type="number" name="royalties_vendite_indirette" value="{{ old('royalties_vendite_indirette', $contratto->royalties_vendite_indirette) }}" class="form-control">
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
                    <input type="number" name="royalties_vendite_dirette" value="{{ old('royalties_vendite_dirette', $contratto->royalties_vendite_dirette) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Royalties Eventi (%)</label>
                    <input type="number" name="royalties_eventi" value="{{ old('royalties_eventi', $contratto->royalties_eventi) }}" class="form-control">
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Aggiorna</button>
                    <a href="{{ route('contratti.index') }}" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
