{{-- resources/views/ordini/xml.blade.php --}}

@php
    /** @var \App\Models\Ordine $ordine */

    // Dati soggetti
    $cliente = $ordine->anagrafica;
    $profilo = \App\Models\Profilo::first();
    if (!$profilo) {
        die('⚠️ Nessun profilo configurato per l’esportazione XML.');
    }

    if (empty($cliente->via_fatturazione) || empty($cliente->comune_fatturazione)) {
    die('⚠️ Dati cliente incompleti: Indirizzo e Comune sono obbligatori per lo SdI.');
    }

    // Riferimento normativo (fallback)
    $rifNorm = $ordine->specifiche_iva
        ?? "IVA assolta all'origine dall'editore, ai sensi dell'art.74 co. 1 lett. c del DPR 633/72";

    // Parametri documento
    $progressivo = str_pad($ordine->id, 5, '0', STR_PAD_LEFT);
    $dataDoc = \Carbon\Carbon::parse($ordine->data)->toDateString();
    $dataScad = \Carbon\Carbon::parse($ordine->data)->addDays(30)->toDateString();

    // IVA a livello d'ordine (default 0% + N2.2)
    $aliqOrd = (float)($ordine->aliquota_iva_ordine ?? 0.00);
    $natOrd  = $ordine->natura_iva_ordine ?? 'N2.2';
    $usaNatura = !empty($natOrd);

    // Aliquota effettiva da esporre (se c'è Natura deve essere 0.00)
    $aliqEff = $usaNatura ? 0.00 : $aliqOrd;

    // Helper numerico
    $fmt = fn($n) => number_format((float)$n, 2, '.', '');

    // Sanitizzazioni minime indirizzi
    $capCed  = $profilo->cap_amministrativa ?: '00000';
    $capCess = $cliente->cap_fatturazione ?: '00000';
    $nazCed  = $profilo->nazione_amministrativa ?: 'IT';
    $nazCess = $cliente->nazione_fatturazione ?: 'IT';

    // Costruzione linee (prezzo netto di riga = listino * (1 - sconto%))
    $righe = [];
    $totImponibile = 0.0;

    foreach ($ordine->libri as $index => $libro) {
        $q = (float) ($libro->pivot->quantita ?? 0);
        $prezzoListino = (float) (
            $libro->pivot->prezzo_copertina
            ?? $libro->prezzo_copertina
            ?? $libro->prezzo
            ?? 0
        );
        $sconto = (float) ($libro->pivot->sconto ?? 0);
        $prezzoNettoUnit = $prezzoListino * (1 - $sconto / 100);
        $totRiga = $prezzoNettoUnit * $q;

        $righe[] = [
            'num' => $index + 1,
            'descrizione' => $libro->titolo,
            'quantita' => $q,
            'prezzo_netto_unit' => $prezzoNettoUnit,
            'totale' => $totRiga,
        ];

        $totImponibile += $totRiga;
    }

    // Imposta e totale documento
    $imposta = $usaNatura ? 0.0 : $totImponibile * ($aliqEff / 100);
    $totDocumento = $totImponibile + $imposta;

    // Importo pagamento: se presente "totale_netto_compilato" lo rispettiamo, altrimenti totale documento
    $importoPagamento = is_null($ordine->totale_netto_compilato)
        ? $totDocumento
        : (float) $ordine->totale_netto_compilato;
@endphp

<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<p:FatturaElettronica versione="FPR12"
    xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2"
    xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2.2/Schema_del_file_xml_FatturaPA_versione_1.2.2.xsd">

  <p:FatturaElettronicaHeader>
    <p:DatiTrasmissione>
      <p:IdTrasmittente>
        <p:IdPaese>IT</p:IdPaese>
        <p:IdCodice>{{ $profilo->partita_iva ?? '00000000000' }}</p:IdCodice>
      </p:IdTrasmittente>
      <p:ProgressivoInvio>{{ $progressivo }}</p:ProgressivoInvio>
      <p:FormatoTrasmissione>FPR12</p:FormatoTrasmissione>
      <p:CodiceDestinatario>{{ $cliente->codice_univoco ?? '0000000' }}</p:CodiceDestinatario>
      @if(!empty($cliente->pec))
        <p:PECDestinatario>{{ $cliente->pec }}</p:PECDestinatario>
      @endif
    </p:DatiTrasmissione>

    <p:CedentePrestatore>
      <p:DatiAnagrafici>
        <p:IdFiscaleIVA>
          <p:IdPaese>IT</p:IdPaese>
          <p:IdCodice>{{ $profilo->partita_iva ?? '00000000000' }}</p:IdCodice>
        </p:IdFiscaleIVA>
        @if(!empty($profilo->codice_fiscale))
          <p:CodiceFiscale>{{ $profilo->codice_fiscale }}</p:CodiceFiscale>
        @endif
        <p:Anagrafica>
          <p:Denominazione>{{ $profilo->denominazione }}</p:Denominazione>
        </p:Anagrafica>
        <p:RegimeFiscale>{{ $profilo->regime_fiscale ?? 'RF01' }}</p:RegimeFiscale>
      </p:DatiAnagrafici>
      <p:Sede>
        <p:Indirizzo>{{ $profilo->indirizzo_amministrativa }}</p:Indirizzo>
        @if(!empty($profilo->numero_civico_amministrativa))
          <p:NumeroCivico>{{ $profilo->numero_civico_amministrativa }}</p:NumeroCivico>
        @endif
        <p:CAP>{{ $capCed }}</p:CAP>
        <p:Comune>{{ $profilo->comune_amministrativa }}</p:Comune>
        @if(!empty($profilo->provincia_amministrativa))
          <p:Provincia>{{ $profilo->provincia_amministrativa }}</p:Provincia>
        @endif
        <p:Nazione>{{ $nazCed }}</p:Nazione>
      </p:Sede>
    </p:CedentePrestatore>

    <p:CessionarioCommittente>
      <p:DatiAnagrafici>
        @if(!empty($cliente->partita_iva))
          <p:IdFiscaleIVA>
            <p:IdPaese>IT</p:IdPaese>
            <p:IdCodice>{{ $cliente->partita_iva }}</p:IdCodice>
          </p:IdFiscaleIVA>
        @endif

        @if(!empty($cliente->codice_fiscale))
          <p:CodiceFiscale>{{ $cliente->codice_fiscale }}</p:CodiceFiscale>
        @endif

        <p:Anagrafica>
          @if(!empty($cliente->denominazione))
            <p:Denominazione>{{ $cliente->denominazione }}</p:Denominazione>
          @else
            <p:Nome>{{ $cliente->nome ?? 'ND' }}</p:Nome>
            <p:Cognome>{{ $cliente->cognome ?? 'ND' }}</p:Cognome>
          @endif
        </p:Anagrafica>
      </p:DatiAnagrafici>

      <p:Sede>
        <p:Indirizzo>{{ $cliente->via_fatturazione ?? 'ND' }}</p:Indirizzo>
        @if(!empty($cliente->civico_fatturazione))
          <p:NumeroCivico>{{ $cliente->civico_fatturazione }}</p:NumeroCivico>
        @endif
        <p:CAP>{{ $capCess }}</p:CAP>
        <p:Comune>{{ $cliente->comune_fatturazione ?? 'ND' }}</p:Comune>
        @if(!empty($cliente->provincia_fatturazione))
          <p:Provincia>{{ $cliente->provincia_fatturazione }}</p:Provincia>
        @endif
        <p:Nazione>{{ $nazCess }}</p:Nazione>
      </p:Sede>
    </p:CessionarioCommittente>
  </p:FatturaElettronicaHeader>

  <p:FatturaElettronicaBody>
    <p:DatiGenerali>
      <p:DatiGeneraliDocumento>
        <p:TipoDocumento>TD01</p:TipoDocumento>
        <p:Divisa>EUR</p:Divisa>
        <p:Data>{{ $dataDoc }}</p:Data>
        <p:Numero>{{ $ordine->codice }}</p:Numero>
        <p:Causale>{{ $rifNorm }}</p:Causale>
      </p:DatiGeneraliDocumento>
    </p:DatiGenerali>

    <p:DatiBeniServizi>
      @foreach($righe as $r)
        <p:DettaglioLinee>
          <p:NumeroLinea>{{ $r['num'] }}</p:NumeroLinea>
          <p:Descrizione>{{ $r['descrizione'] }}</p:Descrizione>
          <p:Quantita>{{ $fmt($r['quantita']) }}</p:Quantita>
          <p:PrezzoUnitario>{{ $fmt($r['prezzo_netto_unit']) }}</p:PrezzoUnitario>
          <p:PrezzoTotale>{{ $fmt($r['totale']) }}</p:PrezzoTotale>
          <p:AliquotaIVA>{{ $fmt($aliqEff) }}</p:AliquotaIVA>
          @if($usaNatura)
            <p:Natura>{{ $natOrd }}</p:Natura>
            <p:RiferimentoNormativo>{{ $rifNorm }}</p:RiferimentoNormativo>
          @endif
        </p:DettaglioLinee>
      @endforeach

      <p:DatiRiepilogo>
        <p:AliquotaIVA>{{ $fmt($aliqEff) }}</p:AliquotaIVA>
        @if($usaNatura)
          <p:Natura>{{ $natOrd }}</p:Natura>
        @endif
        <p:ImponibileImporto>{{ $fmt($totImponibile) }}</p:ImponibileImporto>
        <p:Imposta>{{ $fmt($imposta) }}</p:Imposta>
        <p:EsigibilitaIVA>I</p:EsigibilitaIVA>
        @if($usaNatura)
          <p:RiferimentoNormativo>{{ $rifNorm }}</p:RiferimentoNormativo>
        @endif
      </p:DatiRiepilogo>
    </p:DatiBeniServizi>

    <p:DatiPagamento>
      <p:Condizioni>TP02</p:Condizioni>
      <p:DettaglioPagamento>
        <p:ModalitaPagamento>MP01</p:ModalitaPagamento>
        <p:DataScadenzaPagamento>{{ $dataScad }}</p:DataScadenzaPagamento>
        <p:ImportoPagamento>{{ $fmt($importoPagamento) }}</p:ImportoPagamento>
      </p:DettaglioPagamento>
    </p:DatiPagamento>
  </p:FatturaElettronicaBody>
</p:FatturaElettronica>
