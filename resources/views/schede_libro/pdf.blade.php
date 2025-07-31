<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Scheda Libro</title>
    <style>
        @page { margin: 40px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            line-height: 1.6;
        }
        .logo {
            height: 60px;
            margin-bottom: 10px;
        }
        .header {
            text-align: left;
            margin-bottom: 10px;
        }
        .titolo {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .strillo {
            font-size: 14px;
            color: #d40000;
            font-style: italic;
            margin-bottom: 15px;
        }
        .row {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
        }
        .col {
            flex: 1;
        }
        .copertina {
            max-width: 100%;
            max-height: 300px;
            border: 1px solid #ccc;
        }
        .meta p {
            margin: 2px 0;
        }
        .sezione {
            margin-top: 18px;
        }
        .sezione h2 {
            font-size: 13px;
            margin-bottom: 5px;
            border-bottom: 1px solid #aaa;
            padding-bottom: 2px;
        }
        .barcode {
            text-align: center;
            margin-top: 30px;
        }
        .barcode p {
            font-size: 10px;
            margin-top: 3px;
        }
        .copertina-stesa {
            text-align: center;
            margin-top: 40px;
        }
        .copertina-stesa img {
            max-width: 100%;
            border: 1px solid #ccc;
        }
        .dati-tecnici {
            margin-top: 10px;
        }
        .dati-tecnici table {
            width: 100%;
            border-collapse: collapse;
        }
        .dati-tecnici td {
            padding: 4px 6px;
        }
        .dati-tecnici tr:nth-child(odd) {
            background-color: #f9f9f9;
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
        <div class="header">
            <img src="{{ $logoPath }}" class="logo" alt="Logo marchio">
        </div>
    @endif

    {{-- TITOLO + STRILLO --}}
    <div class="titolo">{{ $scheda->libro->titolo }}</div>
    @if ($scheda->strillo)
        <div class="strillo">«{{ $scheda->strillo }}»</div>
    @endif

    {{-- COPERTINA + DATI --}}
    <div class="row">
        <div class="col" style="max-width: 220px;">
            @if ($scheda->copertina_path)
                <img src="{{ public_path('storage/' . $scheda->copertina_path) }}" alt="Copertina" class="copertina">
            @endif
        </div>
        <div class="col">
            <h2>Sinossi</h2>
            <p>{{ $scheda->sinossi }}</p>
        </div>
    </div>

    {{-- SEZIONI EXTRA --}}
    @foreach ([
        'Descrizione breve' => $scheda->descrizione_breve,
        'Biografia autore' => $scheda->biografia_autore,
        'Extra' => $scheda->extra,
    ] as $titolo => $testo)
        @if ($testo)
            <div class="sezione">
                <h2>{{ $titolo }}</h2>
                <p>{!! nl2br(e($testo)) !!}</p>
            </div>
        @endif
    @endforeach

    {{-- DETTAGLI TECNICI --}}
    <div class="sezione dati-tecnici">
        <h2>Dettagli tecnici</h2>
        <table>
            <tr>
                <td><strong>ISBN:</strong></td>
                <td>{{ $scheda->libro->isbn }}</td>
            </tr>
            <tr>
                <td><strong>Prezzo:</strong></td>
                <td>€{{ number_format($scheda->libro->prezzo, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Data pubblicazione:</strong></td>
                <td>{{ optional($scheda->libro->data_pubblicazione)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td><strong>Marchio:</strong></td>
                <td>{{ $scheda->libro->marchio_editoriale }}</td>
            </tr>
            <tr>
                <td><strong>Collana:</strong></td>
                <td>{{ $scheda->libro->collana }}</td>
            </tr>
            <tr>
                <td><strong>Formato:</strong></td>
                <td>{{ $scheda->formato ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Numero pagine:</strong></td>
                <td>{{ $scheda->numero_pagine ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- CODICE A BARRE --}}
    @if ($scheda->libro->isbn)
        <div class="barcode">
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($scheda->libro->isbn, 'EAN13') }}" alt="Codice a barre ISBN">
            <p>ISBN: {{ $scheda->libro->isbn }}</p>
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
