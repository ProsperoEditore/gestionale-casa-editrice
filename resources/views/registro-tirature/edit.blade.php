@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifica Registro Tirature</h1>

    <form action="{{ route('registro-tirature.update', $registroTirature->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="periodo" class="form-label">Periodo</label>
            <select name="periodo" id="periodo" class="form-control" required>
                <option value="1° trimestre" {{ $registroTirature->periodo == '1° trimestre' ? 'selected' : '' }}>1° trimestre</option>
                <option value="2° trimestre" {{ $registroTirature->periodo == '2° trimestre' ? 'selected' : '' }}>2° trimestre</option>
                <option value="3° trimestre" {{ $registroTirature->periodo == '3° trimestre' ? 'selected' : '' }}>3° trimestre</option>
                <option value="4° trimestre" {{ $registroTirature->periodo == '4° trimestre' ? 'selected' : '' }}>4° trimestre</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="anno" class="form-label">Anno</label>
            <input type="number" name="anno" id="anno" class="form-control" min="1900" max="{{ date('Y') + 1 }}" value="{{ $registroTirature->anno }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Salva Modifiche</button>
        <a href="{{ route('registro-tirature.index') }}" class="btn btn-secondary">Annulla</a>
    </form>
</div>
@endsection
