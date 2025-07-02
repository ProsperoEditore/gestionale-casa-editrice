@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Modifica Contratto</h3>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('contratti.update', $contratto->id) }}" method="POST">
                @include('contratti._form')
            </form>
        </div>
    </div>
</div>
@endsection
