@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifica scheda libro</h1>

    <form action="{{ route('schede-libro.update', $scheda->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Autocomplete libro --}}
        <div class="mb-3">
            <label for="libro_autocomplete" class="form-label">Libro</label>
            <input type="text" id="libro_autocomplete" class="form-control titolo-autocomplete"
                value="{{ $scheda->libro->titolo }} [{{ $scheda->libro->isbn }}]" required>
            <input type="hidden" name="libro_id" id="libro_id" value="{{ $scheda->libro->id }}">
        </div>

        {{-- Campi testuali --}}
        <div class="mb-3">
            <label for="descrizione_breve" class="form-label">Descrizione breve</label>
            <textarea class="form-control" name="descrizione_breve" rows="2">{{ $scheda->descrizione_breve }}</textarea>
        </div>

        <div class="mb-3">
            <label for="sinossi" class="form-label">Sinossi</label>
            <textarea class="form-control" name="sinossi" rows="4">{{ $scheda->sinossi }}</textarea>
        </div>

        <div class="mb-3">
            <label for="strillo" class="form-label">Strillo</label>
            <textarea class="form-control" name="strillo" rows="2">{{ $scheda->strillo }}</textarea>
        </div>

        <div class="mb-3">
            <label for="extra" class="form-label">Extra</label>
            <textarea class="form-control" name="extra" rows="2">{{ $scheda->extra }}</textarea>
        </div>

        <div class="mb-3">
            <label for="biografia_autore" class="form-label">Biografia autore</label>
            <textarea class="form-control" name="biografia_autore" rows="3">{{ $scheda->biografia_autore }}</textarea>
        </div>

        <div class="mb-3">
            <label for="formato" class="form-label">Formato</label>
            <input type="text" class="form-control" name="formato" value="{{ $scheda->formato }}">
        </div>

        <div class="mb-3">
            <label for="numero_pagine" class="form-label">Numero pagine</label>
            <input type="number" class="form-control" name="numero_pagine" value="{{ $scheda->numero_pagine }}">
        </div>

        {{-- Upload immagini --}}
        <div class="mb-3">
            <label for="copertina" class="form-label">Prima di copertina (jpg)</label><br>
            @if ($scheda->copertina_path)
                <img src="{{ asset('storage/' . $scheda->copertina_path) }}" alt="Copertina" style="height: 80px;">
            @endif
            <input type="file" class="form-control mt-2" name="copertina" accept="image/jpeg,image/jpg">
        </div>

        <div class="mb-3">
            <label for="copertina_stesa" class="form-label">Copertina stesa (jpg)</label><br>
            @if ($scheda->copertina_stesa_path)
                <img src="{{ asset('storage/' . $scheda->copertina_stesa_path) }}" alt="Copertina stesa" style="height: 80px;">
            @endif
            <input type="file" class="form-control mt-2" name="copertina_stesa" accept="image/jpeg,image/jpg">
        </div>

        <button type="submit" class="btn btn-success">Salva modifiche</button>
        <a href="{{ route('schede-libro.index') }}" class="btn btn-secondary">Annulla</a>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<script>
$(function () {
    $("#libro_autocomplete").autocomplete({
        minLength: 2,
        delay: 100,
        source: function (request, response) {
            $.ajax({
                url: "{{ route('scheda-libro.autocomplete-libro') }}",
                data: { query: request.term },
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            label: item.titolo + ' [' + item.isbn + ']',
                            value: item.titolo + ' [' + item.isbn + ']',
                            id: item.id
                        };
                    }));
                }
            });
        },
        select: function (event, ui) {
            $("#libro_autocomplete").val(ui.item.label);
            $("#libro_id").val(ui.item.id);
            return false;
        }
    });
});
</script>
@endpush
