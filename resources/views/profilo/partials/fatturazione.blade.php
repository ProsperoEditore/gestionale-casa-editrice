<div class="card mb-4">
    <div class="card-header">Fatturazione</div>
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Codice destinatario</label>
            <input type="text" name="codice_destinatario" class="form-control" value="{{ old('codice_destinatario', $profilo->codice_destinatario ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">PEC</label>
            <input type="email" name="pec" class="form-control" value="{{ old('pec', $profilo->pec ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Nazione</label>
            <input type="text" name="nazione" class="form-control" value="{{ old('nazione', $profilo->nazione ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Partita IVA</label>
            <input type="text" name="partita_iva" class="form-control" value="{{ old('partita_iva', $profilo->partita_iva ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Codice fiscale</label>
            <input type="text" name="codice_fiscale" class="form-control" value="{{ old('codice_fiscale', $profilo->codice_fiscale ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Denominazione (o Nome e Cognome)</label>
            <input type="text" name="denominazione" class="form-control" value="{{ old('denominazione', $profilo->denominazione ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Codice EORI</label>
            <select name="codice_eori" class="form-select">
                <option value="no" {{ (old('codice_eori', $profilo->codice_eori ?? 'no') == 'no') ? 'selected' : '' }}>No</option>
                <option value="sì" {{ (old('codice_eori', $profilo->codice_eori ?? '') == 'sì') ? 'selected' : '' }}>Sì</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Regime fiscale</label>
            <input type="text" name="regime_fiscale" class="form-control" value="{{ old('regime_fiscale', $profilo->regime_fiscale ?? 'Editoria (art.74, c.1, DPR 633/72)') }}">
        </div>
        <div class="col-md-12">
            <label class="form-label">IBAN</label>
            <input type="text" name="iban" class="form-control" value="{{ old('iban', $profilo->iban ?? '') }}">
        </div>
    </div>
</div>
