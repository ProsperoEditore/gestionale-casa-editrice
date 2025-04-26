<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Report - {{ $report->libro->titolo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        .royalties { background-color: #fff3cd; }
        .totali-row { background-color: #fff3cd; font-weight: bold; }
        .intestazione { margin-bottom: 20px; }
        .intestazione h2 { margin: 0; padding: 0; }
    </style>
</head>
<body>

    <div class="intestazione">
        <h2>Report di vendita</h2>
        <p><strong>Titolo:</strong> {{ $report->libro->titolo }}</p>
        @if(!empty($report->periodo))
            <p><strong>Periodo:</strong> {{ $report->periodo }}</p>
        @endif
    </div>

    <div>
        <h4>Percentuali royalties previste da contratto:</h4>
        <ul>
            <li>Vendite dirette: <strong>{{ $percentuali['diretta'] }}%</strong></li>
            <li>Vendite indirette: <strong>{{ $percentuali['indiretta'] }}%</strong></li>
            <li>Eventi: <strong>{{ $percentuali['evento'] }}%</strong></li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th>Periodo</th>
                <th>Luogo</th>
                <th>Q.tà</th>
                <th>Prezzo unitario</th>
                <th>Valore lordo</th>
                <th>Canale</th>
                <th class="royalties">Royalties</th>
            </tr>
        </thead>
        <tbody>
        @foreach($dettagli as $riga)
            <tr>
                <td>{{ $riga->periodo_testo }}</td>
                <td>{{ $riga->luogo }}</td>
                <td>{{ $riga->quantita }}</td>
                <td>€ {{ number_format($riga->prezzo_unitario, 2, ',', '.') }}</td>
                <td>€ {{ number_format($riga->valore_lordo, 2, ',', '.') }}</td>
                <td>{{ $riga->canale }}</td>
                <td class="royalties">€ {{ number_format($riga->royalties, 2, ',', '.') }}</td>
            </tr>
        @endforeach

        @if($dettagli->isEmpty())
            <tr>
                <td colspan="7">Nessun dato disponibile per il report.</td>
            </tr>
        @endif
        </tbody>
        <tfoot>
            <tr class="totali-row">
                <td colspan="2">TOTALE</td>
                <td>{{ $totali['quantita'] }}</td>
                <td colspan="3"></td>
                <td class="royalties">€ {{ number_format($totali['royalties'], 2, ',', '.') }}</td>
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
