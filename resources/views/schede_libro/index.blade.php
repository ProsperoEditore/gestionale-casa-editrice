@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Schede Libro</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('schede-libro.create') }}" class="btn btn-primary mb-3">+ Nuova scheda</a>

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Copertina</th>
                <th>Titolo</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($schede as $scheda)
                <tr>
                    <td style="width: 80px;">
                        @if ($scheda->copertina_path)
                            <img src="{{ asset('storage/' . $scheda->copertina_path) }}" alt="Copertina" class="img-thumbnail" style="height: 60px;">
                        @else
                            <span class="text-muted">N/D</span>
                        @endif
                    </td>
                    <td>{{ $scheda->libro->titolo ?? '-' }}</td>
                    <td style="width: 200px;">
                        <a href="{{ route('schede-libro.edit', $scheda->id) }}" class="btn btn-sm btn-warning">Modifica</a>
                        <form action="{{ route('schede-libro.destroy', $scheda->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa scheda?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Elimina</button>
                        </form>
                        <a href="{{ route('schede-libro.pdf', $scheda->id) }}" class="btn btn-sm btn-success">PDF</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
