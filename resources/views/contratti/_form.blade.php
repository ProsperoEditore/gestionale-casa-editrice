<form 
    action="{{ $contratto->exists ? route('contratti.update', $contratto->id) : route('contratti.store') }}" 
    method="POST"
>

    @csrf
    @if(isset($contratto))
        @method('PUT')
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Nome Contratto <span class="text-danger">*</span></label>
            <input type="text" name="nome_contratto" class="form-control" 
                value="{{ old('nome_contratto', $contratto->nome_contratto ?? '') }}" required>
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
            <label>Sconto Proprio Libro (%)</label>
            <input type="number" step="0.1" name="sconto_proprio_libro" class="form-control"
                value="{{ old('sconto_proprio_libro', $contratto->sconto_proprio_libro ?? '') }}">
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
            <label>Sconto Altri Libri (%)</label>
            <input type="number" step="0.1" name="sconto_altri_libri" class="form-control"
                value="{{ old('sconto_altri_libri', $contratto->sconto_altri_libri ?? '') }}">
        </div>
    </div>

    <hr>
    <h6>Royalties Vendite Indirette</h6>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Royalties Indirette Fissa (se non si usano soglie) (%)</label>
            <input type="number" step="0.1" name="royalties_vendite_indirette" class="form-control"
                value="{{ old('royalties_vendite_indirette', $contratto->royalties_vendite_indirette ?? '') }}">
        </div>
    </div>

    <div class="border-start ps-3 mb-3">
        <div class="row mb-2">
            <div class="col-md-3">
                <label>Soglia 1 (fino a questa quantità):</label>
                <input type="number" name="royalties_vendite_indirette_soglia_1" class="form-control"
                    value="{{ old('royalties_vendite_indirette_soglia_1', $contratto->royalties_vendite_indirette_soglia_1 ?? '') }}">
            </div>
            <div class="col-md-3">
                <label>Percentuale fino a Soglia 1 (%):</label>
                <input type="number" step="0.1" name="royalties_vendite_indirette_percentuale_1" class="form-control"
                    value="{{ old('royalties_vendite_indirette_percentuale_1', $contratto->royalties_vendite_indirette_percentuale_1 ?? '') }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-3">
                <label>Soglia 2 (fino a questa quantità):</label>
                <input type="number" name="royalties_vendite_indirette_soglia_2" class="form-control"
                    value="{{ old('royalties_vendite_indirette_soglia_2', $contratto->royalties_vendite_indirette_soglia_2 ?? '') }}">
            </div>
            <div class="col-md-3">
                <label>Percentuale tra Soglia 1 e Soglia 2 (%):</label>
                <input type="number" step="0.1" name="royalties_vendite_indirette_percentuale_2" class="form-control"
                    value="{{ old('royalties_vendite_indirette_percentuale_2', $contratto->royalties_vendite_indirette_percentuale_2 ?? '') }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-3">
                <label>Percentuale oltre Soglia 2 (%):</label>
                <input type="number" step="0.1" name="royalties_vendite_indirette_percentuale_3" class="form-control"
                    value="{{ old('royalties_vendite_indirette_percentuale_3', $contratto->royalties_vendite_indirette_percentuale_3 ?? '') }}">
            </div>
        </div>
    </div>

    <hr>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Royalties Vendite Dirette (%) <span class="text-danger">*</span></label>
            <input type="number" step="0.1" name="royalties_vendite_dirette" class="form-control" required
                value="{{ old('royalties_vendite_dirette', $contratto->royalties_vendite_dirette ?? '') }}">
        </div>
        <div class="col-md-6">
            <label>Royalties Eventi (%) <span class="text-danger">*</span></label>
            <input type="number" step="0.1" name="royalties_eventi" class="form-control" required
                value="{{ old('royalties_eventi', $contratto->royalties_eventi ?? '') }}">
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary me-2">Salva</button>
        <a href="{{ route('contratti.index') }}" class="btn btn-secondary">Annulla</a>
    </div>
</form>
