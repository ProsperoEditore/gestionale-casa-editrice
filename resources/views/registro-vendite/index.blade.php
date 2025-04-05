@extends('layouts.app')

@section('content')
<div class="container">
  <h3>Registro Vendite</h3>
  <a class="btn btn-success mb-2" href="{{ route('registro-vendite.create') }}">Aggiungi Nuovo</a>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Canale</th>
        <th>Azioni</th>
        <th>Registro</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $item)
        <tr>
          <td>{{ $item->anagrafica->nome }}</td>
          <td>{{ $item->canale_vendita }}</td>
          <td>
              <form action="{{ route('registro-vendite.destroy', $item->id) }}" method="POST" style="display:inline">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm">Elimina</button>
            </form>
          </td>
          <td><a class="btn btn-info btn-sm" href="{{ route('registro-vendite.gestione', $item->id) }}">Registro</a></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
