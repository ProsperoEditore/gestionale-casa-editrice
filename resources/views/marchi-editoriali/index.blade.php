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
                        <td data-label="Nome">{{ $item->nome }}</td>
                        <td data-label="Email">{{ $item->email }}</td>
                        <td data-label="Azioni" class="align-middle">
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

        table.table tbody td[data-label="Azioni"]::before {
            display: none;
        }

        table.table tbody td:last-child {
            justify-content: center;
        }
    }
    </style>
@endsection
