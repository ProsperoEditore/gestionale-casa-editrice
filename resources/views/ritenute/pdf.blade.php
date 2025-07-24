<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Ritenuta {{ $ritenuta->numero }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 40px;
            color: #222;
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        .logo img {
            height: 60px;
        }
        .sezione {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .firma {
            margin-top: 40px;
            text-align: right;
        }
        .marca {
            position: absolute;
            bottom: 40px;
            left: 0;
            border: 1px solid #000;
            padding: 10px;
            width: 200px;
            font-size: 11px;
            white-space: pre-line;
        }
        .pagamenti {
            position: absolute;
            bottom: 40px;
            right: 0;
            font-size: 11px;
            color: red;
        }
    </style>
</head>
<body>
    <h3 style="text-align:center; margin-bottom:5px;">NOTA PER LA CESSIONE DI DIRITTI D’AUTORE</h3>
    <p style="text-align:center; font-style:italic;">La presente Cessione Diritti d’Autore è regolata dalle normative di leggi vigenti sul Diritto d’Autore</p>

    <div class="sezione">
        <strong>SOGGETTO PERCIPIENTE</strong><br>
        {{ $ritenuta->nome_autore }} {{ $ritenuta->cognome_autore }}<br>
        nato/a a {{ $ritenuta->luogo_nascita }} il {{ $ritenuta->data_nascita->format('d/m/Y') }}<br>
        {{ $ritenuta->indirizzo }}<br>
        {{ $ritenuta->codice_fiscale }}<br>
        @if($ritenuta->iban) IBAN: {{ $ritenuta->iban }}<br>@endif
    </div>

    <div class="sezione" style="text-align:right">
        <strong>SOGGETTO EROGANTE</strong><br>
        PROSPERO EDITORE di Burgazzi Riccardo<br>
        Via della Stampa 25 – 20026, Novate Milanese (MI)<br>
        C.F.: BRGRCR88B13F205Z / P. IVA: 08148530960
    </div>

    <p>Nota numero: {{ $ritenuta->numero }} &nbsp;&nbsp;&nbsp;&nbsp;{{ $ritenuta->data_emissione->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Descrizione prestazione</th>
                <th>Importo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ritenuta->prestazioni as $p)
                <tr>
                    <td>{{ $p['descrizione'] }}</td>
                    <td>€ {{ number_format($p['importo'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="sezione">
        <p><strong>Totale:</strong> € {{ number_format($ritenuta->totale, 2, ',', '.') }}</p>
        <p><strong>Quota esente:</strong> € {{ number_format($ritenuta->quota_esente, 2, ',', '.') }}</p>
        <p><strong>Imponibile:</strong> € {{ number_format($ritenuta->imponibile, 2, ',', '.') }}</p>
        <p><strong>R.A. (20% su imponibile):</strong> € {{ number_format($ritenuta->ritenuta, 2, ',', '.') }}</p>
        <p><strong>Netto da pagare:</strong> € {{ number_format($ritenuta->netto_pagare, 2, ',', '.') }}</p>
        @if($ritenuta->nota_iva)
            <p>{{ $ritenuta->nota_iva }}</p>
        @endif
    </div>

    <div class="firma">
        FIRMA DEL DICHIARANTE ________________________________________
    </div>

    <div class="marca">
        marca da bollo da € 2,00
        (per importi superiori a 77,47)
    </div>

    @if($ritenuta->data_pagamento_netto || $ritenuta->data_pagamento_ritenuta)
    <div class="pagamenti">
        @if($ritenuta->data_pagamento_netto)
        Pagamento netto: {{ $ritenuta->data_pagamento_netto->format('d/m/Y') }}<br>
        @endif
        @if($ritenuta->data_pagamento_ritenuta)
        Pagamento ritenuta: {{ $ritenuta->data_pagamento_ritenuta->format('d/m/Y') }}
        @endif
    </div>
    @endif
</body>
</html>
