@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Profilo fiscale e legale</h2>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('profilo.store') }}">
        @csrf

        @include('profilo.partials.fatturazione')
        @include('profilo.partials.sedi')
        @include('profilo.partials.contatti')
        @include('profilo.partials.rea')
        @include('profilo.partials.rappresentante')

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success">Salva Profilo</button>
        </div>
    </form>
</div>
@endsection