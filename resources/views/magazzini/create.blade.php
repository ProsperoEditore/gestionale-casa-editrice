@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Crea Nuovo Magazzino</h2>

    <div class="card">
        <div class="card-body">
        <form action="{{ url('/magazzini/store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="anagrafica_id" class="form-label">Anagrafica Associata</label>
                    <select name="anagrafica_id" id="anagrafica_id" class="form-control select2" required>
                        <option value="">Seleziona un'anagrafica</option>
                        @foreach($anagrafiche as $anagrafica)
                            <option value="{{ $anagrafica->id }}">{{ $anagrafica->nome }} ({{ $anagrafica->categoria }})</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Salva</button>
                <a href="{{ route('magazzini.index') }}" class="btn btn-secondary">Annulla</a>
            </form>
        </div>
    </div>
</div>

    <!-- Inclusione di Select2 per l'autocompletamento -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Cerca un'anagrafica",
                allowClear: true
            });
        });
    </script>
@endsection