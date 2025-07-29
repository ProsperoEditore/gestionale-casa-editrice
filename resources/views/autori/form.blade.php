<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" class="form-control" value="{{ old('nome', $autore->nome ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Cognome</label>
        <input type="text" name="cognome" class="form-control" value="{{ old('cognome', $autore->cognome ?? '') }}">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Pseudonimo</label>
        <input type="text" name="pseudonimo" class="form-control" value="{{ old('pseudonimo', $autore->pseudonimo ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Denominazione (per enti)</label>
        <input type="text" name="denominazione" class="form-control" value="{{ old('denominazione', $autore->denominazione ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Codice Fiscale</label>
    <input type="text" name="codice_fiscale" class="form-control" value="{{ old('codice_fiscale', $autore->codice_fiscale ?? '') }}">
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Data di nascita</label>
        <input type="date" name="data_nascita" class="form-control" value="{{ old('data_nascita', $autore->data_nascita ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Luogo di nascita</label>
        <input type="text" name="luogo_nascita" class="form-control" value="{{ old('luogo_nascita', $autore->luogo_nascita ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">IBAN</label>
    <input type="text" name="iban" class="form-control" value="{{ old('iban', $autore->iban ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Indirizzo</label>
    <input type="text" name="indirizzo" class="form-control" value="{{ old('indirizzo', $autore->indirizzo ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Biografia</label>
    <textarea name="biografia" class="form-control" rows="5">{{ old('biografia', $autore->biografia ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Libri associati</label>
    <select name="libri[]" class="form-control" multiple>
        @foreach($libri as $libro)
            <option value="{{ $libro->id }}" {{ isset($autore) && $autore->libri->contains($libro->id) ? 'selected' : '' }}>
                {{ $libro->titolo }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Foto (opzionale)</label>
    <input type="file" name="foto" class="form-control">
    @if(isset($autore) && $autore->foto)
        <div class="mt-2">
            <img src="{{ asset('storage/foto_autori/' . $autore->foto) }}" alt="Foto autore" height="100">
        </div>
    @endif
</div>

<button type="submit" class="btn btn-primary">Salva</button>
