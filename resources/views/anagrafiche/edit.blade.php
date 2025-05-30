@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Modifica Anagrafica</h3>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('anagrafiche.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Categoria con Select -->
                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select name="categoria" class="form-control" required>
                            <option value="magazzino editore" {{ $item->categoria == 'magazzino editore' ? 'selected' : '' }}>Magazzino Editore</option>
                            <option value="sito" {{ $item->categoria == 'sito' ? 'selected' : '' }}>Sito</option>
                            <option value="libreria c.e." {{ $item->categoria == 'libreria c.e.' ? 'selected' : '' }}>Libreria C.E.</option>
                            <option value="libreria cliente" {{ $item->categoria == 'libreria cliente' ? 'selected' : '' }}>Libreria Cliente</option>
                            <option value="privato" {{ $item->categoria == 'privato' ? 'selected' : '' }}>Privato</option>
                            <option value="biblioteca" {{ $item->categoria == 'biblioteca' ? 'selected' : '' }}>Biblioteca</option>
                            <option value="associazione" {{ $item->categoria == 'associazione' ? 'selected' : '' }}>Associazione</option>
                            <option value="università" {{ $item->categoria == 'università' ? 'selected' : '' }}>Università</option>
                            <option value="grossista" {{ $item->categoria == 'grossista' ? 'selected' : '' }}>Grossista</option>
                            <option value="distributore" {{ $item->categoria == 'distributore' ? 'selected' : '' }}>Distributore</option>
                            <option value="fiere" {{ $item->categoria == 'fiere' ? 'selected' : '' }}>Fiere</option>
                            <option value="festival" {{ $item->categoria == 'festival' ? 'selected' : '' }}>Festival</option>
                            <option value="altro" {{ $item->categoria == 'altro' ? 'selected' : '' }}>Altro</option>
                        </select>
                    </div>

                    <div class="mb-3"><label class="form-label">Nome</label><input type="text" name="nome" value="{{ $item->nome }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Indirizzo di Fatturazione</label><input type="text" name="indirizzo_fatturazione" value="{{ $item->indirizzo_fatturazione }}" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Indirizzo di Spedizione</label><input type="text" name="indirizzo_spedizione" value="{{ $item->indirizzo_spedizione }}" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Partita IVA</label><input type="text" name="partita_iva" value="{{ $item->partita_iva }}" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Codice Fiscale</label><input type="text" name="codice_fiscale" id="codice_fiscale" value="{{ $item->codice_fiscale }}" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ $item->email }}" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Numero di Telefono</label><input type="text" name="telefono" value="{{ $item->telefono }}" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">PEC</label><input type="email" name="pec" value="{{ $item->pec }}" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Codice Univoco</label><input type="text" name="codice_univoco" id="codice_univoco" value="{{ $item->codice_univoco }}" class="form-control"></div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">Aggiorna</button>
                        <a href="{{ route('anagrafiche.index') }}" class="btn btn-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        ['codice_fiscale', 'codice_univoco'].forEach(function (id) {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', function () {
                    this.value = this.value.toUpperCase();
                });
            }
        });
    });
    </script>



@endsection
