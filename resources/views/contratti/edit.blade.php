@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Modifica Contratto</h3>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('contratti.update', $contratto->id) }}" method="POST">
                @include('contratti._form')

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Aggiorna</button>
                    <a href="{{ route('contratti.index') }}" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
