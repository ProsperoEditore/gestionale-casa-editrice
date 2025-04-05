@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Magazzini</h3>

    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('magazzini.create') }}" class="btn btn-success">Aggiungi Nuovo</a>
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
                        <th>Azioni</th>
                        <th>Giacenze</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($magazzini as $magazzino)
                        <tr>
                            <td>{{ $magazzino->anagrafica->categoria ?? 'N/A' }}</td>
                            <td>{{ $magazzino->anagrafica->nome ?? 'N/A' }}</td>
                            <td>{{ $magazzino->anagrafica->email ?? 'N/A' }}</td>
                            <td>
                                <input type="date" class="form-control scadenza-input"
                                       data-id="{{ $magazzino->id }}"
                                       value="{{ $magazzino->prossima_scadenza ?? '' }}"
                                       onchange="updateScadenza({{ $magazzino->id }}, this)">
                            </td>
                            <td>
                                <form action="{{ route('magazzini.destroy', $magazzino) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro di voler eliminare questo magazzino?')">Elimina</button>
                                </form>
                            </td>
                            <td>
                                <a href="{{ route('giacenze.create', ['magazzino' => $magazzino->id]) }}" class="btn btn-secondary btn-sm">Vedi Giacenze</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.scadenza-input').forEach(input => {
        colorizeDate(input);
    });

    function colorizeDate(input) {
        const dateValue = input.value;
        if (!dateValue) return;

        const today = new Date();
        const scadenzaDate = new Date(dateValue);
        scadenzaDate.setHours(0, 0, 0, 0);
        today.setHours(0, 0, 0, 0);

        const diffTime = scadenzaDate - today;
        const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)); // differenza in giorni

        // Reset
        input.style.backgroundColor = '';
        input.style.color = '';

        if (diffDays >= -7 && diffDays <= 14) {
            // GIALLO
            input.style.backgroundColor = '#ffeb3b';
            input.style.color = '#000';
        } else if (diffDays < -14) {
            // ROSSO
            input.style.backgroundColor = '#dc3545';
            input.style.color = '#fff';
        }
    }

    let timeout;

    function updateScadenza(id, input) {
        clearTimeout(timeout);

        timeout = setTimeout(() => {
            const nuovaScadenza = input.value;

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
                if (data.success) {
                    colorizeDate(input); // Ricalcola i colori dopo aggiornamento
                } else {
                    alert("Errore nell'aggiornamento della data.");
                }
            })
            .catch(error => console.error('Errore:', error));
        }, 500);
    }
</script>


@endsection
