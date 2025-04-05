@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Anagrafiche</h3>
        
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('anagrafiche.create') }}" class="btn btn-success">Aggiungi Nuovo</a>
            <form action="{{ route('anagrafiche.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cerca per nome...">
        <button class="btn btn-outline-primary">Cerca</button>
    </form>
        </div>

        <table class="table table-bordered text-center">
            <thead class="thead-dark">
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
                        <td>{{ $item->nome }}</td>
                        <td>{{ $item->indirizzo_spedizione }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->telefono }}</td>
                        <td>
                            <a href="{{ route('anagrafiche.edit', $item->id) }}" class="btn btn-warning btn-sm">Modifica</a>
                            <form action="{{ route('anagrafiche.destroy', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Elimina</button>
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