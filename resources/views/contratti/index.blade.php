@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Elenco Contratti</h3>

    <div class="text-end mb-3">
        <a href="{{ route('contratti.create') }}" class="btn btn-success">Aggiungi Contratto</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome Contratto</th>
                <th>Sconto Proprio Libro (%)</th>
                <th>Sconto Altri Libri (%)</th>
                <th>Royalties Indirette (%)</th>
                <th>Royalties Dirette (%)</th>
                <th>Royalties Eventi (%)</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contratti as $contratto)
                <tr>
                    <td data-label="Nome Contratto">{{ $contratto->nome_contratto }}</td>
                    <td data-label="Sconto Proprio Libro">{{ $contratto->sconto_proprio_libro }}%</td>
                    <td data-label="Sconto Altri Libri">{{ $contratto->sconto_altri_libri }}%</td>
                    <td data-label="Royalties Indirette">{{ $contratto->royalties_vendite_indirette }}%</td>
                    <td data-label="Royalties Dirette">{{ $contratto->royalties_vendite_dirette }}%</td>
                    <td data-label="Royalties Eventi">{{ $contratto->royalties_eventi }}%</td>
                    <td data-label="Azioni" class="align-middle">
                        <a href="{{ route('contratti.edit', $contratto->id) }}" class="text-warning me-1" title="Modifica">
                            <i class="bi bi-pencil fs-5"></i>
                        </a>
                        <form action="{{ route('contratti.destroy', $contratto->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn p-0 border-0 bg-transparent text-danger" title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo contratto?')">
                                <i class="bi bi-trash fs-5"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<style>
@media (max-width: 767.98px) {
    table.table thead {
        display: none;
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

    table.table tbody td:last-child {
        justify-content: center;
    }

    table.table tbody td[data-label="Azioni"]::before {
        display: none;
    }
}
</style>
@endsection
