<div class="card mb-4">
    <div class="card-header">Rappresentante legale</div>
    <div class="card-body row g-3">
        <div class="col-md-4">
            <label class="form-label">Nazione</label>
            <input type="text" name="rapp_nazione" class="form-control" value="{{ old('rapp_nazione', $profilo->rapp_nazione ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Partita IVA</label>
            <input type="text" name="rapp_partita_iva" class="form-control" value="{{ old('rapp_partita_iva', $profilo->rapp_partita_iva ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Codice fiscale</label>
            <input type="text" name="rapp_codice_fiscale" class="form-control" value="{{ old('rapp_codice_fiscale', $profilo->rapp_codice_fiscale ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Denominazione (o Nome e Cognome)</label>
            <input type="text" name="rapp_denominazione" class="form-control" value="{{ old('rapp_denominazione', $profilo->rapp_denominazione ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Codice EORI</label>
            <select name="rapp_codice_eori" class="form-select">
                <option value="no" {{ (old('rapp_codice_eori', $profilo->rapp_codice_eori ?? 'no') == 'no') ? 'selected' : '' }}>No</option>
                <option value="sì" {{ (old('rapp_codice_eori', $profilo->rapp_codice_eori ?? '') == 'sì') ? 'selected' : '' }}>Sì</option>
            </select>
        </div>
    </div>
</div>