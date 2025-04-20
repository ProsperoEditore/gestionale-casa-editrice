@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registro Tirature</h1>
    
    <a href="{{ route('registro-tirature.create') }}" class="btn btn-success">Aggiungi Nuovo</a>
    
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Periodo</th>
                <th>Anno</th>
                <th>Azioni</th>
                <th>Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registros as $registro)
            <tr>
                <td>{{ $registro->periodo }}</td>
                <td>{{ $registro->anno }}</td>
                <td>
                    <a href="{{ route('registro-tirature.edit', $registro->id) }}" class="btn btn-primary">Modifica</a>
                    <form action="{{ route('registro-tirature.destroy', $registro->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Elimina</button>
                    </form>
                </td>
                <td>
                    <a href="{{ route('registro-tirature.show', $registro->id) }}" class="btn btn-info">Registro</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
<div class="d-flex justify-content-center mt-4">
{{ $registros->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>


</div>
@endsection
