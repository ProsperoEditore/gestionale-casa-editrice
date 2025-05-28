<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .logo {
            width: 140px;
        }

        .intestazione {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .intestazione h2 {
            font-size: 20px;
            margin: 0;
        }

        .dati {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .totale {
            background-color: #f9f48f;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>

    {{-- INTESTAZIONE --}}
    <div class="intestazione">
        <h2>Registro Vendite</h2>
    </div>

    <div class="dati">
        <p><strong>Anagrafica:</strong> {{ $registro->anagrafica->nome }}</p>

        @if($filtro_date['da'] || $filtro_date['a'])
            <p><strong>Periodo:</strong>
                @if($filtro_date['da']) dal {{ \Carbon\Carbon::parse($filtro_date['da'])->format('d/m/Y') }} @endif
                @if($filtro_date['a']) al {{ \Carbon\Carbon::parse($filtro_date['a'])->format('d/m/Y') }} @endif
            </p>
        @endif
    </div>

    {{-- TABELLA DETTAGLI --}}
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>ISBN</th>
                <th>Titolo</th>
                <th>Quantità</th>
                <th>Prezzo</th>
                <th>Valore</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totaleQuantita = 0;
                $totaleValore = 0;
            @endphp

            @foreach($dettagli as $riga)
                @php
                    $quantita = $riga->quantita ?? 0;
                    $prezzo = $riga->prezzo ?? 0;
                    $valore = $quantita * $prezzo;
                    $totaleQuantita += $quantita;
                    $totaleValore += $valore;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($riga->data)->format('d/m/Y') }}</td>
                    <td>{{ $riga->isbn }}</td>
                    <td style="text-align: left;">{{ $riga->titolo }}</td>
                    <td>{{ $quantita }}</td>
                    <td>{{ number_format($prezzo, 2, ',', '.') }} €</td>
                    <td>{{ number_format($valore, 2, ',', '.') }} €</td>
                </tr>
            @endforeach

            <tr class="totale">
                <td colspan="3">Totale</td>
                <td>{{ $totaleQuantita }}</td>
                <td></td>
                <td>{{ number_format($totaleValore, 2, ',', '.') }} €</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Registro Vendite generato il {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>

    {{-- NUMERAZIONE PAGINE --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans", "normal");
            $size = 10;
            $pageText = "pag. {PAGE_NUM} di {PAGE_COUNT}";
            $pdf->page_text(270, 570, $pageText, $font, $size);
        }
    </script>

</body>
</html>
