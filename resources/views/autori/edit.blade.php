@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Modifica Autore</h3>
    <form action="{{ route('autori.update', $autore) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('autori.form')
    </form>
</div>
@endsection
