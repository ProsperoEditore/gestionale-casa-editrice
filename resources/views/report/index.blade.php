@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Report</h3>
        
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('report.create') }}" class="btn btn-success">Aggiungi Nuovo</a>
            <form action="{{ route('report.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cerca per libro...">
        <button class="btn btn-outline-primary">Cerca</button>
    </form>
        </div>

        <table class="table table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th>Data Creazione</th>
                    <th>Titolo</th>
                    <th>Contratto</th>
                    <th>Azioni</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->data_creazione }}</td>
                        <td>{{ $item->libro->titolo }}</td>
                        <td>{{ $item->contratto->nome_contratto ?? '-' }}</td>
                        <td>
                            <form action="{{ route('report.destroy', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Elimina</button>
                            </form>
                        </td>
                        <td>
                        <a href="{{ route('report.dettagli.index', $item->id) }}" class="btn btn-info btn-sm">Report</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
    {{ $items->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
    </div>
@endsection