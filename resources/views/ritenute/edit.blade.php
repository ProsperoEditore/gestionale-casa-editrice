@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Modifica ritenuta d'acconto per Diritti d'Autore</h3>

    <form action="{{ route('ritenute.update', $ritenuta->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3 g-3">
            <div class="col-md-4 col-12">
                <label>Nome</label>
                <input type="text" name="nome_autore" class="form-control" value="{{ $ritenuta->nome_autore }}" required>
            </div>
            <div class="col-md-4 col-12">
                <label>Cognome</label>
                <input type="text" name="cognome_autore" class="form-control" value="{{ $ritenuta->cognome_autore }}" required>
            </div>
            <div class="col-md-4 col-12">
                <label>Codice Fiscale</label>
                <input type="text" name="codice_fiscale" class="form-control" value="{{ $ritenuta->codice_fiscale }}" required>
            </div>
        </div>

        <div class="row mb-3 g-3">
            <div class="col-md-4 col-12">
                <label>Luogo di nascita</label>
                <input type="text" name="luogo_nascita" class="form-control" value="{{ $ritenuta->luogo_nascita }}" required>
            </div>
            <div class="col-md-4 col-12">
                <label>Data di nascita</label>
                <div class="input-group">
                    <input type="date" name="data_nascita" id="data_nascita" class="form-control" value="{{ $ritenuta->data_nascita->format('Y-m-d') }}" required>
                    <span class="input-group-text" id="etichetta_eta">—</span>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <label>IBAN</label>
                <input type="text" name="iban" class="form-control" value="{{ $ritenuta->iban }}">
            </div>
        </div>

        <div class="mb-3">
            <label>Indirizzo</label>
            <input type="text" name="indirizzo" class="form-control" value="{{ $ritenuta->indirizzo }}">
        </div>

        <div class="row mb-3 g-3">
            <div class="col-md-4 col-12">
                <label>Marchio editoriale</label>
                <select name="marchio_id" class="form-select">
                    <option value="">-- Seleziona --</option>
                    @foreach($marchi as $marchio)
                        <option value="{{ $marchio->id }}" {{ $ritenuta->marchio_id == $marchio->id ? 'selected' : '' }}>{{ $marchio->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-12">
                <label>Numero nota</label>
                <input type="text" name="numero_nota" class="form-control" value="{{ $ritenuta->numero }}" required>
            </div>
            <div class="col-md-4 col-12">
                <label>Luogo</label>
                <input type="text" name="luogo" class="form-control" value="{{ $ritenuta->luogo }}">
            </div>
            <div class="col-md-4 col-12">
                <label>Data emissione</label>
                <input type="date" name="data_emissione" id="data_emissione" class="form-control" value="{{ $ritenuta->data_emissione->format('Y-m-d') }}" required>
            </div>
        </div>

        {{-- Prestazioni e script JS uguale a create.blade.php --}}

        <button type="submit" class="btn btn-primary">Aggiorna</button>
    </form>
</div>
@endsection
