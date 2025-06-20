@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Crea Nuovo Anagrafiche</h3>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('anagrafiche.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select name="categoria" class="form-control" required>
                            <option value="magazzino editore">Magazzino Editore</option>
                            <option value="sito">Sito</option>
                            <option value="libreria c.e.">Libreria C.E.</option>
                            <option value="libreria cliente">Libreria Cliente</option>
                            <option value="privato">Privato</option>
                            <option value="biblioteca">Biblioteca</option>
                            <option value="associazione">Associazione</option>
                            <option value="università">Università</option>
                            <option value="scuola">Scuola</option>
                            <option value="grossista">Grossista</option>
                            <option value="distributore">Distributore</option>
                            <option value="fiere">Fiere</option>
                            <option value="festival">Festival</option>
                            <option value="altro">Altro</option>
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
                            <label class="form-label">
                                Denominazione <span class="text-danger">*</span>
                                <span title="Alternativo a Nome + Cognome" style="cursor: help;">ℹ️</span>
                            </label>
                            <input type="text" name="denominazione" id="denominazione" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="nome" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Cognome <span class="text-danger">*</span></label>
                            <input type="text" name="cognome" id="cognome" class="form-control">
                        </div>
                    </div>


                        <h5 class="mt-4">Indirizzo di Fatturazione</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Indirizzo</label>
                                <input type="text" name="via_fatturazione" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Numero civico</label>
                                <input type="text" name="civico_fatturazione" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">CAP</label>
                                <input type="text" name="cap_fatturazione" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Comune</label>
                                <input type="text" name="comune_fatturazione" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Provincia</label>
                                <input type="text" name="provincia_fatturazione" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nazione</label>
                                <input type="text" name="nazione_fatturazione" class="form-control" value="IT">
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="copiaIndirizzo" checked>
                            <label class="form-check-label" for="copiaIndirizzo">
                                L'indirizzo di spedizione è uguale a quello di fatturazione
                            </label>
                        </div>

                        <h5 class="mt-4">Indirizzo di Spedizione</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Indirizzo</label>
                                <input type="text" name="via_spedizione" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Numero civico</label>
                                <input type="text" name="civico_spedizione" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">CAP</label>
                                <input type="text" name="cap_spedizione" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Comune</label>
                                <input type="text" name="comune_spedizione" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Provincia</label>
                                <input type="text" name="provincia_spedizione" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nazione</label>
                                <input type="text" name="nazione_spedizione" class="form-control" value="IT">
                            </div>
                        </div>

                    <div class="mb-3">
                        <label class="form-label">Partita IVA</label>
                        <input type="text" name="partita_iva" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Codice Fiscale</label>
                        <input type="text" name="codice_fiscale" id="codice_fiscale" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Numero di Telefono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PEC</label>
                        <input type="email" name="pec" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Codice Univoco</label>
                        <input type="text" name="codice_univoco" id="codice_univoco" class="form-control">
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-success">Salva</button>
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

    <script>
    document.getElementById('copiaIndirizzo').addEventListener('change', function () {
        const copia = this.checked;
        const campi = ['via', 'civico', 'cap', 'comune', 'provincia', 'nazione'];

        campi.forEach(campo => {
            const fatt = document.querySelector(`[name="${campo}_fatturazione"]`);
            const sped = document.querySelector(`[name="${campo}_spedizione"]`);
            if (fatt && sped) {
                if (copia) {
                    sped.value = fatt.value;
                    sped.readOnly = true;
                } else {
                    sped.readOnly = false;
                }
            }
        });
    });

    // Copia all'avvio se checkbox attiva
    document.getElementById('copiaIndirizzo').dispatchEvent(new Event('change'));
    </script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const denominazione = document.getElementById('denominazione');
    const nome = document.getElementById('nome');
    const cognome = document.getElementById('cognome');
    const form = document.querySelector('form');

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

    form.addEventListener('submit', function (e) {
        const den = denominazione.value.trim();
        const nom = nome.value.trim();
        const cog = cognome.value.trim();

        if (den === '' && (nom === '' || cog === '')) {
            e.preventDefault();
            alert('Compila la denominazione oppure nome + cognome');
        }
    });

    aggiornaCampi();
});
</script>


@endsection
