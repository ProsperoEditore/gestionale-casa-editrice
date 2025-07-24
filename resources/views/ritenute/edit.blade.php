@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Modifica Ritenuta {{ $ritenuta->numero }}</h3>

    <form action="{{ route('ritenute.update', $ritenuta) }}" method="POST">
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
                <input type="date" name="data_nascita" class="form-control" value="{{ $ritenuta->data_nascita->format('Y-m-d') }}" required>
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
            <div class="col-md-6 col-12">
                <label>Marchio editoriale</label>
                <select name="marchio_id" class="form-select">
                    <option value="">-- Seleziona --</option>
                    @foreach($marchi as $marchio)
                        <option value="{{ $marchio->id }}" @selected($ritenuta->marchio_id == $marchio->id)>{{ $marchio->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-12">
                <label>Data emissione</label>
                <input type="date" name="data_emissione" class="form-control" value="{{ $ritenuta->data_emissione->format('Y-m-d') }}" required>
            </div>
        </div>

        <div class="mb-4">
            <h5>Prestazioni</h5>
            <div class="table-responsive">
                <table class="table" id="tabella-prestazioni">
                    <thead><tr><th>Descrizione</th><th>Importo</th></tr></thead>
                    <tbody>
                        @foreach($ritenuta->prestazioni as $p)
                            <tr>
                                <td><input name="prestazioni[][descrizione]" class="form-control" value="{{ $p['descrizione'] }}"></td>
                                <td><input name="prestazioni[][importo]" class="form-control" value="{{ $p['importo'] }}"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mb-3 g-3">
            <div class="col-md-6 col-12">
                <label>Nota IVA</label>
                <input type="text" name="nota_iva" class="form-control" value="{{ $ritenuta->nota_iva }}">
            </div>
            <div class="col-md-6 col-12">
                <label>Marca da bollo</label>
                <input type="text" name="marca_bollo" class="form-control" value="{{ $ritenuta->marca_bollo }}">
            </div>
        </div>

        <button type="submit" class="btn btn-success">Aggiorna</button>
    </form>
</div>
@endsection