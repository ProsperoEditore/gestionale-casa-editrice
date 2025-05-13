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
                    <td>{{ $contratto->nome_contratto }}</td>
                    <td>{{ $contratto->sconto_proprio_libro }}%</td>
                    <td>{{ $contratto->sconto_altri_libri }}%</td>
                    <td>{{ $contratto->royalties_vendite_indirette }}%</td>
                    <td>{{ $contratto->royalties_vendite_dirette }}%</td>
                    <td>{{ $contratto->royalties_eventi }}%</td>
                    <td class="align-middle">
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
@endsection
