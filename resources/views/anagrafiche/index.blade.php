@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Anagrafiche</h3>
        
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-3">
            <a href="{{ route('anagrafiche.create') }}" class="btn btn-success">Aggiungi Nuovo</a>

            <form action="{{ route('anagrafiche.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cerca per nome...">

                <select name="categoria" class="form-select">
                    <option value="">Cerca per categoria...</option>
                    <option value="magazzino editore" {{ request('categoria') == 'magazzino editore' ? 'selected' : '' }}>Magazzino Editore</option>
                    <option value="sito" {{ request('categoria') == 'sito' ? 'selected' : '' }}>Sito</option>
                    <option value="libreria c.e." {{ request('categoria') == 'libreria c.e.' ? 'selected' : '' }}>Libreria C.E.</option>
                    <option value="libreria cliente" {{ request('categoria') == 'libreria cliente' ? 'selected' : '' }}>Libreria Cliente</option>
                    <option value="privato" {{ request('categoria') == 'privato' ? 'selected' : '' }}>Privato</option>
                    <option value="biblioteca" {{ request('categoria') == 'biblioteca' ? 'selected' : '' }}>Biblioteca</option>
                    <option value="associazione" {{ request('categoria') == 'associazione' ? 'selected' : '' }}>Associazione</option>
                    <option value="università" {{ request('categoria') == 'università' ? 'selected' : '' }}>Università</option>
                    <option value="grossista" {{ request('categoria') == 'grossista' ? 'selected' : '' }}>Grossista</option>
                    <option value="distributore" {{ request('categoria') == 'distributore' ? 'selected' : '' }}>Distributore</option>
                    <option value="fiere" {{ request('categoria') == 'fiere' ? 'selected' : '' }}>Fiere</option>
                    <option value="festival" {{ request('categoria') == 'festival' ? 'selected' : '' }}>Festival</option>
                    <option value="altro" {{ request('categoria') == 'altro' ? 'selected' : '' }}>Altro</option>
                </select>

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
            {{ $items->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
