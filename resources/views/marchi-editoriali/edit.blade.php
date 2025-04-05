@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Modifica Marchi-editoriali</h3>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('marchi-editoriali.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3"><label class="form-label">Nome</label><input type="text" name="nome" value="{{ $item->nome }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Logo (URL)</label><input type="text" name="logo" value="{{ $item->logo }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Sito Web</label><input type="url" name="sito_web" value="{{ $item->sito_web }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Indirizzo Sede Legale</label><input type="text" name="indirizzo_sede_legale" value="{{ $item->indirizzo_sede_legale }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Partita IVA</label><input type="text" name="partita_iva" value="{{ $item->partita_iva }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Codice Univoco</label><input type="text" name="codice_univoco" value="{{ $item->codice_univoco }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">IBAN</label><input type="text" name="iban" value="{{ $item->iban }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Indirizzo Sede Logistica</label><input type="text" name="indirizzo_sede_logistica" value="{{ $item->indirizzo_sede_logistica }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Telefono</label><input type="text" name="telefono" value="{{ $item->telefono }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ $item->email }}" class="form-control" required></div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">Aggiorna</button>
                        <a href="{{ route('marchi-editoriali.index') }}" class="btn btn-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection