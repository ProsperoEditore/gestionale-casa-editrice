@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Modifica Libro</h3>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('libri.update', $libro->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">ISBN</label>
                        <input type="text" name="isbn" class="form-control" value="{{ $libro->isbn }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Titolo</label>
                        <input type="text" name="titolo" class="form-control" value="{{ $libro->titolo }}" required>
                    </div>

                    <!-- ✅ Marchio Editoriale - Menù a tendina -->
                    <div class="mb-3">
                        <label class="form-label">Marchio Editoriale</label>
                        <select name="marchio_editoriale_id" class="form-control" required>
                            @foreach($marchi as $marchio)
                                <option value="{{ $marchio->id }}" {{ $libro->marchio_editoriale_id == $marchio->id ? 'selected' : '' }}>
                                    {{ $marchio->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Collana</label>
                        <input type="text" name="collana" class="form-control" value="{{ $libro->collana }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data di Pubblicazione</label>
                        <input type="date" name="data_pubblicazione" class="form-control" value="{{ $libro->data_pubblicazione }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Anno di Pubblicazione</label>
                        <input type="number" name="anno_pubblicazione" class="form-control" value="{{ $libro->anno_pubblicazione }}" required>
                    </div>

                    <!-- ✅ Prezzo - Accetta Decimali -->
                    <div class="mb-3">
                        <label class="form-label">Prezzo (€)</label>
                        <input type="number" name="prezzo" class="form-control" step="0.01" min="0" value="{{ $libro->prezzo }}" required>
                    </div>

                    <!-- ✅ Costo di Produzione - Accetta Decimali -->
                    <div class="mb-3">
                        <label class="form-label">Costo di Produzione (€)</label>
                        <input type="number" name="costo_produzione" class="form-control" step="0.01" min="0" value="{{ $libro->costo_produzione }}">
                    </div>

                    <!-- ✅ Stato - Menù a tendina -->
                    <div class="mb-3">
                        <label class="form-label">Stato</label>
                        <select name="stato" class="form-control">
                            <option value="C" {{ $libro->stato == 'C' ? 'selected' : '' }}>In commercio</option>
                            <option value="FC" {{ $libro->stato == 'FC' ? 'selected' : '' }}>Fuori catalogo</option>
                            <option value="A" {{ $libro->stato == 'A' ? 'selected' : '' }}>Accantonato</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data Cessazione Commercio</label>
                        <input type="date" name="data_cessazione_commercio" class="form-control" value="{{ $libro->data_cessazione_commercio }}">
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">Aggiorna</button>
                        <a href="{{ route('libri.index') }}" class="btn btn-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
