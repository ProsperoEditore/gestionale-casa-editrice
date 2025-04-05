@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center">Crea Registro Vendite</h3>

    <form action="{{ route('registro-vendite.store') }}" method="POST">
        @csrf

        <div class="card mt-4">
            <div class="card-body">
                <div class="mb-3">
                    <label>Anagrafica associata</label>
                    <select class="form-control select2" name="anagrafica_id" required>
                        <option></option> <!-- necessario per placeholder -->
                        @foreach ($anagrafiche as $anagrafica)
                            <option value="{{ $anagrafica->id }}">{{ $anagrafica->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Canale vendita</label>
                    <select class="form-control" name="canale_vendita" required>
                        <option value="Vendite indirette">Vendite indirette</option>
                        <option value="Vendite dirette">Vendite dirette</option>
                        <option value="Eventi">Eventi</option>
                        <option value="Altro">Altro</option>
                    </select>
                </div>

                
                <button type="submit" class="btn btn-success">Salva</button>
            </div>
        </div>
    </form>
</div>


@endsection


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