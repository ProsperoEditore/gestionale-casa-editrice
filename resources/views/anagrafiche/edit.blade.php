@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Modifica Anagrafica</h3>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('anagrafiche.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select name="categoria" class="form-control" required>
                            @foreach(['magazzino editore','sito','libreria c.e.','libreria cliente','privato','biblioteca','associazione','università','grossista','distributore','fiere','festival','altro'] as $cat)
                                <option value="{{ $cat }}" {{ $item->categoria == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Denominazione</label>
                            <input type="text" name="denominazione" id="denominazione" class="form-control" value="{{ $item->denominazione }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" id="nome" class="form-control" value="{{ $item->nome }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Cognome</label>
                            <input type="text" name="cognome" id="cognome" class="form-control" value="{{ $item->cognome }}">
                        </div>
                    </div>

                    <h5 class="mt-4">Indirizzo di Fatturazione</h5>
                    <div class="row">
                        @foreach(['via','civico','cap','comune','provincia','nazione'] as $field)
                            <div class="col-md-{{ $field == 'via' || $field == 'comune' ? 6 : 3 }} mb-3">
                                <label class="form-label">{{ ucfirst($field) }}</label>
                                <input type="text" name="{{ $field }}_fatturazione" class="form-control" value="{{ $item[$field.'_fatturazione'] }}">
                            </div>
                        @endforeach
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" class="form-check-input" id="copiaIndirizzo" checked>
                        <label class="form-check-label" for="copiaIndirizzo">
                            L'indirizzo di spedizione è uguale a quello di fatturazione
                        </label>
                    </div>

                    <h5 class="mt-4">Indirizzo di Spedizione</h5>
                    <div class="row">
                        @foreach(['via','civico','cap','comune','provincia','nazione'] as $field)
                            <div class="col-md-{{ $field == 'via' || $field == 'comune' ? 6 : 3 }} mb-3">
                                <label class="form-label">{{ ucfirst($field) }}</label>
                                <input type="text" name="{{ $field }}_spedizione" class="form-control" value="{{ $item[$field.'_spedizione'] }}">
                            </div>
                        @endforeach
                    </div>

                    @foreach(['partita_iva','codice_fiscale','email','telefono','pec','codice_univoco'] as $field)
                        <div class="mb-3">
                            <label class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                            <input type="text" name="{{ $field }}" id="{{ $field }}" class="form-control" value="{{ $item[$field] }}">
                        </div>
                    @endforeach

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
                if (denominazione.value.trim() !== '') {
                    nome.disabled = true;
                    cognome.disabled = true;
                } else {
                    nome.disabled = false;
                    cognome.disabled = false;
                }

                if (nome.value.trim() !== '' || cognome.value.trim() !== '') {
                    denominazione.disabled = true;
                } else {
                    denominazione.disabled = false;
                }
            }

            [denominazione, nome, cognome].forEach(i => i.addEventListener('input', aggiornaCampi));
            aggiornaCampi();

            document.getElementById('copiaIndirizzo').addEventListener('change', function () {
                const copia = this.checked;
                ['via','civico','cap','comune','provincia','nazione'].forEach(function (campo) {
                    const fatt = document.querySelector(`[name="${campo}_fatturazione"]`);
                    const sped = document.querySelector(`[name="${campo}_spedizione"]`);
                    if (fatt && sped) {
                        sped.readOnly = copia;
                        if (copia) sped.value = fatt.value;
                    }
                });
            });

            document.getElementById('copiaIndirizzo').dispatchEvent(new Event('change'));
        });
    </script>
@endsection
