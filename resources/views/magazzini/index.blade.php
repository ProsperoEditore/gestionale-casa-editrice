@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Magazzini</h3>

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-3">
        <a href="{{ route('magazzini.create') }}" class="btn btn-success">Aggiungi Nuovo</a>

        <form action="{{ route('magazzini.index') }}" method="GET" class="d-flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cerca per nome...">
            <select name="categoria" class="form-select">
                <option value="">Cerca per categoria...</option>
                <option value="magazzino editore" {{ request('categoria') == 'magazzino editore' ? 'selected' : '' }}>Magazzino Editore</option>
                <option value="libreria cliente" {{ request('categoria') == 'libreria cliente' ? 'selected' : '' }}>Libreria Cliente</option>
            </select>
            <button class="btn btn-outline-primary">Cerca</button>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>Categoria</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Prossima Scadenza</th>
                        @if(auth()->user()->ruolo !== 'utente')
                            <th>Azioni</th>
                        @endif
                        <th>Giacenze</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($magazzini as $magazzino)
                        <tr>
                            <td>{{ $magazzino->anagrafica->categoria ?? 'N/A' }}</td>
                            <td>{{ $magazzino->anagrafica->nome ?? 'N/A' }}</td>
                            <td>{{ $magazzino->anagrafica->email ?? 'N/A' }}</td>
                            <td class="d-flex align-items-center justify-content-center gap-2">
                                @if(optional($magazzino->anagrafica)->categoria === 'magazzino editore')
                                    <span class="badge bg-secondary">N.D.</span>
                                @else
                                    <input type="date" class="form-control scadenza-input"
                                        data-id="{{ $magazzino->id }}"
                                        value="{{ $magazzino->prossima_scadenza ? \Carbon\Carbon::parse($magazzino->prossima_scadenza)->format('Y-m-d') : '' }}"
                                        onchange="updateScadenza({{ $magazzino->id }}, this)">
                                @endif
                            </td>

                            @if(auth()->user()->ruolo !== 'utente')
                                <td>
                                    <form action="{{ route('magazzini.destroy', $magazzino) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro di voler eliminare questo magazzino?')">Elimina</button>
                                    </form>
                                </td>
                            @endif

                            <td>
                                <a href="{{ route('giacenze.create', ['magazzino' => $magazzino->id]) }}" class="btn btn-secondary btn-sm">Vedi Giacenze</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">
        {{ $magazzini->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.scadenza-input').forEach(input => {
        colorizeDate(input);
        input.addEventListener('change', () => {
            updateScadenza(input.dataset.id, input);
        });
    });

    function colorizeDate(input) {
    const dateValue = input.value;
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    input.style.transition = 'background-color 0.5s ease, color 0.5s ease';

    input.style.backgroundColor = '';
    input.style.color = '';

    if (!dateValue) {
        input.style.backgroundColor = '#f8f9fa';
        input.style.color = '#000';
        return;
    }

    const scadenzaDate = new Date(dateValue);
    scadenzaDate.setHours(0, 0, 0, 0);

    const diffDays = Math.round((scadenzaDate - today) / (1000 * 60 * 60 * 24));

    if (diffDays >= 0 && diffDays <= 7) {
        input.style.backgroundColor = '#ffeb3b'; // Giallo
        input.style.color = '#000';
    } else if (diffDays < 0 && Math.abs(diffDays) <= 14) {
        input.style.backgroundColor = '#ffeb3b'; // Giallo anche se è passata da meno di 14gg
        input.style.color = '#000';
    } else if (diffDays < -14) {
        input.style.backgroundColor = '#dc3545'; // Rosso
        input.style.color = '#fff';
    } else {
        input.style.backgroundColor = '#28a745'; // Verde
        input.style.color = '#fff';
    }
}


    let timeout;

    function updateScadenza(id, input) {
        clearTimeout(timeout);
        const nuovaScadenza = input.value;

        timeout = setTimeout(() => {
            fetch(`/magazzini/${id}/update-scadenza`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ prossima_scadenza: nuovaScadenza })
            })
            .then(response => response.json())
            .then(data => {
                colorizeDate(input); // forza il ricolore sempre
                if (!data.success) {
                    alert("Errore nell'aggiornamento della data.");
                }
            })

            .catch(error => {
                console.error('Errore:', error);
                alert("Errore nella richiesta.");
            });
        }, 500);
    }
});
</script>
@endsection
