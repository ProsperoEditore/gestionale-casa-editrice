<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Scheda Libro</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 40px;
            color: #111;
            line-height: 1.5;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }
        .logo {
            width: 120px;
        }
        .row {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        .col {
            flex: 1;
        }
        .titolo {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .dati p {
            margin: 4px 0;
            font-size: 14px;
        }
        .copertina {
            max-width: 100%;
            max-height: 300px;
            border: 1px solid #ccc;
        }
        .sezione {
            margin-top: 25px;
        }
        .sezione h2 {
            font-size: 15px;
            color: #222;
            border-bottom: 1px solid #aaa;
            margin-bottom: 5px;
        }
        .barcode {
            text-align: center;
            margin-top: 30px;
        }
        .copertina-stesa {
            margin-top: 40px;
            text-align: center;
        }
        .copertina-stesa img {
            max-width: 100%;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

    {{-- LOGO MARCHIO + NOME --}}
    <div class="header">
        @php
            $marchio = strtolower($scheda->libro->marchio_editoriale ?? 'prospero');
            $logoPath = public_path("images/marchi/logo-$marchio.png");
        @endphp

        @if (file_exists($logoPath))
            <img src="{{ $logoPath }}" class="logo" alt="Logo marchio editoriale">
        @endif

        <div style="margin-left: 20px; font-size: 16px; font-weight: bold;">
            {{ $scheda->libro->marchio_editoriale ?? 'Prospero Editore' }}
        </div>
    </div>


    {{-- COPERTINA + DATI --}}
    <div class="row">
        <div class="col">
            @if ($scheda->copertina_path)
                <img src="{{ public_path('storage/' . $scheda->copertina_path) }}" class="copertina" alt="Copertina">
            @endif
        </div>
        <div class="col">
            <div class="titolo">{{ $scheda->libro->titolo }}</div>
            <div class="dati">
                <p><strong>ISBN:</strong> {{ $scheda->libro->isbn }}</p>
                <p><strong>Prezzo:</strong> €{{ number_format($scheda->libro->prezzo, 2, ',', '.') }}</p>
                <p><strong>Data pubblicazione:</strong> {{ optional($scheda->libro->data_pubblicazione)->format('d/m/Y') }}</p>
                <p><strong>Marchio:</strong> {{ $scheda->libro->marchio_editoriale ?? '-' }}</p>
                <p><strong>Collana:</strong> {{ $scheda->libro->collana ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- SEZIONI TESTUALI --}}
    @foreach ([
        'Strillo' => $scheda->strillo,
        'Descrizione breve' => $scheda->descrizione_breve,
        'Sinossi' => $scheda->sinossi,
        'Biografia autore' => $scheda->biografia_autore,
        'Extra' => $scheda->extra,
    ] as $titolo => $testo)
        @if ($testo)
            <div class="sezione">
                <h2>{{ $titolo }}</h2>
                <p>{{ $testo }}</p>
            </div>
        @endif
    @endforeach

    {{-- DETTAGLI TECNICI --}}
    <div class="sezione">
        <h2>Dettagli tecnici</h2>
        <p><strong>Formato:</strong> {{ $scheda->formato ?? '-' }}</p>
        <p><strong>Numero pagine:</strong> {{ $scheda->numero_pagine ?? '-' }}</p>
    </div>

    {{-- CODICE A BARRE --}}
    @if ($scheda->libro->isbn)
        <div class="barcode">
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($scheda->libro->isbn, 'EAN13') }}" alt="Codice a barre ISBN">
        </div>
    @endif

    {{-- COPERTINA STESA --}}
    @if ($scheda->copertina_stesa_path)
        <div class="copertina-stesa">
            <h2>Copertina completa</h2>
            <img src="{{ public_path('storage/' . $scheda->copertina_stesa_path) }}" alt="Copertina stesa">
        </div>
    @endif

</body>
</html>
