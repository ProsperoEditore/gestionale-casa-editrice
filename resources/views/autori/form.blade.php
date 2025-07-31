@extends('layouts.app')

@section('content')

<form action="{{ isset($autore) ? route('autori.update', $autore) : route('autori.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($autore))
        @method('PUT')
    @endif
    
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" class="form-control" value="{{ old('nome', $autore->nome ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Cognome</label>
        <input type="text" name="cognome" class="form-control" value="{{ old('cognome', $autore->cognome ?? '') }}">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Pseudonimo</label>
        <input type="text" name="pseudonimo" class="form-control" value="{{ old('pseudonimo', $autore->pseudonimo ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Denominazione (per enti)</label>
        <input type="text" name="denominazione" class="form-control" value="{{ old('denominazione', $autore->denominazione ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Codice Fiscale</label>
    <input type="text" name="codice_fiscale" class="form-control" value="{{ old('codice_fiscale', $autore->codice_fiscale ?? '') }}">
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Data di nascita</label>
        <input type="date" name="data_nascita" class="form-control" value="{{ old('data_nascita', $autore->data_nascita ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Luogo di nascita</label>
        <input type="text" name="luogo_nascita" class="form-control" value="{{ old('luogo_nascita', $autore->luogo_nascita ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">IBAN</label>
    <input type="text" name="iban" class="form-control" value="{{ old('iban', $autore->iban ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Indirizzo</label>
    <input type="text" name="indirizzo" class="form-control" value="{{ old('indirizzo', $autore->indirizzo ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Biografia</label>
    <textarea name="biografia" class="form-control" rows="5">{{ old('biografia', $autore->biografia ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Libri associati</label>
    <input type="text" id="autocomplete-libro" class="form-control" placeholder="Scrivi titolo del libro">
    <button type="button" class="btn btn-outline-primary mt-2" onclick="aggiungiLibro()">+ Aggiungi libro</button>

    <ul id="libriSelezionati" class="mt-3 list-group">
        @if(old('libri'))
            @foreach(old('libri') as $id)
                @php $libro = \App\Models\Libro::find($id); @endphp
                @if($libro)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $libro->titolo }}
                        <input type="hidden" name="libri[]" value="{{ $libro->id }}">
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.parentNode.remove()">✕</button>
                    </li>
                @endif
            @endforeach
        @elseif(isset($autore))
            @foreach($autore->libri as $libro)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $libro->titolo }}
                    <input type="hidden" name="libri[]" value="{{ $libro->id }}">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.parentNode.remove()">✕</button>
                </li>
            @endforeach
        @endif
    </ul>
</div>

<div class="mb-3">
    <label class="form-label">Foto (opzionale)</label>
    <input type="file" name="foto" class="form-control">
    @if(isset($autore) && $autore->foto)
        <div class="mt-2">
            <img src="{{ asset('storage/foto_autori/' . $autore->foto) }}" alt="Foto autore" height="100">
        </div>
    @endif
</div>

<button type="submit" class="btn btn-primary">Salva</button>
</form>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
let selectedLibroId = null;

$("#autocomplete-libro").autocomplete({
    source: function(request, response) {
        $.ajax({
            url: "{{ route('autori.autocomplete-libro') }}",
            dataType: "json",
            data: { term: request.term },
            success: function(data) {
                response(data);
            }
        });
    },
    minLength: 2,
    focus: function(event, ui) {
        $("#autocomplete-libro").val($(ui.item.label).text());
        return false;
    },
    select: function(event, ui) {
        $("#autocomplete-libro").val($(ui.item.label).text());
        selectedLibroId = ui.item.value;
        return false;
    }
}).autocomplete("instance")._renderItem = function(ul, item) {
    return $("<li>")
        .append("<div class='ui-menu-item-wrapper'>" + item.label + "</div>")
        .appendTo(ul);
};

function aggiungiLibro() {
    const titolo = $('#autocomplete-libro').val();
    const id = selectedLibroId;

    if (!id) {
        alert("⚠️ Seleziona un libro dal menu a tendina prima di aggiungere.");
        return;
    }

    if (!$(`input[name="libri[]"][value="${id}"]`).length) {
        const li = `<li class="list-group-item d-flex justify-content-between align-items-center">
            ${titolo}
            <input type="hidden" name="libri[]" value="${id}">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.parentNode.remove()">✕</button>
        </li>`;
        $('#libriSelezionati').append(li);
    }

    $('#autocomplete-libro').val('');
    selectedLibroId = null;
}
</script>
@endpush


@push('styles')
<style>
.ui-autocomplete {
    max-height: 200px;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1050 !important;
    font-size: 14px;
}

.ui-menu-item-wrapper {
    padding: 8px 12px;
    line-height: 1.4;
    white-space: normal;
    font-family: system-ui, sans-serif;
    border-bottom: 1px solid #e9ecef;
}

.ui-menu-item-wrapper strong {
    display: block;
    font-weight: 600;
    color: #212529;
}

.ui-menu-item-wrapper small {
    color: #6c757d;
    font-size: 12px;
}
</style>
@endpush

