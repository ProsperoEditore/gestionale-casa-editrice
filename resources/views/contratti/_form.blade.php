@csrf

@if(isset($contratto))
    @method('PUT')
@endif

<div class="mb-3">
    <label class="form-label">Nome Contratto <span class="text-danger">*</span></label>
    <input type="text" name="nome_contratto" value="{{ old('nome_contratto', $contratto->nome_contratto ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Sconto Proprio Libro (%)</label>
    <input type="number" name="sconto_proprio_libro" value="{{ old('sconto_proprio_libro', $contratto->sconto_proprio_libro ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Sconto Altri Libri (%)</label>
    <input type="number" name="sconto_altri_libri" value="{{ old('sconto_altri_libri', $contratto->sconto_altri_libri ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Royalties Vendite Indirette - Valori con soglie</label>
</div>

<div class="ms-4 border-start ps-3 mb-4">

    <div class="form-group mb-2">
        <label for="royalties_vendite_indirette_soglia_1">Soglia 1 (fino a questa quantità):</label>
        <input type="number" name="royalties_vendite_indirette_soglia_1" class="form-control"
               value="{{ old('royalties_vendite_indirette_soglia_1', $contratto->royalties_vendite_indirette_soglia_1 ?? '') }}">
    </div>
    <div class="form-group mb-3">
        <label for="royalties_vendite_indirette_percentuale_1">Percentuale fino a Soglia 1 (%):</label>
        <input type="number" step="0.01" name="royalties_vendite_indirette_percentuale_1" class="form-control"
               value="{{ old('royalties_vendite_indirette_percentuale_1', $contratto->royalties_vendite_indirette_percentuale_1 ?? '') }}">
    </div>

    <div class="form-group mb-2">
        <label for="royalties_vendite_indirette_soglia_2">Soglia 2 (fino a questa quantità):</label>
        <input type="number" name="royalties_vendite_indirette_soglia_2" class="form-control"
               value="{{ old('royalties_vendite_indirette_soglia_2', $contratto->royalties_vendite_indirette_soglia_2 ?? '') }}">
    </div>
    <div class="form-group mb-3">
        <label for="royalties_vendite_indirette_percentuale_2">Percentuale tra Soglia 1 e Soglia 2 (%):</label>
        <input type="number" step="0.01" name="royalties_vendite_indirette_percentuale_2" class="form-control"
               value="{{ old('royalties_vendite_indirette_percentuale_2', $contratto->royalties_vendite_indirette_percentuale_2 ?? '') }}">
    </div>

    <div class="form-group mb-3">
        <label for="royalties_vendite_indirette_percentuale_3">Percentuale oltre Soglia 2 (%):</label>
        <input type="number" step="0.01" name="royalties_vendite_indirette_percentuale_3" class="form-control"
               value="{{ old('royalties_vendite_indirette_percentuale_3', $contratto->royalties_vendite_indirette_percentuale_3 ?? '') }}">
    </div>

</div>



<div class="mb-3">
    <label class="form-label">Royalties Vendite Dirette (%) <span class="text-danger">*</span></label>
    <input type="number" name="royalties_vendite_dirette" value="{{ old('royalties_vendite_dirette', $contratto->royalties_vendite_dirette ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Royalties Eventi (%) <span class="text-danger">*</span></label>
    <input type="number" name="royalties_eventi" value="{{ old('royalties_eventi', $contratto->royalties_eventi ?? '') }}" class="form-control" required>
</div>
