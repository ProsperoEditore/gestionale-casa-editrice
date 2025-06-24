<div class="card mb-4">
    <div class="card-header">Sede Amministrativa</div>
    <div class="card-body row g-3">
        <div class="col-md-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="sede_unica" id="sede_unica" value="1" {{ old('sede_unica', $profilo->sede_unica ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="sede_unica">L'indirizzo della Sede operativa è uguale a quello della Sede amministrativa</label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Indirizzo</label>
            <input type="text" name="indirizzo_amministrativa" class="form-control" value="{{ old('indirizzo_amministrativa', $profilo->indirizzo_amministrativa ?? '') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">N. Civico</label>
            <input type="text" name="numero_civico_amministrativa" class="form-control" value="{{ old('numero_civico_amministrativa', $profilo->numero_civico_amministrativa ?? '') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">CAP</label>
            <input type="text" name="cap_amministrativa" class="form-control" value="{{ old('cap_amministrativa', $profilo->cap_amministrativa ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Comune</label>
            <input type="text" name="comune_amministrativa" class="form-control" value="{{ old('comune_amministrativa', $profilo->comune_amministrativa ?? '') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Provincia</label>
            <input type="text" name="provincia_amministrativa" class="form-control" value="{{ old('provincia_amministrativa', $profilo->provincia_amministrativa ?? '') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Nazione</label>
            <input type="text" name="nazione_amministrativa" class="form-control" value="{{ old('nazione_amministrativa', $profilo->nazione_amministrativa ?? '') }}">
        </div>
    </div>

        <div class="card-header mt-4">Sede Operativa</div>
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Indirizzo</label>
            <input type="text" data-operativa name="indirizzo_operativa" class="form-control" value="{{ old('indirizzo_operativa', $profilo->indirizzo_operativa ?? '') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">N. Civico</label>
            <input type="text" data-operativa name="numero_civico_operativa" class="form-control" value="{{ old('numero_civico_operativa', $profilo->numero_civico_operativa ?? '') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">CAP</label>
            <input type="text" data-operativa name="cap_operativa" class="form-control" value="{{ old('cap_operativa', $profilo->cap_operativa ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Comune</label>
            <input type="text" data-operativa name="comune_operativa" class="form-control" value="{{ old('comune_operativa', $profilo->comune_operativa ?? '') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Provincia</label>
            <input type="text" data-operativa name="provincia_operativa" class="form-control" value="{{ old('provincia_operativa', $profilo->provincia_operativa ?? '') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Nazione</label>
            <input type="text" data-operativa name="nazione_operativa" class="form-control" value="{{ old('nazione_operativa', $profilo->nazione_operativa ?? '') }}">
        </div>
    </div>
</div>