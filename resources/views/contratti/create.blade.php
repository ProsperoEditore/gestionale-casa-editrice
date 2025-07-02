@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Crea Nuovo Contratto</h3>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('contratti.store') }}" method="POST">
                @include('contratti._form', ['contratto' => null])

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Salva</button>
                    <a href="{{ route('contratti.index') }}" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
