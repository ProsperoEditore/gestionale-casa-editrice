@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Modifica anagrafica esistente</h3>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('anagrafiche.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Categoria con Select -->
                    <div class="mb-3">
                        <label class="form-label">Categoria <span class="text-danger">*</span></label>
                        <select name="categoria" class="form-control" required>
                            <option value="magazzino editore" {{ $item->categoria == 'magazzino editore' ? 'selected' : '' }}>Magazzino Editore</option>
                            <option value="sito" {{ $item->categoria == 'sito' ? 'selected' : '' }}>Sito</option>
                            <option value="libreria c.e." {{ $item->categoria == 'libreria c.e.' ? 'selected' : '' }}>Libreria C.E.</option>
                            <option value="libreria cliente" {{ $item->categoria == 'libreria cliente' ? 'selected' : '' }}>Libreria Cliente</option>
                            <option value="privato" {{ $item->categoria == 'privato' ? 'selected' : '' }}>Privato</option>
                            <option value="biblioteca" {{ $item->categoria == 'biblioteca' ? 'selected' : '' }}>Biblioteca</option>
                            <option value="associazione" {{ $item->categoria == 'associazione' ? 'selected' : '' }}>Associazione</option>
                            <option value="università" {{ $item->categoria == 'università' ? 'selected' : '' }}>Università</option>
                            <option value="scuola" {{ $item->categoria == 'scuola' ? 'selected' : '' }}>Scuola</option>
                            <option value="grossista" {{ $item->categoria == 'grossista' ? 'selected' : '' }}>Grossista</option>
                            <option value="distributore" {{ $item->categoria == 'distributore' ? 'selected' : '' }}>Distributore</option>
                            <option value="fiere" {{ $item->categoria == 'fiere' ? 'selected' : '' }}>Fiere</option>
                            <option value="festival" {{ $item->categoria == 'festival' ? 'selected' : '' }}>Festival</option>
                            <option value="altro" {{ $item->categoria == 'altro' ? 'selected' : '' }}>Altro</option>
                        </select>
                    </div>

                    <div class="mb-3">
    <label class="form-label">
        Tipo di Fatturazione <span class="text-danger">*</span>
    </label>
    <select name="tipo_fatturazione" class="form-select" required>
        <option value="">-- Seleziona --</option>
        <option value="B2B" {{ old('tipo_fatturazione', $item->tipo_fatturazione ?? 'B2B') === 'B2B' ? 'selected' : '' }}>
            Fatturazione elettronica B2B
        </option>
        <option value="PA" {{ old('tipo_fatturazione', $item->tipo_fatturazione ?? 'B2B') === 'PA' ? 'selected' : '' }}>
            Fatturazione elettronica PA
        </option>
    </select>
</div>


                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Denominazione <span class="text-danger">*</span> <span class="badge bg-info">alternativa a Nome e Cognome</span></label>
                            <input type="text" name="denominazione" id="denominazione" value="{{ $item->denominazione }}" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="nome" value="{{ $item->nome }}" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Cognome <span class="text-danger">*</span></label>
                            <input type="text" name="cognome" id="cognome" value="{{ $item->cognome }}" class="form-control">
                        </div>
                    </div>

                    <h5 class="mt-4">Indirizzo di Fatturazione</h5>
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Indirizzo</label>
        <input type="text" name="via_fatturazione" value="{{ $item->via_fatturazione }}" class="form-control">
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Civico</label>
        <input type="text" name="civico_fatturazione" value="{{ $item->civico_fatturazione }}" class="form-control">
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">CAP</label>
        <input type="text" name="cap_fatturazione" value="{{ $item->cap_fatturazione }}" class="form-control">
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Comune</label>
        <input type="text" name="comune_fatturazione" value="{{ $item->comune_fatturazione }}" class="form-control">
    </div>
    <div class="col-md-1 mb-3">
        <label class="form-label">Provincia</label>
        <input type="text" name="provincia_fatturazione" value="{{ $item->provincia_fatturazione }}" class="form-control">
    </div>
    <div class="col-md-1 mb-3">
        <label class="form-label">Nazione</label>
        <input type="text" name="nazione_fatturazione" value="{{ $item->nazione_fatturazione }}" class="form-control">
    </div>
</div>

<h5 class="mt-4">Indirizzo di Spedizione</h5>
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Indirizzo</label>
        <input type="text" name="via_spedizione" value="{{ $item->via_spedizione }}" class="form-control">
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Civico</label>
        <input type="text" name="civico_spedizione" value="{{ $item->civico_spedizione }}" class="form-control">
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">CAP</label>
        <input type="text" name="cap_spedizione" value="{{ $item->cap_spedizione }}" class="form-control">
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Comune</label>
        <input type="text" name="comune_spedizione" value="{{ $item->comune_spedizione }}" class="form-control">
    </div>
    <div class="col-md-1 mb-3">
        <label class="form-label">Provincia</label>
        <input type="text" name="provincia_spedizione" value="{{ $item->provincia_spedizione }}" class="form-control">
    </div>
    <div class="col-md-1 mb-3">
        <label class="form-label">Nazione</label>
        <input type="text" name="nazione_spedizione" value="{{ $item->nazione_spedizione }}" class="form-control">
    </div>
</div>


                    <div class="mb-3">
                        <label class="form-label">Partita IVA</label>
                        <input type="text" name="partita_iva" value="{{ $item->partita_iva }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Codice Fiscale</label>
                        <input type="text" name="codice_fiscale" id="codice_fiscale" value="{{ $item->codice_fiscale }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ $item->email }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Numero di Telefono</label>
                        <input type="text" name="telefono" value="{{ $item->telefono }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PEC</label>
                        <input type="email" name="pec" value="{{ $item->pec }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Codice Univoco</label>
                        <input type="text" name="codice_univoco" id="codice_univoco" value="{{ $item->codice_univoco }}" class="form-control">
                    </div>

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

            const denominazione = document.getElementById('denominazione');
            const nome = document.getElementById('nome');
            const cognome = document.getElementById('cognome');

            function aggiornaCampi() {
                const den = denominazione.value.trim();
                const nom = nome.value.trim();
                const cog = cognome.value.trim();

                if (den !== '') {
                    nome.disabled = true;
                    cognome.disabled = true;
                } else {
                    nome.disabled = false;
                    cognome.disabled = false;
                }

                if (nom !== '' || cog !== '') {
                    denominazione.disabled = true;
                } else {
                    denominazione.disabled = false;
                }
            }

            denominazione.addEventListener('input', aggiornaCampi);
            nome.addEventListener('input', aggiornaCampi);
            cognome.addEventListener('input', aggiornaCampi);

            aggiornaCampi();
        });
    </script>
@endsection
