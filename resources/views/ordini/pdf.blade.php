<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Ordine #{{ $ordine->codice }}</title>
    <style>
    body {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 12px;
        color: #222;
        margin: 40px;
        position: relative; 
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .logo-container {
    position: absolute;
    top: 0px;
    right: 0px;
}

.logo {
    height: 60px;
    width: auto;
}


    .marchio-info {
        font-size: 12px;
        line-height: 1.4;
    }

    .cliente-info {
        font-size: 12px;
        text-align: right;
        line-height: 1.5;
    }

    h2 {
        text-align: center;
        margin-top: 40px;
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    p {
        margin: 4px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th {
        background-color: #f8f8f8;
        padding: 6px;
        font-weight: bold;
        text-align: left;
        font-size: 12px;
        border-bottom: 1px solid #ccc;
    }

    td {
        padding: 6px;
        font-size: 12px;
        border-bottom: 1px solid #eee;
        vertical-align: top;
    }

    .barrato {
        text-decoration: line-through;
        color: #888;
    }

    .totale-row {
    font-weight: bold;
    background-color: #f2f2f2;
    }

    @font-face {
        font-family: 'Code128';
        src: url('{{ asset('fonts/code128.ttf') }}');
    }

    .barcode {
        font-family: 'Code128';
        font-size: 30px; /* Modifica la dimensione in base alle tue necessità */
        margin-right: 10px;
        color: black
    }

    .barrato td {
    text-decoration: line-through;
    color: #888;
    }

    .verde td {
        color: green;
        font-weight: normal;
    }

    .rosso td {
        color: red;
        font-weight: normal;
    }



</style>


</head>
<body>

@php
    $logo = 'logo-prospero.png'; // Default di sicurezza

    $marchiPresenti = $ordine->libri->pluck('marchio_editoriale_id')->unique();

    if ($marchiPresenti->contains(1)) {
        // Se tra i libri c'è almeno un libro di Prospero, usa sempre il logo di Prospero
        $logo = 'logo-prospero.png';
    } elseif ($marchiPresenti->count() === 1) {
        // Se c'è solo un marchio presente e non è Prospero
        $marchioId = $marchiPresenti->first();
        if ($marchioId == 2) {
            $logo = 'logo-calibano.png';
        } elseif ($marchioId == 3) {
            $logo = 'logo-miranda.png';
        }
    }
@endphp

    {{-- Header: Colonna sinistra dati marchio --}}
    <div class="header">
    <div style="flex: 1;">
        <div class="marchio-info">
            <strong>{{ $marchio->nome }}</strong><br>
            Indirizzo legale: {{ $marchio->indirizzo_sede_legale }}<br>
            Indirizzo logistica: {{ $marchio->indirizzo_sede_logistica }}<br>
            P.IVA: {{ $marchio->partita_iva }}<br>
            Codice Univoco: {{ $marchio->codice_univoco }}<br>
            IBAN: {{ $marchio->iban }}<br>
            Tel: {{ $marchio->telefono }}<br>
            Email: {{ $marchio->email }}<br>
            Sito: {{ $marchio->sito_web }}
        </div>
    </div>

    <div class="logo-container">
        <img src="{{ asset('images/' . $logo) }}" class="logo" alt="Logo Marchio">
    </div>

        <div class="cliente-info">
            <h4>{{ $ordine->anagrafica->nome_completo }}</h4>
            @if(!empty($ordine->anagrafica->indirizzo_fatturazione))
                <strong>Indirizzo Fatturazione:</strong> {{ $ordine->anagrafica->indirizzo_fatturazione }}<br>
            @endif
            @if(!empty($ordine->anagrafica->indirizzo_spedizione))
                <strong>Indirizzo Spedizione:</strong> {{ $ordine->anagrafica->indirizzo_spedizione }}<br>
            @endif
            @if(!empty($ordine->anagrafica->partita_iva))
                <strong>Partita IVA:</strong> {{ $ordine->anagrafica->partita_iva }}<br>
            @endif
            @if(!empty($ordine->anagrafica->codice_fiscale))
                <strong>Codice Fiscale:</strong> {{ $ordine->anagrafica->codice_fiscale }}<br>
            @endif
            @if(!empty($ordine->anagrafica->codice_univoco))
                <strong>Codice Univoco:</strong> {{ $ordine->anagrafica->codice_univoco }}<br>
            @endif
            @if(!empty($ordine->anagrafica->email))
                <strong>Email:</strong> {{ $ordine->anagrafica->email }}<br>
            @endif
            @if(!empty($ordine->anagrafica->pec))
                <strong>PEC:</strong> {{ $ordine->anagrafica->pec }}<br>
            @endif
            @if(!empty($ordine->anagrafica->telefono))
                <strong>Telefono:</strong> {{ $ordine->anagrafica->telefono }}<br>
            @endif
        </div>
    </div>
</div>

    <h2 style="margin-top: 40px;">Dettagli Ordine #{{ $ordine->codice }}</h2>
    <p><strong>Tipo ordine:</strong> {{ ucfirst($ordine->tipo_ordine) }}</p>
    <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($ordine->data)->format('d-m-Y') }}</p>
        @if($ordine->tipo_ordine === 'acquisto')
        <p><em style="color: #444;">
            Copia di cortesia. Documento non valido ai fini fiscali. Seguirà fattura elettronica.
        </em></p>
    @endif




    {{-- Se tipo = Conto Deposito, mostra causale e condizioni se compilati --}}
    @if($ordine->tipo_ordine === 'conto deposito')
        @if(!empty($ordine->causale))
            <p><strong>Causale:</strong> {{ $ordine->causale }}</p>
        @endif
        @if(!empty($ordine->condizioni_conto_deposito))
            <p><strong>Condizioni conto deposito:</strong><br>
                {!! nl2br(e($ordine->condizioni_conto_deposito)) !!}
            </p>
        @endif
    @endif

    @php
    $totale_quantita = 0;
    $totale_lordo = 0;
    $totale_netto = 0;

    foreach($ordine->libri as $libro) {
        $totale_quantita += $libro->pivot->quantita;
        $totale_lordo += $libro->pivot->valore_vendita_lordo;
        $totale_netto += $libro->pivot->netto_a_pagare;
    }
    @endphp

    <table>
        <thead>
            <tr>
                <th>ISBN</th>
                <th>Titolo</th>
                <th>Quantità</th>
                <th>Prezzo Copertina</th>
                <th>Prezzo Lordo</th>
            @if(!in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
                <th>Sconto (%)</th>
                <th>Valore Scontato</th>
            @endif
                <th>Info</th>
            </tr>
        </thead>
            <tbody>
                @foreach($ordine->libri as $libro)
                    <tr class="
                        @if($libro->pivot->info_spedizione === 'fuori catalogo') barrato
                        @elseif(in_array($libro->pivot->info_spedizione, ['spedito da tipografia', 'consegna a mano', 'spedito da magazzino terzo'])) verde
                        @elseif($libro->pivot->info_spedizione === 'momentaneamente non disponibile') rosso
                        @endif
                    ">
                <td><span class="barcode">{{ $libro->isbn }}</span>
                <br>
                {{ $libro->isbn }}
                </td>
                <td>{{ $libro->titolo }}</td>
                <td>{{ $libro->pivot->quantita }}</td>
                <td>{{ number_format($libro->pivot->prezzo_copertina, 2) }} €</td>
                <td>
                    <del>{{ number_format($libro->pivot->valore_vendita_lordo, 2) }} €</del>
                </td>
                @if(!in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
                    <td>{{ number_format($libro->pivot->sconto, 2) }}%</td>
                    <td>{{ number_format($libro->pivot->netto_a_pagare, 2) }} €</td>
                @endif
                <td>{{ $libro->pivot->info_spedizione }}</td>
                </tr>
            @endforeach


            <tr class="totale-row">
                <td><strong>Totali</strong></td>
                <td>{{ $ordine->libri->count() }}</td>
                <td>{{ $totale_quantita }}</td>
                <td></td>
                <td>{{ number_format($totale_lordo, 2) }} €</td>
                @if(!in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
                    <td></td>
                    <td>{{ number_format($totale_netto, 2) }} €</td>
                @endif
                <td></td>
            </tr>

        </tbody>
    </table>

    {{-- Totale e metodi di pagamento solo per Acquisto --}}
    @if(in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
    <div style="margin: 20px 0; text-align: center;">
        <p style="font-size: 16px; font-weight: bold; margin: 10px 0;">
            Totale a pagare: {{ number_format($ordine->totale_netto_compilato, 2) }} €
        </p>
    </div>

    @if($ordine->pagato)
    <div style="margin: 10px 0; text-align: center;">
        <p style="font-size: 14px; color: red; font-weight: bold; margin-top: 5px;">
            PAGATO: {{ $ordine->pagato }}
        </p>
    </div>
    @endif

    @if(!empty($ordine->specifiche_iva))
        <p><strong>Specifiche IVA:</strong> {{ $ordine->specifiche_iva }}</p>
    @endif

    @if(!empty($ordine->costo_spedizione))
        <p><strong>Costo spedizione:</strong> {{ $ordine->costo_spedizione }}</p>
    @endif

    @if(!empty($ordine->altre_specifiche_iva))
        <p><strong>Altre specifiche:</strong> {{ $ordine->altre_specifiche_iva }}</p>
    @endif

    @if(!empty($ordine->tempi_pagamento))
        <p><strong>Tempi di pagamento:</strong> {{ $ordine->tempi_pagamento }}</p>
    @endif

    @if(!empty($ordine->modalita_pagamento))
        <p><strong>Modalità di pagamento</strong><br>
            {!! nl2br(e($ordine->modalita_pagamento)) !!}
        </p>
    @endif
@endif



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
