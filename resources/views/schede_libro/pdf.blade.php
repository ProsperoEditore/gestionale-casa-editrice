<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>{{ $scheda->libro->titolo }}</title>
    <style>
        @page { margin: 40px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            line-height: 1.5;
        }
        .logo {
            height: 60px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .titolo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .strillo {
            font-size: 14px;
            color: #b30000;
            font-style: italic;
            margin-bottom: 15px;
        }
        .row {
            display: flex;
            gap: 30px;
        }
        .col-left {
            width: 35%;
        }
        .col-right {
            width: 65%;
        }
        .copertina {
            width: 100%;
            border: 1px solid #ccc;
        }
        .barcode {
            text-align: center;
            margin-top: 20px;
        }
        .barcode p {
            font-size: 10px;
            margin-top: 5px;
        }
        .box {
            background: #f8f8f8;
            border: 1px solid #ccc;
            padding: 10px 12px;
            margin-top: 15px;
        }
        .box p {
            margin: 4px 0;
        }
        h2 {
            font-size: 14px;
            border-bottom: 1px solid #aaa;
            margin-top: 25px;
            margin-bottom: 5px;
        }
        .sezione {
            margin-top: 15px;
        }
    </style>
</head>
<body>

    {{-- Header con logo e marchio --}}
    <div class="header">
        @php
            $marchio = strtolower($scheda->libro->marchio_editoriale ?? 'prospero');
            $logoPath = public_path("images/marchi/logo-$marchio.png");
        @endphp
        @if (file_exists($logoPath))
            <img src="{{ $logoPath }}" class="logo" alt="Logo marchio">
        @endif
    </div>

    {{-- Titolo e strillo --}}
    <div class="titolo">{{ $scheda->libro->titolo }}</div>
    @if ($scheda->strillo)
        <div class="strillo">«{{ $scheda->strillo }}»</div>
    @endif

    {{-- Corpo principale --}}
    <div class="row">
        <div class="col-left">
            @if ($scheda->copertina_path)
                <img src="{{ public_path('storage/' . $scheda->copertina_path) }}" class="copertina" alt="Copertina">
            @endif

            @if ($scheda->libro->isbn)
                <div class="barcode">
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($scheda->libro->isbn, 'EAN13') }}" alt="Codice a barre ISBN">
                    <p>ISBN {{ $scheda->libro->isbn }}</p>
                </div>
            @endif
        </div>

        <div class="col-right">
            <h2>Sinossi</h2>
            <p>{!! nl2br(e($scheda->sinossi)) !!}</p>

            {{-- Dettagli tecnici --}}
            <div class="box">
                <p><strong>Prezzo:</strong> €{{ number_format($scheda->libro->prezzo, 2, ',', '.') }}</p>
                <p><strong>Data pubblicazione:</strong> {{ optional($scheda->libro->data_pubblicazione)->format('d/m/Y') }}</p>
                <p><strong>Marchio editoriale:</strong> {{ $scheda->libro->marchio_editoriale }}</p>
                <p><strong>Collana:</strong> {{ $scheda->libro->collana ?? '-' }}</p>
                <p><strong>Formato:</strong> {{ $scheda->formato ?? '-' }}</p>
                <p><strong>Pagine:</strong> {{ $scheda->numero_pagine ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Altre sezioni facoltative --}}
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

    {{-- Copertina stesa --}}
    @if ($scheda->copertina_stesa_path)
        <div class="sezione">
            <h2>Copertina completa</h2>
            <img src="{{ public_path('storage/' . $scheda->copertina_stesa_path) }}" alt="Copertina stesa" style="width: 100%; border: 1px solid #ccc;">
        </div>
    @endif

</body>
</html>
