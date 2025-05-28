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
        <img src="{{ public_path('images/logo-prospero.png') }}" class="logo" alt="Logo">
        <h2>Registro Vendite</h2>
    </div>

    <div class="dati">
        <p><strong>Anagrafica:</strong> {{ $registro->anagrafica->nome }}</p>
        <p><strong>Canale di Vendita:</strong> {{ $registro->canale_vendita }}</p>
        <p><strong>Periodo:</strong> {{ $registro->created_at->format('d/m/Y') }}</p>
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

            @foreach($registro->dettagli as $riga)
                @php
                    $quantita = $riga->quantita ?? 0;
                    $valore = $quantita * ($riga->prezzo ?? 0);
                    $totaleQuantita += $quantita;
                    $totaleValore += $valore;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($riga->data)->format('d/m/Y') }}</td>
                    <td>{{ $riga->isbn }}</td>
                    <td style="text-align: left;">{{ $riga->titolo }}</td>
                    <td>{{ $quantita }}</td>
                    <td>{{ number_format($riga->prezzo, 2, ',', '.') }} €</td>
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

    {{-- FOOTER --}}
    <div class="footer">
        Prospero Editore di Burgazzi Riccardo – Registro generato il {{ now()->format('d/m/Y') }}
    </div>

</body>
</html>
