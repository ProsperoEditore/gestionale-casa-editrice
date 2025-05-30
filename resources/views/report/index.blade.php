@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Report</h3>
    
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
        <a href="{{ route('report.create') }}" class="btn btn-success">Aggiungi Nuovo</a>

        <form action="{{ route('report.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cerca per libro...">
            <button class="btn btn-outline-primary">Cerca</button>
        </form>
    </div>

    <!-- DESKTOP -->
    <div class="d-none d-md-block table-responsive">
        <table class="table table-bordered text-center align-middle text-nowrap">
            <thead class="table-dark">
                <tr>
                    <th>Data Creazione</th>
                    <th>Titolo</th>
                    <th>Contratto</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->data_creazione }}</td>
                        <td>{{ $item->libro->titolo }}</td>
                        <td>{{ $item->contratto->nome_contratto ?? '-' }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-3">
                                <form action="{{ route('report.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn p-0 border-0 bg-transparent text-danger" title="Elimina">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                                <a href="{{ route('report.dettagli.index', $item->id) }}" class="text-info" title="Visualizza Report">
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
                    <h5 class="card-title">{{ $item->libro->titolo }}</h5>
                    <p class="mb-1"><strong>Data:</strong> {{ $item->data_creazione }}</p>
                    <p class="mb-1"><strong>Contratto:</strong> {{ $item->contratto->nome_contratto ?? '-' }}</p>
                    <div class="d-flex flex-wrap gap-3 mt-2">
                        <form action="{{ route('report.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Elimina">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        <a href="{{ route('report.dettagli.index', $item->id) }}" class="btn btn-sm btn-info" title="Visualizza Report">
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
