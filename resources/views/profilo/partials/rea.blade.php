<div class="card mb-4">
    <div class="card-header">Dati iscrizione REA</div>
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Numero REA</label>
            <input type="text" name="numero_rea" class="form-control" value="{{ old('numero_rea', $profilo->numero_rea ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Capitale sociale</label>
            <input type="text" name="capitale_sociale" class="form-control" value="{{ old('capitale_sociale', $profilo->capitale_sociale ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Provincia ufficio</label>
            <input type="text" name="provincia_ufficio_rea" class="form-control" value="{{ old('provincia_ufficio_rea', $profilo->provincia_ufficio_rea ?? '') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Tipologia soci SRL</label>
            <select name="tipologia_soci" class="form-select">
                <option value="unico" {{ (old('tipologia_soci', $profilo->tipologia_soci ?? '') == 'unico') ? 'selected' : '' }}>Unico</option>
                <option value="multipli" {{ (old('tipologia_soci', $profilo->tipologia_soci ?? '') == 'multipli') ? 'selected' : '' }}>Multipli</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Stato liquidazione</label>
            <select name="stato_liquidazione" class="form-select">
                <option value="Non in liquidazione" {{ (old('stato_liquidazione', $profilo->stato_liquidazione ?? '') == 'Non in liquidazione') ? 'selected' : '' }}>Non in liquidazione</option>
                <option value="In liquidazione" {{ (old('stato_liquidazione', $profilo->stato_liquidazione ?? '') == 'In liquidazione') ? 'selected' : '' }}>In liquidazione</option>
            </select>
        </div>
    </div>
</div>