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
        .header .logo {
            width: 120px;
        }
        .row {
            display: flex;
            gap: 40px;
            margin-top: 20px;
        }
        .col {
            flex: 1;
        }
        .titolo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .dati {
            font-size: 13px;
            color: #555;
        }
        .copertina {
            max-width: 100%;
            max-height: 320px;
            border: 1px solid #ccc;
        }
        .sezione {
            margin-top: 25px;
        }
        .sezione h2 {
            font-size: 16px;
            color: #333;
            border-bottom: 1px solid #aaa;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .barcode {
            margin-top: 30px;
            text-align: center;
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

    {{-- HEADER CON LOGO --}}
    <div class="header">
        @php
            $marchio = strtolower($scheda->libro->marchio_editoriale ?? 'prospero');
            $logoPath = public_path("images/marchi/logo-$marchio.png");
        @endphp
        @if (file_exists($logoPath))
            <img src="{{ $logoPath }}" class="logo" alt="Logo marchio">
        @endif
    </div>

    {{-- RIGA: COPERTINA + DATI --}}
    <div class="row">
        {{-- COLONNA COPERTINA --}}
        <div class="col">
            @if ($scheda->copertina_path)
                <img src="{{ public_path('storage/' . $scheda->copertina_path) }}" alt="Copertina" class="copertina">
            @endif
        </div>

        {{-- COLONNA DATI --}}
        <div class="col">
            <div class="titolo">{{ $scheda->libro->titolo }}</div>
            <div class="dati">
                <p><strong>ISBN:</strong> {{ $scheda->libro->isbn }}</p>
                <p><strong>Prezzo:</strong> €{{ number_format($scheda->libro->prezzo, 2, ',', '.') }}</p>
                <p><strong>Data pubblicazione:</strong> {{ \Carbon\Carbon::parse($scheda->libro->data_pubblicazione)->format('d/m/Y') }}</p>
                <p><strong>Marchio:</strong> {{ $scheda->libro->marchio_editoriale ?? '-' }}</p>
                <p><strong>Collana:</strong> {{ $scheda->libro->collana ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- SEZIONI TESTUALI --}}
    @if ($scheda->strillo)
        <div class="sezione">
            <h2>Strillo</h2>
            <p>{{ $scheda->strillo }}</p>
        </div>
    @endif

    @if ($scheda->descrizione_breve)
        <div class="sezione">
            <h2>Descrizione breve</h2>
            <p>{{ $scheda->descrizione_breve }}</p>
        </div>
    @endif

    @if ($scheda->sinossi)
        <div class="sezione">
            <h2>Sinossi</h2>
            <p>{{ $scheda->sinossi }}</p>
        </div>
    @endif

    @if ($scheda->biografia_autore)
        <div class="sezione">
            <h2>Biografia autore</h2>
            <p>{{ $scheda->biografia_autore }}</p>
        </div>
    @endif

    @if ($scheda->extra)
        <div class="sezione">
            <h2>Extra</h2>
            <p>{{ $scheda->extra }}</p>
        </div>
    @endif

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

    {{-- COPERTINA STESA (SE PRESENTE) --}}
    @if ($scheda->copertina_stesa_path)
        <div class="copertina-stesa">
            <h2>Copertina completa</h2>
            <img src="{{ public_path('storage/' . $scheda->copertina_stesa_path) }}" alt="Copertina stesa">
        </div>
    @endif

</body>
</html>
