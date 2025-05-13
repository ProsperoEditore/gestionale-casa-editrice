@extends('layouts.app')

@section('content')
<div class="container mt-5">
  <h3 class="text-center mb-4">Registro Vendite</h3>

  <a class="btn btn-success mb-2" href="{{ route('registro-vendite.create') }}">Aggiungi Nuovo</a>

  <form action="{{ route('registro-vendite.index') }}" method="GET" class="d-flex mb-3" style="max-width: 300px;">
    <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cerca per anagrafica...">
    <button class="btn btn-outline-primary">Cerca</button>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>Nome</th>
          <th>Canale</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
          <tr>
            <td>{{ $item->anagrafica->nome }}</td>
            <td>{{ $item->canale_vendita }}</td>
            <td>
              <div class="d-flex justify-content-center gap-2 text-nowrap">

                <form action="{{ route('registro-vendite.destroy', $item->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn p-0 border-0 bg-transparent text-danger" title="Elimina">
                    <i class="bi bi-trash fs-5"></i>
                  </button>
                </form>

                <a class="text-info" href="{{ route('registro-vendite.gestione', $item->id) }}" title="Gestione Registro">
                  <i class="bi bi-journal-text fs-5"></i>
                </a>

              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-center mt-4">
    {{ $items->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
  </div>
</div>
@endsection
