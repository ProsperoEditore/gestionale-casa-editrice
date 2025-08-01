@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Libri</h3>
        
        <div class="mb-3">
            <a href="{{ route('libri.create') }}" class="btn btn-success">Aggiungi Nuovo</a>

            <form action="{{ route('libri.index') }}" method="GET" style="min-width: 300px;" class="d-flex align-items-center mt-2">
                <select name="search" id="titolo_search" class="form-control select2" onchange="this.form.submit()">
                    <option value="">Cerca per titolo...</option>
                    @foreach($tuttiTitoli as $libro)
                        <option value="{{ $libro->titolo }}" {{ request('search') == $libro->titolo ? 'selected' : '' }}>
                            {{ $libro->titolo }}
                        </option>
                    @endforeach
                </select>
            </form>

            <form action="{{ route('libri.import') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 mt-2">
                @csrf
                <input type="file" name="file" class="form-control" required>
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
                    <td data-label="ISBN">{{ $item->isbn }}</td>
                    <td data-label="Titolo">{{ $item->titolo }}</td>
                    <td data-label="Marchio Editoriale">{{ $item->marchio_editoriale->nome ?? 'N/D' }}</td>
                    <td data-label="Prezzo">{{ number_format($item->prezzo, 2, ',', '.') }} €</td>
                    <td data-label="Anno Pubblicazione">{{ $item->anno_pubblicazione }}</td>
                    <td data-label="Stato">
                        <form action="{{ route('libri.update', $item->id) }}" method="POST" class="d-flex flex-column align-items-center stato-form" style="min-width: 120px;">
                            @csrf
                            @method('PUT')

                            <select name="stato" class="form-select stato-select" data-libro-id="{{ $item->id }}">
                                <option value="C" {{ $item->stato == 'C' ? 'selected' : '' }}>In commercio</option>
                                <option value="A" {{ $item->stato == 'A' ? 'selected' : '' }}>Accantonato</option>
                                <option value="FC" {{ $item->stato == 'FC' ? 'selected' : '' }}>Fuori Catalogo</option>
                            </select>

                            <input type="date" name="data_cessazione_commercio"
                                class="form-control mt-2 data-cessazione"
                                style="display: {{ $item->stato == 'FC' ? 'block' : 'none' }};"
                                value="{{ $item->data_cessazione_commercio }}">
                            
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Salva</button>
                        </form>
                    </td>

                    <td data-label="Azioni" class="align-middle">
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

<style>
@media (max-width: 767.98px) {
    table.table thead {
        display: none;
    }

    table.table tbody tr:not(:last-child) {
    margin-bottom: 1.2rem;  
    }

    table.table tbody tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        padding: 0.8rem;
        border-radius: 0.5rem;
        background: #fff;
    }

    table.table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.4rem 0;
        border: none !important;
        width: 100%;
    }

    table.table tbody td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #333;
    }

    table.table tbody td[data-label="Azioni"]::before {
        display: none;
    }

    table.table tbody td:last-child {
        justify-content: center;
    }
}
</style>


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

<script>
$(document).ready(function () {
    $('.stato-select').on('change', function () {
        const selected = $(this).val();
        const form = $(this).closest('.stato-form');
        const dataInput = form.find('.data-cessazione');

        if (selected === 'FC') {
            dataInput.show();
        } else {
            dataInput.hide();
            dataInput.val('');
        }
    });
});
</script>



@endsection
