{{-- resources/views/ordini/xml.blade.php --}}

@php
    /** @var \App\Models\Ordine $ordine */

    $cliente = $ordine->anagrafica;
    $profilo = \App\Models\Profilo::first();
    if (!$profilo) {
        die('⚠️ Nessun profilo configurato per l’esportazione XML.');
    }

    // Validazioni minime per evitare errori "campi vuoti"
    if (empty($profilo->denominazione)) die('⚠️ Denominazione Cedente mancante.');
    if (empty($profilo->partita_iva))  die('⚠️ Partita IVA Cedente mancante.');
    if (empty($profilo->indirizzo_amministrativa) || empty($profilo->comune_amministrativa)) {
        die('⚠️ Indirizzo/Comune sede Cedente mancanti.');
    }
    if (empty($cliente->via_fatturazione) || empty($cliente->comune_fatturazione)) {
        die('⚠️ Dati cliente incompleti: Indirizzo e Comune sono obbligatori per lo SdI.');
    }

    // Se invii ATTRAVERSO Unimatica come terzo intermediario, metti true e compila i dati
    $usaIntermediarioUnimatica = true;
    $intermPaese  = 'IT';
    $intermPiva   = '02098391200';
    $intermDenom  = 'UNIMATICA S.P.A.';

    // Riferimento normativo (fallback)
    $rifNorm = $ordine->specifiche_iva
        ?? "IVA assolta all'origine dall'editore, ai sensi dell'art.74 co. 1 lett. c del DPR 633/72";

    // Parametri documento
    $progressivo = str_pad($ordine->id, 5, '0', STR_PAD_LEFT);
    $dataDoc = \Carbon\Carbon::parse($ordine->data)->toDateString();
    $dataScad = \Carbon\Carbon::parse($ordine->data)->addDays(30)->toDateString();

    // Numero documento senza "/"
    $numeroDoc = str_replace('/', '-', (string) $ordine->codice);

    // Destinatario: uso codice univoco se presente, altrimenti PEC, altrimenti 0000000
    $codiceDest = trim($cliente->codice_univoco ?? '') !== '' ? strtoupper(trim($cliente->codice_univoco)) : '0000000';
    $pecDest    = trim($cliente->pec ?? '');

    // IVA a livello d'ordine (default 0% + N2.2)
    $aliqOrd = (float)($ordine->aliquota_iva_ordine ?? 0.00);
    $natOrd  = $ordine->natura_iva_ordine ?? 'N2.2';
    $usaNatura = !empty($natOrd);
    $aliqEff = $usaNatura ? 0.00 : $aliqOrd;

    // Helper numerico
    $fmt = fn($n) => number_format((float)$n, 2, '.', '');

    // Sanitizzazioni minime indirizzi/codici
    $capCed  = str_pad(preg_replace('/\D/', '', (string)($profilo->cap_amministrativa ?? '')), 5, '0', STR_PAD_LEFT) ?: '00000';
    $capCess = str_pad(preg_replace('/\D/', '', (string)($cliente->cap_fatturazione ?? '')), 5, '0', STR_PAD_LEFT) ?: '00000';
    $nazCed  = 'IT';
    $nazCess = 'IT';

    // Cedente: regime fiscale
    $regimeFiscale = $profilo->regime_fiscale ?: 'RF07'; // imposta quello corretto del profilo

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

    // Dati anagrafici cedente/cessionario robusti
    $denomCed = trim($profilo->denominazione);
    $cfCed    = trim($profilo->codice_fiscale ?? '');
    $pivaCed  = trim($profilo->partita_iva);

    $denomCess = trim($cliente->denominazione ?? '');
    $nomeCess  = trim($cliente->nome ?? '');
    $cognCess  = trim($cliente->cognome ?? '');
    $pivaCess  = trim($cliente->partita_iva ?? '');
    $cfCess    = trim($cliente->codice_fiscale ?? '');

    // Se è persona fisica senza denominazione, SdI vuole Nome + Cognome
    $isPersonaFisica = ($denomCess === '' && ($nomeCess !== '' || $cognCess !== ''));
@endphp

<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<FatturaElettronica versione="FPR12"
    xmlns="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2"
    xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2.2/Schema_del_file_xml_FatturaPA_versione_1.2.2.xsd">

  <FatturaElettronicaHeader>
    <DatiTrasmissione>
      <IdTrasmittente>
        @if($usaIntermediarioUnimatica)
          <IdPaese>{{ $intermPaese }}</IdPaese>
          <IdCodice>{{ $intermPiva }}</IdCodice>
        @else
          <IdPaese>IT</IdPaese>
          <IdCodice>{{ $pivaCed }}</IdCodice>
        @endif
      </IdTrasmittente>
      <ProgressivoInvio>{{ $progressivo }}</ProgressivoInvio>
      <FormatoTrasmissione>FPR12</FormatoTrasmissione>
      <CodiceDestinatario>{{ $codiceDest }}</CodiceDestinatario>
      @if($codiceDest === '0000000' && $pecDest !== '')
        <PECDestinatario>{{ $pecDest }}</PECDestinatario>
      @endif
    </DatiTrasmissione>

    <CedentePrestatore>
      <DatiAnagrafici>
        <IdFiscaleIVA>
          <IdPaese>IT</IdPaese>
          <IdCodice>{{ $pivaCed }}</IdCodice>
        </IdFiscaleIVA>
        @if($cfCed !== '')
          <CodiceFiscale>{{ $cfCed }}</CodiceFiscale>
        @endif
        <Anagrafica>
          <Denominazione>{{ $denomCed }}</Denominazione>
        </Anagrafica>
        <RegimeFiscale>{{ $regimeFiscale }}</RegimeFiscale>
      </DatiAnagrafici>
      <Sede>
        <Indirizzo>{{ $profilo->indirizzo_amministrativa }}</Indirizzo>
        @if(!empty($profilo->numero_civico_amministrativa))
          <NumeroCivico>{{ $profilo->numero_civico_amministrativa }}</NumeroCivico>
        @endif
        <CAP>{{ $capCed }}</CAP>
        <Comune>{{ $profilo->comune_amministrativa }}</Comune>
        @if(!empty($profilo->provincia_amministrativa))
          <Provincia>{{ $profilo->provincia_amministrativa }}</Provincia>
        @endif
        <Nazione>{{ $nazCed }}</Nazione>
      </Sede>
    </CedentePrestatore>

    <CessionarioCommittente>
      <DatiAnagrafici>
        @if($pivaCess !== '')
          <IdFiscaleIVA>
            <IdPaese>IT</IdPaese>
            <IdCodice>{{ $pivaCess }}</IdCodice>
          </IdFiscaleIVA>
        @endif

        @if($cfCess !== '')
          <CodiceFiscale>{{ $cfCess }}</CodiceFiscale>
        @endif

        <Anagrafica>
          @if(!$isPersonaFisica)
            <Denominazione>{{ $denomCess !== '' ? $denomCess : 'Cliente' }}</Denominazione>
          @else
            <Nome>{{ $nomeCess !== '' ? $nomeCess : 'ND' }}</Nome>
            <Cognome>{{ $cognCess !== '' ? $cognCess : 'ND' }}</Cognome>
          @endif
        </Anagrafica>
      </DatiAnagrafici>

      <Sede>
        <Indirizzo>{{ $cliente->via_fatturazione }}</Indirizzo>
        @if(!empty($cliente->civico_fatturazione))
          <NumeroCivico>{{ $cliente->civico_fatturazione }}</NumeroCivico>
        @endif
        <CAP>{{ $capCess }}</CAP>
        <Comune>{{ trim($cliente->comune_fatturazione) }}</Comune>
        @if(!empty($cliente->provincia_fatturazione))
          <Provincia>{{ $cliente->provincia_fatturazione }}</Provincia>
        @endif
        <Nazione>{{ $nazCess }}</Nazione>
      </Sede>
    </CessionarioCommittente>

    @if($usaIntermediarioUnimatica)
      <TerzoIntermediarioOSoggettoEmittente>
        <DatiAnagrafici>
          <IdFiscaleIVA>
            <IdPaese>{{ $intermPaese }}</IdPaese>
            <IdCodice>{{ $intermPiva }}</IdCodice>
          </IdFiscaleIVA>
          <Anagrafica>
            <Denominazione>{{ $intermDenom }}</Denominazione>
          </Anagrafica>
        </DatiAnagrafici>
      </TerzoIntermediarioOSoggettoEmittente>
      <SoggettoEmittente>TZ</SoggettoEmittente>
    @endif
  </FatturaElettronicaHeader>

  <FatturaElettronicaBody>
    <DatiGenerali>
      <DatiGeneraliDocumento>
        <TipoDocumento>TD01</TipoDocumento>
        <Divisa>EUR</Divisa>
        <Data>{{ $dataDoc }}</Data>
        <Numero>{{ $numeroDoc }}</Numero>
        <Causale>{{ $rifNorm }}</Causale>
        {{-- opzionale: <ImportoTotaleDocumento>{{ $fmt($totDocumento) }}</ImportoTotaleDocumento> --}}
      </DatiGeneraliDocumento>
    </DatiGenerali>

    <DatiBeniServizi>
      @foreach($righe as $r)
        <DettaglioLinee>
          <NumeroLinea>{{ $r['num'] }}</NumeroLinea>
          <Descrizione>{{ $r['descrizione'] }}</Descrizione>
          <Quantita>{{ $fmt($r['quantita']) }}</Quantita>
          <PrezzoUnitario>{{ $fmt($r['prezzo_netto_unit']) }}</PrezzoUnitario>
          <PrezzoTotale>{{ $fmt($r['totale']) }}</PrezzoTotale>
          <AliquotaIVA>{{ $fmt($aliqEff) }}</AliquotaIVA>
          @if($usaNatura)
            <Natura>{{ $natOrd }}</Natura>
            <RiferimentoNormativo>{{ $rifNorm }}</RiferimentoNormativo>
          @endif
        </DettaglioLinee>
      @endforeach

      <DatiRiepilogo>
        <AliquotaIVA>{{ $fmt($aliqEff) }}</AliquotaIVA>
        @if($usaNatura)
          <Natura>{{ $natOrd }}</Natura>
        @endif
        <ImponibileImporto>{{ $fmt($totImponibile) }}</ImponibileImporto>
        <Imposta>{{ $fmt($imposta) }}</Imposta>
        <EsigibilitaIVA>I</EsigibilitaIVA>
        @if($usaNatura)
          <RiferimentoNormativo>{{ $rifNorm }}</RiferimentoNormativo>
        @endif
      </DatiRiepilogo>
    </DatiBeniServizi>

    <DatiPagamento>
      <CondizioniPagamento>TP02</CondizioniPagamento>
      <DettaglioPagamento>
        {{-- MP01 Bonifico, MP05 Rid/Altro: imposta quello che usi di solito --}}
        <ModalitaPagamento>MP01</ModalitaPagamento>
        <DataScadenzaPagamento>{{ $dataScad }}</DataScadenzaPagamento>
        <ImportoPagamento>{{ $fmt($importoPagamento) }}</ImportoPagamento>
        {{-- opzionali: IBAN/Beneficiario se vuoi replicare lo stile Unimatica --}}
        {{-- <Beneficiario>{{ $denomCed }}</Beneficiario> --}}
        {{-- <IBAN>{{ $profilo->iban ?? '' }}</IBAN> --}}
      </DettaglioPagamento>
    </DatiPagamento>
  </FatturaElettronicaBody>
</FatturaElettronica>
