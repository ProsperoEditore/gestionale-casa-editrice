@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Libri</h3>
        
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('libri.create') }}" class="btn btn-success">Aggiungi Nuovo</a>
            <form action="{{ route('libri.index') }}" method="GET" style="min-width: 300px;" class="d-flex align-items-center">
                <select name="search" id="titolo_search" class="form-control select2" onchange="this.form.submit()">
                    <option value="">Cerca per titolo...</option>
                    @foreach($tuttiTitoli as $libro)
                        <option value="{{ $libro->titolo }}" {{ request('search') == $libro->titolo ? 'selected' : '' }}>
                            {{ $libro->titolo }}
                        </option>
                    @endforeach
                </select>
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
                        <td class="align-middle">
                            <a href="{{ route('libri.edit', $item->id) }}" class="text-warning me-1" title="Modifica">
                                <i class="bi bi-pencil fs-5"></i>
                            </a>
                            <form action="{{ route('libri.destroy', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn p-0 border-0 bg-transparent text-danger" title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo libro?')">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
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


    <!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('#titolo_search').select2({
        placeholder: "Cerca per titolo...",
        allowClear: true
    });
});
</script>

@endsection
