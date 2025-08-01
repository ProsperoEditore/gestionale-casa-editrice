@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">Elenco Autori</h3>

    <a href="{{ route('autori.create') }}" class="btn btn-success mb-3">Aggiungi Autore</a>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Libri</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($autori as $a)
                <tr>
                    {{-- NOME --}}
                    <td class="text-start">
                        @if($a->pseudonimo)
                            <strong>{{ $a->pseudonimo }}</strong>
                        @elseif($a->denominazione)
                            <strong>{{ $a->denominazione }}</strong>
                        @else
                            {{ $a->nome }} {{ $a->cognome }}
                        @endif
                    </td>

                    {{-- LIBRI --}}
                    <td class="text-start">
                        @if($a->libri->isEmpty())
                            <em class="text-muted">Nessun libro</em>
                        @else
                            <ul class="mb-0 ps-3">
                        @foreach($a->libri as $libro)
                            <li>
                                <strong>{{ $libro->titolo }}</strong><br>
                                <small class="text-muted">
                                    ISBN: {{ $libro->isbn }} |
                                    Prezzo: € {{ number_format($libro->prezzo, 2, ',', '.') }}
                                </small>
                            </li>
                        @endforeach

                            </ul>
                        @endif
                    </td>

                    {{-- AZIONI --}}
                    <td>
                        <a href="{{ route('autori.edit', $a) }}" class="btn btn-warning btn-sm">Modifica</a>
                        <form action="{{ route('autori.destroy', $a) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Eliminare questo autore?')">Elimina</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
