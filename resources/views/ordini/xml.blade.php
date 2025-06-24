@php
$cliente = $ordine->anagrafica;
$profilo = \App\Models\Profilo::first();
if (!$profilo) die('⚠️ Nessun profilo configurato per l’esportazione XML.');
@endphp

<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<p:FatturaElettronica versione="FPR12"
    xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2"
    xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2
    http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2.2/Schema_del_file_xml_FatturaPA_versione_1.2.2.xsd">

  <p:FatturaElettronicaHeader>
    <p:DatiTrasmissione>
      <p:IdTrasmittente>
        <p:IdPaese>IT</p:IdPaese>
        <p:IdCodice>{{ $profilo->partita_iva ?? '00000000000' }}</p:IdCodice>
      </p:IdTrasmittente>
      <p:ProgressivoInvio>{{ str_pad($ordine->id, 5, '0', STR_PAD_LEFT) }}</p:ProgressivoInvio>
      <p:FormatoTrasmissione>FPR12</p:FormatoTrasmissione>
      <p:CodiceDestinatario>{{ $cliente->codice_univoco ?? '0000000' }}</p:CodiceDestinatario>
      @if($cliente->pec)
        <p:PECDestinatario>{{ $cliente->pec }}</p:PECDestinatario>
      @endif
    </p:DatiTrasmissione>

    <p:CedentePrestatore>
      <p:DatiAnagrafici>
        <p:IdFiscaleIVA>
          <p:IdPaese>IT</p:IdPaese>
          <p:IdCodice>{{ $profilo->partita_iva ?? '00000000000' }}</p:IdCodice>
        </p:IdFiscaleIVA>
        <p:Anagrafica>
          <p:Denominazione>{{ $profilo->denominazione }}</p:Denominazione>
        </p:Anagrafica>
        <p:RegimeFiscale>{{ $profilo->regime_fiscale ?? 'RF01' }}</p:RegimeFiscale>
      </p:DatiAnagrafici>
      <p:Sede>
        <p:Indirizzo>{{ $profilo->indirizzo_amministrativa }}</p:Indirizzo>
        <p:NumeroCivico>{{ $profilo->numero_civico_amministrativa }}</p:NumeroCivico>
        <p:CAP>{{ $profilo->cap_amministrativa ?? '00000' }}</p:CAP>
        <p:Comune>{{ $profilo->comune_amministrativa }}</p:Comune>
        <p:Provincia>{{ $profilo->provincia_amministrativa }}</p:Provincia>
        <p:Nazione>{{ $profilo->nazione_amministrativa }}</p:Nazione>
      </p:Sede>
    </p:CedentePrestatore>

    <p:CessionarioCommittente>
      <p:DatiAnagrafici>
        @if($cliente->partita_iva)
        <p:IdFiscaleIVA>
          <p:IdPaese>IT</p:IdPaese>
          <p:IdCodice>{{ $cliente->partita_iva }}</p:IdCodice>
        </p:IdFiscaleIVA>
        @endif

        @if($cliente->codice_fiscale)
        <p:CodiceFiscale>{{ $cliente->codice_fiscale }}</p:CodiceFiscale>
        @endif

        <p:Anagrafica>
          @if($cliente->denominazione)
            <p:Denominazione>{{ $cliente->denominazione }}</p:Denominazione>
          @else
            <p:Nome>{{ $cliente->nome ?? 'ND' }}</p:Nome>
            <p:Cognome>{{ $cliente->cognome ?? 'ND' }}</p:Cognome>
          @endif
        </p:Anagrafica>
      </p:DatiAnagrafici>

      <p:Sede>
        <p:Indirizzo>{{ $cliente->via_fatturazione ?? 'ND' }}</p:Indirizzo>
        <p:NumeroCivico>{{ $cliente->civico_fatturazione ?? 'ND' }}</p:NumeroCivico>
        <p:CAP>{{ $cliente->cap_fatturazione ?? '00000' }}</p:CAP>
        <p:Comune>{{ $cliente->comune_fatturazione ?? 'ND' }}</p:Comune>
        <p:Provincia>{{ $cliente->provincia_fatturazione ?? 'ND' }}</p:Provincia>
        <p:Nazione>{{ $cliente->nazione_fatturazione ?? 'IT' }}</p:Nazione>
      </p:Sede>
    </p:CessionarioCommittente>
  </p:FatturaElettronicaHeader>

  <p:FatturaElettronicaBody>
    <p:DatiGenerali>
      <p:DatiGeneraliDocumento>
        <p:TipoDocumento>TD01</p:TipoDocumento>
        <p:Divisa>EUR</p:Divisa>
        <p:Data>{{ $ordine->data }}</p:Data>
        <p:Numero>{{ $ordine->codice }}</p:Numero>
      </p:DatiGeneraliDocumento>
    </p:DatiGenerali>

    <p:DatiBeniServizi>
      @foreach($ordine->libri as $index => $libro)
      <p:DettaglioLinee>
        <p:NumeroLinea>{{ $index + 1 }}</p:NumeroLinea>
        <p:Descrizione>{{ $libro->titolo }}</p:Descrizione>
        <p:Quantita>{{ $libro->pivot->quantita }}</p:Quantita>
        <p:PrezzoUnitario>{{ number_format($libro->pivot->prezzo_copertina, 2, '.', '') }}</p:PrezzoUnitario>
        <p:PrezzoTotale>{{ number_format($libro->pivot->valore_vendita_lordo, 2, '.', '') }}</p:PrezzoTotale>
        <p:Natura>N2.2</p:Natura>
        <p:RiferimentoNormativo>IVA assolta all'origine dall'editore, ai sensi dell'art.74 co. 1 lett. c del DPR 633/72</p:RiferimentoNormativo>
      </p:DettaglioLinee>
      @endforeach
    </p:DatiBeniServizi>

    <p:DatiPagamento>
      <p:Condizioni>TP02</p:Condizioni>
      <p:DettaglioPagamento>
        <p:ModalitaPagamento>MP01</p:ModalitaPagamento>
        <p:DataScadenzaPagamento>{{ \Carbon\Carbon::parse($ordine->data)->addDays(30)->toDateString() }}</p:DataScadenzaPagamento>
        <p:ImportoPagamento>{{ number_format($ordine->totale_netto_compilato, 2, '.', '') }}</p:ImportoPagamento>
      </p:DettaglioPagamento>
    </p:DatiPagamento>
  </p:FatturaElettronicaBody>
</p:FatturaElettronica>
