<div class="card mb-4">
    <div class="card-header">Contatti</div>
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Telefono</label>
            <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $profilo->telefono ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $profilo->email ?? '') }}">
        </div>
    </div>
</div>