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
    <label class="form-label">Royalties Vendite Indirette (%) <span class="text-danger">*</span></label>
    <input type="number" name="royalties_vendite_indirette" value="{{ old('royalties_vendite_indirette', $contratto->royalties_vendite_indirette ?? '') }}" class="form-control" required>
</div>

@foreach([1, 2, 3] as $n)
    <div class="form-group">
        <label for="royalties_vendite_indirette_soglia_{{ $n }}">Soglia {{ $n }} (Quantità):</label>
        <input type="number" name="royalties_vendite_indirette_soglia_{{ $n }}" value="{{ old("royalties_vendite_indirette_soglia_$n", $contratto->{'royalties_vendite_indirette_soglia_'.$n} ?? '') }}" class="form-control">
    </div>
    <div class="form-group">
        <label for="royalties_vendite_indirette_percentuale_{{ $n }}">Percentuale Soglia {{ $n }} (%):</label>
        <input type="number" step="0.01" name="royalties_vendite_indirette_percentuale_{{ $n }}" value="{{ old("royalties_vendite_indirette_percentuale_$n", $contratto->{'royalties_vendite_indirette_percentuale_'.$n} ?? '') }}" class="form-control">
    </div>
@endforeach

<div class="mb-3">
    <label class="form-label">Royalties Vendite Dirette (%) <span class="text-danger">*</span></label>
    <input type="number" name="royalties_vendite_dirette" value="{{ old('royalties_vendite_dirette', $contratto->royalties_vendite_dirette ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Royalties Eventi (%) <span class="text-danger">*</span></label>
    <input type="number" name="royalties_eventi" value="{{ old('royalties_eventi', $contratto->royalties_eventi ?? '') }}" class="form-control" required>
</div>
