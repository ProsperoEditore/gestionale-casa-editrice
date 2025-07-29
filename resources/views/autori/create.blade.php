@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Aggiungi Autore</h3>
    <form action="{{ route('autori.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('autori.form')
    </form>
</div>
@endsection
