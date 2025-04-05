@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crea Registro Tirature</h1>

    <form action="{{ route('registro-tirature.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="periodo" class="form-label">Periodo</label>
            <select name="periodo" id="periodo" class="form-control" required>
                <option value="">-- Seleziona un periodo --</option>
                <option value="1° trimestre">1° trimestre</option>
                <option value="2° trimestre">2° trimestre</option>
                <option value="3° trimestre">3° trimestre</option>
                <option value="4° trimestre">4° trimestre</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="anno" class="form-label">Anno</label>
            <input type="number" name="anno" id="anno" class="form-control" min="1900" max="{{ date('Y') + 1 }}" required>
        </div>

        <button type="submit" class="btn btn-success">Salva</button>
    </form>
</div>
@endsection
