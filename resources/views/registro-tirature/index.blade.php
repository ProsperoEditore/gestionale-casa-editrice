@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Registro Tirature</h1>

    <a href="{{ route('registro-tirature.create') }}" class="btn btn-success mb-3">Aggiungi Nuovo</a>

    <!-- DESKTOP -->
    <div class="d-none d-md-block table-responsive">
        <table class="table table-bordered text-center align-middle text-nowrap">
            <thead class="table-dark">
                <tr>
                    <th>Periodo</th>
                    <th>Anno</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $registro)
                    <tr>
                        <td>{{ $registro->periodo }}</td>
                        <td>{{ $registro->anno }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('registro-tirature.edit', $registro->id) }}" class="text-warning" title="Modifica">
                                    <i class="bi bi-pencil fs-5"></i>
                                </a>
                                <form action="{{ route('registro-tirature.destroy', $registro->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn p-0 border-0 bg-transparent text-danger" title="Elimina">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                                <a href="{{ route('registro-tirature.show', $registro->id) }}" class="text-info" title="Visualizza Registro">
                                    <i class="bi bi-journal-text fs-5"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- MOBILE -->
    <div class="d-md-none">
        @foreach($registros as $registro)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $registro->periodo }} - {{ $registro->anno }}</h5>
                    <div class="d-flex flex-wrap gap-3 mt-2">
                        <a href="{{ route('registro-tirature.edit', $registro->id) }}" class="btn btn-sm btn-warning" title="Modifica">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('registro-tirature.destroy', $registro->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Elimina">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        <a href="{{ route('registro-tirature.show', $registro->id) }}" class="btn btn-sm btn-info" title="Registro">
                            <i class="bi bi-journal-text"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $registros->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
