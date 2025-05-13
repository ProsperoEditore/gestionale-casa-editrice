@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Marchi editoriali</h3>
        
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('marchi-editoriali.create') }}" class="btn btn-success">Aggiungi Nuovo</a>
        </div>

        <table class="table table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->nome }}</td>
                        <td>{{ $item->email }}</td>
                        <td class="align-middle">
                            <a href="{{ route('marchi-editoriali.edit', $item->id) }}" class="text-warning me-1" title="Modifica">
                                <i class="bi bi-pencil fs-5"></i>
                            </a>
                            <form action="{{ route('marchi-editoriali.destroy', $item->id) }}" method="POST" style="display:inline;">
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
@endsection