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
                            <option value="grossista">Grossista</option>
                            <option value="distributore">Distributore</option>
                            <option value="fiere">Fiere</option>
                            <option value="festival">Festival</option>
                            <option value="altro">Altro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Indirizzo di Fatturazione</label>
                        <input type="text" name="indirizzo_fatturazione" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Indirizzo di Spedizione</label>
                        <input type="text" name="indirizzo_spedizione" class="form-control">
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


@endsection
