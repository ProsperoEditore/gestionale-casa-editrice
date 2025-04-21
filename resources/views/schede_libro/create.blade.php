@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crea nuova scheda libro</h1>

    <form action="{{ route('schede-libro.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Autocomplete per selezionare un libro esistente --}}
        <div class="mb-3">
            <label for="libro_id" class="form-label">Libro</label>
            <input type="text" id="libro_autocomplete" class="form-control titolo-autocomplete" placeholder="Cerca titolo o ISBN..." required>
            <input type="hidden" name="libro_id" class="libro-id" id="libro_id">
        </div>

        {{-- Campi descrittivi facoltativi --}}
        <div class="mb-3">
            <label for="descrizione_breve" class="form-label">Descrizione breve</label>
            <textarea class="form-control" name="descrizione_breve" rows="2"></textarea>
        </div>

        <div class="mb-3">
            <label for="sinossi" class="form-label">Sinossi</label>
            <textarea class="form-control" name="sinossi" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <label for="strillo" class="form-label">Strillo</label>
            <textarea class="form-control" name="strillo" rows="2"></textarea>
        </div>

        <div class="mb-3">
            <label for="extra" class="form-label">Extra</label>
            <textarea class="form-control" name="extra" rows="2"></textarea>
        </div>

        <div class="mb-3">
            <label for="biografia_autore" class="form-label">Biografia autore</label>
            <textarea class="form-control" name="biografia_autore" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="formato" class="form-label">Formato</label>
            <input type="text" class="form-control" name="formato">
        </div>

        <div class="mb-3">
            <label for="numero_pagine" class="form-label">Numero pagine</label>
            <input type="number" class="form-control" name="numero_pagine">
        </div>

        {{-- Upload immagini --}}
        <div class="mb-3">
            <label for="copertina" class="form-label">Prima di copertina (jpg)</label>
            <input type="file" class="form-control" name="copertina" accept="image/jpeg,image/jpg">
        </div>

        <div class="mb-3">
            <label for="copertina_stesa" class="form-label">Copertina stesa (jpg)</label>
            <input type="file" class="form-control" name="copertina_stesa" accept="image/jpeg,image/jpg">
        </div>

        <button type="submit" class="btn btn-primary">Salva scheda</button>
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
