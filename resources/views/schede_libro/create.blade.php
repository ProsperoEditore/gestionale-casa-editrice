@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crea nuova scheda libro</h1>

    <form action="{{ route('schede-libro.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Autocomplete per selezionare un libro esistente --}}
        <div class="mb-3">
            <label for="libro_id" class="form-label">Libro</label>
            <input type="text" id="titolo_libro" class="form-control" placeholder="Cerca libro per titolo o ISBN">
            <input type="hidden" name="libro_id" id="libro_id">
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

@section('scripts')
<script>
    const libri = @json($libri->map(function($l) {
        return [
            'id'    => $l->id,
            'label' => $l->titolo + ' [' + $l->isbn + ']',
            'value' => $l->titolo
        ];
    }));

    $(function () {
        $('#titolo_libro').autocomplete({
            source: libri,
            select: function (event, ui) {
                $('#titolo_libro').val(ui.item.label); // mostra Titolo [ISBN]
                $('#libro_id').val(ui.item.id);        // salva solo l'ID nascosto
                return false;
            }
        });
    });
</script>
@endsection
