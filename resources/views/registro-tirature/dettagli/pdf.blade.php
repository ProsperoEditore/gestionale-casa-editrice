<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registro Tirature PDF</title>
    <style>
        @page {
            margin: 120px 30px 60px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }

        /* Intestazione */
        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 60px;
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
        }

        .header-left {
            float: left;
            width: 15%;
        }

        .header-right {
            float: right;
            width: 75%;
            text-align: right;
        }

        .header-left img {
            height: 100px; 
            width: auto;
        }

        .header-right .ragione-sociale {
            font-size: 14px;
            font-weight: bold;
        }

        .header-right .periodo {
            font-size: 12px;
        }

        /* Footer */
        footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
        }

        /* Tabella */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10mm;
        }

        th, td {
            border: 1px solid #000;
            padding: 4pt 6pt;
            text-align: left;
        }

        .total-row {
            font-weight: bold;
            background-color: #fff3cd;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Totali */
        .totals-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }

    </style>
</head>
<body>
    <!-- Intestazione -->
    <header>
        <div class="header-right">
            <div class="ragione-sociale">Prospero Editore di Burgazzi Riccardo</div>
            <div class="periodo">Registro Tirature – {{ $registro->periodo }} {{ $registro->anno }}</div>
        </div>
    </header>

    <!-- Tabella dei dettagli -->
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Titolo</th>
                <th>Copie Stampate</th>
                <th>Prezzo IVA</th>
                <th>Imponibile Relativo</th>
                <th>Imponibile</th>
                <th>IVA 4%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dettagli as $dettaglio)
            <tr>
                <td>{{ \Carbon\Carbon::parse($dettaglio->data)->format('d-m-Y') }}</td>
                <td>{{ $dettaglio->titolo->titolo }}</td>
                <td>{{ $dettaglio->copie_stampate }}</td>
                <td>{{ number_format($dettaglio->prezzo_vendita_iva, 3, ',', '') }}</td>
                <td>{{ number_format($dettaglio->imponibile_relativo, 3, ',', '') }}</td>
                <td>{{ number_format($dettaglio->imponibile, 3, ',', '') }}</td>
                <td>{{ number_format($dettaglio->iva_4percento, 3, ',', '') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <th colspan="2">Totali</th>
                <th>{{ $dettagli->sum('copie_stampate') }}</th>
                <th></th>
                <th>{{ number_format($dettagli->sum('imponibile_relativo'), 3, ',', '') }}</th>
                <th>{{ number_format($dettagli->sum('imponibile'), 3, ',', '') }}</th>
                <th>{{ number_format($dettagli->sum('iva_4percento'), 3, ',', '') }}</th>
            </tr>
        </tfoot>
    </table>


<!-- Footer con numerazione delle pagine -->
<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont('Helvetica', 'normal');
        $size = 9;
        $text = "pag. {PAGE_NUM} di {PAGE_COUNT}";
        $width = $fontMetrics->getTextWidth($text, $font, $size);
        $pdf->page_text(($pdf->get_width() - $width) / 2, $pdf->get_height() - 20, $text, $font, $size);
    }
</script>





</body>
</html>
