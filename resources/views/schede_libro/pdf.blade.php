<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Scheda Libro</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 50px;
            font-size: 13px;
            color: #222;
            line-height: 1.6;
        }
        .logo {
            height: 60px;
            margin-bottom: 20px;
        }
        .row {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        .col {
            flex: 1;
        }
        .copertina {
            max-width: 100%;
            max-height: 340px;
            border: 1px solid #ccc;
        }
        .titolo {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .meta p {
            margin: 2px 0;
        }
        .sezione {
            margin-top: 25px;
        }
        .sezione h2 {
            font-size: 14px;
            margin-bottom: 5px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }
        .barcode {
            text-align: center;
            margin-top: 40px;
        }
        .copertina-stesa {
            text-align: center;
            margin-top: 40px;
        }
        .copertina-stesa img {
            max-width: 100%;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

    {{-- LOGO MARCHIO EDITORIALE --}}
    @php
        $marchio = strtolower($scheda->libro->marchio_editoriale ?? 'prospero');
        $logoPath = public_path("images/marchi/logo-$marchio.png");
    @endphp
    @if (file_exists($logoPath))
        <img src="{{ $logoPath }}" class="logo" alt="Logo marchio">
    @endif

    {{-- COPERTINA + DATI --}}
    <div class="row">
        <div class="col">
            @if ($scheda->copertina_path)
                <img src="{{ public_path('storage/' . $scheda->copertina_path) }}" alt="Copertina" class="copertina">
            @endif
        </div>
        <div class="col">
            <div class="titolo">{{ $scheda->libro->titolo }}</div>
            <div class="meta">
                <p><strong>ISBN:</strong> {{ $scheda->libro->isbn }}</p>
                <p><strong>Prezzo:</strong> €{{ number_format($scheda->libro->prezzo, 2, ',', '.') }}</p>
                <p><strong>Data pubblicazione:</strong> {{ optional($scheda->libro->data_pubblicazione)->format('d/m/Y') }}</p>
                <p><strong>Marchio:</strong> {{ $scheda->libro->marchio_editoriale }}</p>
                <p><strong>Collana:</strong> {{ $scheda->libro->collana }}</p>
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
