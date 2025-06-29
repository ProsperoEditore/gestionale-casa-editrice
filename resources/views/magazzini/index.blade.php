@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Magazzini</h3>

    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif


        @php
            $scarichiDaApprovare = \App\Models\ScaricoRichiesto::where('stato', 'in attesa')->count();
        @endphp

        @if($scarichiDaApprovare > 0)
            <div class="text-center mb-3">
                <a href="{{ route('scarichi-richiesti.index') }}" class="btn btn-warning btn-lg fw-bold shadow px-4 py-2">
                    ⚠️ Ci sono degli scarichi da approvare! ({{ $scarichiDaApprovare }})
                </a>
            </div>
        @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-3">
        <a href="{{ route('magazzini.create') }}" class="btn btn-success">Aggiungi Nuovo</a>

        <form action="{{ route('magazzini.index') }}" method="GET" class="d-flex flex-wrap gap-2">
            <select name="search" class="form-control select2" onchange="this.form.submit()">
                <option value="">Cerca per nome...</option>
                @foreach(\App\Models\Anagrafica::orderBy('nome')->get() as $anagrafica)
                    <option value="{{ $anagrafica->nome_completo }}" {{ request('search') == $anagrafica->nome_completo ? 'selected' : '' }}>
                        {{ $anagrafica->nome_completo }}
                    </option>
                @endforeach
            </select>
            <select name="categoria" class="form-select">
                <option value="">Cerca per categoria...</option>
                <option value="magazzino editore" {{ request('categoria') == 'magazzino editore' ? 'selected' : '' }}>Magazzino Editore</option>
                <option value="libreria cliente" {{ request('categoria') == 'libreria cliente' ? 'selected' : '' }}>Libreria Cliente</option>
            </select>
            <button class="btn btn-outline-primary">Cerca</button>
        </form>
    </div>

        <div class="card d-none d-md-block">
            <div class="card-body">
                <table class="table table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>Categoria</th>
                        <th>Nome</th>
                        <th>Contatti</th>
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
                            <td>{{ $magazzino->anagrafica->nome_completo ?? 'N/A' }}</td>
                            <td>
                                {{ $magazzino->anagrafica->email ?? 'N/A' }}<br>
                                <small>{{ $magazzino->anagrafica->telefono ?? 'N/A' }}</small>
                            </td>
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
                            <td class="align-middle">
                                <!-- 📩 Bottone INVIA EMAIL -->
                                <form action="{{ route('magazzini.inviaRendiconto', $magazzino->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn p-0 border-0 bg-transparent text-primary" title="Richiedi rendiconto via email">
                                        <i class="bi bi-envelope fs-5"></i>
                                    </button>
                                </form>

                                <!-- 🗑 Bottone ELIMINA -->
                                <form action="{{ route('magazzini.destroy', $magazzino) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn p-0 border-0 bg-transparent text-danger" title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo magazzino?')">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                            </td>
                            @endif

                            <td class="align-middle">
                                <a href="{{ route('giacenze.create', ['magazzino' => $magazzino->id]) }}" class="text-secondary" title="Vedi Giacenze">
                                    <i class="bi bi-box-seam fs-5"></i>
                                </a>
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


<!-- MOBILE -->
<div class="d-block d-md-none">
    @foreach($magazzini as $magazzino)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $magazzino->anagrafica->nome_completo ?? 'N/A' }}</h5>
                <p class="mb-1"><strong>Categoria:</strong> {{ $magazzino->anagrafica->categoria ?? 'N/A' }}</p>
                <p class="mb-1"><strong>Contatti:</strong><br>
                    {{ $magazzino->anagrafica->email ?? 'N/A' }}<br>
                    <small>{{ $magazzino->anagrafica->telefono ?? 'N/A' }}</small>
                </p>

                <p class="mb-1"><strong>Prossima Scadenza:</strong>
                    @if(optional($magazzino->anagrafica)->categoria === 'magazzino editore')
                        <span class="badge bg-secondary">N.D.</span>
                    @else
                        <input type="date" class="form-control scadenza-input"
                            data-id="{{ $magazzino->id }}"
                            value="{{ $magazzino->prossima_scadenza ? \Carbon\Carbon::parse($magazzino->prossima_scadenza)->format('Y-m-d') : '' }}"
                            onchange="updateScadenza({{ $magazzino->id }}, this)">
                    @endif
                </p>

                @if(auth()->user()->ruolo !== 'utente')
                    <div class="d-flex flex-wrap gap-3 mt-2">
                        <strong>Azioni:</strong>
                        <form action="{{ route('magazzini.destroy', $magazzino) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Elimina">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                @endif

                <div class="d-flex flex-wrap gap-3 mt-2">
                    <strong>Giacenze:</strong>
                    <a href="{{ route('giacenze.create', ['magazzino' => $magazzino->id]) }}" class="btn btn-sm btn-secondary" title="Vedi Giacenze">
                        <i class="bi bi-box-seam"></i>
                    </a>
                </div>
            </div>
        </div>
    @endforeach

        <div class="d-flex justify-content-center mt-3">
        {{ $magazzini->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
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

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({
        placeholder: "Cerca per nome...",
        allowClear: true
    });
});
</script>

@endsection
