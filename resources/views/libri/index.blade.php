@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Libri</h3>
        
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('libri.create') }}" class="btn btn-success">Aggiungi Nuovo</a>
            <form action="{{ route('libri.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cerca per titolo...">
        <button class="btn btn-outline-primary">Cerca</button>
    </form>
            
            <!-- Form per l'importazione da Excel -->
            <form action="{{ route('libri.import') }}" method="POST" enctype="multipart/form-data" class="d-flex">
                @csrf
                <input type="file" name="file" class="form-control me-2" required>
                <button type="submit" class="btn btn-primary">Importa Excel</button>
            </form>
        </div>

        <table class="table table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th>ISBN</th>
                    <th>Titolo</th>
                    <th>Marchio Editoriale</th>
                    <th>Prezzo</th>
                    <th>Anno Pubblicazione</th>
                    <th>Stato</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->isbn }}</td>
                        <td>{{ $item->titolo }}</td>
                        <td>{{ $item->marchio_editoriale->nome ?? 'N/D' }}</td>
                        <td>{{ number_format($item->prezzo, 2, ',', '.') }} €</td>
                        <td>{{ $item->anno_pubblicazione }}</td>
                        <td>{{ $item->stato }}</td>
                        <td>
                            <a href="{{ route('libri.edit', $item->id) }}" class="btn btn-warning btn-sm">Modifica</a>
                            <form action="{{ route('libri.destroy', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro di voler eliminare questo libro?')">Elimina</button>
                            </form>
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
