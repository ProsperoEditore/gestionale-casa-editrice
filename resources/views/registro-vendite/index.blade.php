@extends('layouts.app')

@section('content')
<div class="container mt-5">
  <h3 class="text-center mb-4">Registro Vendite</h3>

  <a class="btn btn-success mb-2" href="{{ route('registro-vendite.create') }}">Aggiungi Nuovo</a>

  <form action="{{ route('registro-vendite.index') }}" method="GET" class="d-flex mb-3" style="max-width: 300px;">
    <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cerca per anagrafica...">
    <button class="btn btn-outline-primary">Cerca</button>
  </form>

  <!-- DESKTOP -->
  <div class="d-none d-md-block table-responsive">
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
            <td>
              <form action="{{ route('registro-vendite.updateCanale', $item->id) }}" method="POST" class="d-flex align-items-center">
                @csrf
                @method('PATCH')
                <select name="canale_vendita" class="form-select form-select-sm me-1">
                  <option value="Vendite dirette" {{ $item->canale_vendita == 'Vendite dirette' ? 'selected' : '' }}>Vendite dirette</option>
                  <option value="Vendite indirette" {{ $item->canale_vendita == 'Vendite indirette' ? 'selected' : '' }}>Vendite indirette</option>
                  <option value="Evento" {{ $item->canale_vendita == 'Evento' ? 'selected' : '' }}>Evento</option>
                </select>
                <button type="submit" class="btn btn-sm btn-success" title="Salva"><i class="bi bi-check-lg"></i></button>
              </form>
            </td>

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

  <!-- MOBILE -->
  <div class="d-md-none">
    @foreach($items as $item)
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">{{ $item->anagrafica->nome }}</h5>
          <form action="{{ route('registro-vendite.updateCanale', $item->id) }}" method="POST" class="d-flex align-items-center mb-2">
            @csrf
            @method('PATCH')
            <select name="canale_vendita" class="form-select form-select-sm me-2">
              <option value="Vendite dirette" {{ $item->canale_vendita == 'Vendite dirette' ? 'selected' : '' }}>Vendite dirette</option>
              <option value="Vendite indirette" {{ $item->canale_vendita == 'Vendite indirette' ? 'selected' : '' }}>Vendite indirette</option>
              <option value="Evento" {{ $item->canale_vendita == 'Evento' ? 'selected' : '' }}>Evento</option>
            </select>
            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></button>
          </form>

          <div class="d-flex flex-wrap gap-3 mt-2">
            <form action="{{ route('registro-vendite.destroy', $item->id) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo registro?')">
                <i class="bi bi-trash"></i>
              </button>
            </form>
            <a class="btn btn-sm btn-info" href="{{ route('registro-vendite.gestione', $item->id) }}" title="Gestione Registro">
              <i class="bi bi-journal-text"></i>
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="d-flex justify-content-center mt-4">
    {{ $items->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
  </div>
</div>
@endsection
