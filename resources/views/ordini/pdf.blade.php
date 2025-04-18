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
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .logo {
        height: 60px;
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
</style>


</head>
<body>
    {{-- Header: Logo e dati marchio + anagrafica cliente a destra --}}
    <div class="header">
        <div>
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

        <div class="cliente-info">
            <h4>Dati Cliente</h4>
            <strong>Nome:</strong> {{ $ordine->anagrafica->nome }}<br>
            <strong>Categoria:</strong> {{ $ordine->anagrafica->categoria }}<br>
            <strong>Indirizzo Fatturazione:</strong> {{ $ordine->anagrafica->indirizzo_fatturazione }}<br>
            <strong>Indirizzo Spedizione:</strong> {{ $ordine->anagrafica->indirizzo_spedizione }}<br>
            <strong>Partita IVA:</strong> {{ $ordine->anagrafica->partita_iva }}<br>
            <strong>Codice Fiscale:</strong> {{ $ordine->anagrafica->codice_fiscale }}<br>
            <strong>Codice Univoco:</strong> {{ $ordine->anagrafica->codice_univoco }}<br>
            <strong>Email:</strong> {{ $ordine->anagrafica->email }}<br>
            <strong>PEC:</strong> {{ $ordine->anagrafica->pec }}<br>
            <strong>Telefono:</strong> {{ $ordine->anagrafica->telefono }}
        </div>
    </div>

    <h2 style="margin-top: 40px;">Dettagli Ordine #{{ $ordine->codice }}</h2>
    <p><strong>Tipo ordine:</strong> {{ ucfirst($ordine->tipo_ordine) }}</p>
    <p><strong>Data:</strong> {{ $ordine->data }}</p>

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

    <table>
        <thead>
            <tr>
                <th>ISBN</th>
                <th>Titolo</th>
                <th>Quantità</th>
                <th>Prezzo Copertina</th>
                <th>Prezzo Lordo</th>
            @if($ordine->tipo_ordine !== 'acquisto')
                <th>Sconto (%)</th>
                <th>Valore Scontato</th>
            @endif
                <th>Info</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordine->libri as $libro)
                <tr>
                <td>{{ $libro->isbn }}</td>
                <td>{{ $libro->titolo }}</td>
                <td>{{ $libro->pivot->quantita }}</td>
                <td>{{ number_format($libro->pivot->prezzo_copertina, 2) }} €</td>
                <td>
                    <del>{{ number_format($libro->pivot->valore_vendita_lordo, 2) }} €</del>
                </td>
                @if($ordine->tipo_ordine !== 'acquisto')
                    <td>{{ number_format($libro->pivot->sconto, 2) }}%</td>
                    <td>{{ number_format($libro->pivot->netto_a_pagare, 2) }} €</td>
                @endif
                <td>{{ $libro->pivot->info_spedizione }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totale e metodi di pagamento solo per Acquisto --}}
    @if(in_array($ordine->tipo_ordine, ['acquisto', 'acquisto autore']))
    <div style="margin: 30px 0; text-align: center;">
        <p style="font-size: 16px; font-weight: bold; margin: 10px 0;">
            Totale a pagare: {{ number_format($ordine->totale_netto_compilato, 2) }} €
        </p>
    </div>

    @if(!empty($ordine->specifiche_iva))
        <p><strong>Specifiche IVA:</strong> {{ $ordine->specifiche_iva }}</p>
    @endif

    @if(!empty($ordine->costo_spedizione))
        <p><strong>Costo Spedizione:</strong> {{ $ordine->costo_spedizione }}</p>
    @endif

    @if(!empty($ordine->altre_specifiche_iva))
        <p><strong>Altre Specifiche IVA:</strong> {{ $ordine->altre_specifiche_iva }}</p>
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
</body>
</html>
