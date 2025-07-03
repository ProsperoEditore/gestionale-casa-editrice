@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Anagrafiche</h3>

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-3">
        <a href="{{ route('anagrafiche.create') }}" class="btn btn-success">Aggiungi Nuovo</a>

        <form action="{{ route('anagrafiche.index') }}" method="GET" class="d-flex flex-wrap gap-2">
            <select name="search" id="anagrafica_search" class="form-control" style="min-width:300px" onchange="this.form.submit()">
                <option value="">Cerca per anagrafica...</option>
                @foreach($tutteAnagrafiche as $anagrafica)
                    <option value="{{ $anagrafica->id }}" {{ request('search') == $anagrafica->id ? 'selected' : '' }}>
                        {{ $anagrafica->nome_completo }}
                    </option>
                @endforeach
            </select>

            <select name="categoria" class="form-select">
                <option value="">Cerca per categoria...</option>
                @foreach([
                    'magazzino editore','sito','libreria c.e.','libreria cliente','privato','biblioteca',
                    'associazione','università', 'scuola', 'grossista','distributore','fiere','festival','altro'
                ] as $categoria)
                    <option value="{{ $categoria }}" {{ request('categoria') == $categoria ? 'selected' : '' }}>{{ ucfirst($categoria) }}</option>
                @endforeach
            </select>

            <button class="btn btn-outline-primary">Cerca</button>
        </form>
    </div>

    <!-- DESKTOP -->
    <div class="d-none d-md-block table-responsive">
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Categoria</th>
                    <th>Nome</th>
                    <th>Indirizzo Spedizione</th>
                    <th>Email</th>
                    <th>Telefono</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->categoria }}</td>
                        <td>{{ $item->nome_completo }}</td>
                        <td>{{ $item->indirizzo_spedizione }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->telefono }}</td>
                        <td>
                            <a href="{{ route('anagrafiche.edit', $item->id) }}" class="text-warning me-2" title="Modifica">
                                <i class="bi bi-pencil fs-5"></i>
                            </a>
                            <form action="{{ route('anagrafiche.destroy', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn p-0 border-0 bg-transparent text-danger" title="Elimina">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- MOBILE -->
    <div class="d-md-none">
        @foreach($items as $item)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $item->nome }}</h5>
                    <p class="mb-1"><strong>Categoria:</strong> {{ $item->categoria }}</p>
                    <p class="mb-1"><strong>Indirizzo:</strong> {{ $item->indirizzo_spedizione }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $item->email }}</p>
                    <p class="mb-1"><strong>Telefono:</strong> {{ $item->telefono }}</p>

                    <div class="d-flex flex-wrap gap-3 mt-2">
                        <a href="{{ route('anagrafiche.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Modifica">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('anagrafiche.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Elimina" onclick="return confirm('Sei sicuro?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $items->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
