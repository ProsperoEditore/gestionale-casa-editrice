<div class="card mb-4">
    <div class="card-header">Fatturazione<span class="text-danger">*</span></div>
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
            <select name="regime_fiscale" class="form-select" required>
                <option value="">Seleziona</option>
                <option value="RF01" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF01' ? 'selected' : '' }}>Ordinario</option>
                <option value="RF02" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF02' ? 'selected' : '' }}>Contribuenti minimi (art.1, c.96-117, L. 244/07)</option>
                <option value="RF04" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF04' ? 'selected' : '' }}>Agricoltura e attività connesse e pesca (artt.34 e 34-bis, DPR 633/72)</option>
                <option value="RF05" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF05' ? 'selected' : '' }}>Vendita sali e tabacchi (art.74, c.1, DPR 633/72)</option>
                <option value="RF06" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF06' ? 'selected' : '' }}>Commercio fiammiferi (art.74, c.1, DPR 633/72)</option>
                <option value="RF07" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF07' ? 'selected' : '' }}>Editoria (art.74, c.1, DPR 633/72)</option>
                <option value="RF08" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF08' ? 'selected' : '' }}>Gestione servizi telefonia pubblica (art.74, c.1, DPR 633/72)</option>
                <option value="RF09" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF09' ? 'selected' : '' }}>Rivendita documenti di trasporto pubblico e di sosta</option>
                <option value="RF10" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF10' ? 'selected' : '' }}>Intrattenimenti, giochi e altre attività di cui alla tariffa allegata al DPR 640/72</option>
                <option value="RF11" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF11' ? 'selected' : '' }}>Agenzie viaggi e turismo (art.74-ter, DPR 633/72)</option>
                <option value="RF12" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF12' ? 'selected' : '' }}>Agriturismo (art.5, c.2, L. 413/91)</option>
                <option value="RF13" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF13' ? 'selected' : '' }}>Vendite a domicilio (art.25-bis, c.6, DPR 600/73)</option>
                <option value="RF14" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF14' ? 'selected' : '' }}>Beni usati, oggetti d’arte, antiquariato o da collezione (art.36, DL 41/95)</option>
                <option value="RF15" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF15' ? 'selected' : '' }}>Agenzie di vendita all’asta di oggetti d’arte, antiquariato o da collezione</option>
                <option value="RF16" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF16' ? 'selected' : '' }}>IVA per cassa P.A. (art.6, c.5, DPR 633/72)</option>
                <option value="RF17" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF17' ? 'selected' : '' }}>IVA per cassa (art. 32-bis, DL 83/2012)</option>
                <option value="RF18" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF18' ? 'selected' : '' }}>Altro</option>
                <option value="RF19" {{ old('regime_fiscale', $profilo->regime_fiscale ?? '') == 'RF19' ? 'selected' : '' }}>Regime forfettario (art.1, c.54-89, L. 190/2014)</option>
            </select>

        </div>
        <div class="col-md-12">
            <label class="form-label">IBAN</label>
            <input type="text" name="iban" class="form-control" value="{{ old('iban', $profilo->iban ?? '') }}">
        </div>

    </div>
</div>
