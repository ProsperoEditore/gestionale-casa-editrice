@php
$cliente = $ordine->anagrafica;
$marchio = \App\Models\MarchioEditoriale::where('nome', 'Prospero Editore')->first();
@endphp

<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<FatturaElettronica versione="FPR12" xmlns="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2">
  <FatturaElettronicaHeader>
    <DatiTrasmissione>
      <IdTrasmittente>
        <IdPaese>IT</IdPaese>
        <IdCodice>{{ $marchio->partita_iva }}</IdCodice>
      </IdTrasmittente>
      <ProgressivoInvio>{{ str_pad($ordine->id, 5, '0', STR_PAD_LEFT) }}</ProgressivoInvio>
      <FormatoTrasmissione>FPR12</FormatoTrasmissione>
      <CodiceDestinatario>{{ $cliente->codice_univoco ?? '0000000' }}</CodiceDestinatario>
      @if($cliente->pec)
      <PECDestinatario>{{ $cliente->pec }}</PECDestinatario>
      @endif
    </DatiTrasmissione>
    <CedentePrestatore>
      <DatiAnagrafici>
        <IdFiscaleIVA>
          <IdPaese>IT</IdPaese>
          <IdCodice>{{ $marchio->partita_iva }}</IdCodice>
        </IdFiscaleIVA>
        <Anagrafica>
          <Denominazione>{{ $marchio->nome }}</Denominazione>
        </Anagrafica>
        <RegimeFiscale>RF01</RegimeFiscale>
      </DatiAnagrafici>
      <Sede>
        <Indirizzo>{{ $marchio->indirizzo_sede_legale }}</Indirizzo>
        <CAP>00000</CAP>
        <Comune>Milano</Comune>
        <Provincia>MI</Provincia>
        <Nazione>IT</Nazione>
      </Sede>
    </CedentePrestatore>
    <CessionarioCommittente>
      <DatiAnagrafici>
        <CodiceFiscale>{{ $cliente->codice_fiscale ?? 'ND' }}</CodiceFiscale>
        <Anagrafica>
          <Denominazione>{{ $cliente->nome }}</Denominazione>
        </Anagrafica>
      </DatiAnagrafici>
      <Sede>
        <Indirizzo>{{ $cliente->indirizzo_fatturazione }}</Indirizzo>
        <CAP>00000</CAP>
        <Comune>Roma</Comune>
        <Provincia>RM</Provincia>
        <Nazione>IT</Nazione>
      </Sede>
    </CessionarioCommittente>
  </FatturaElettronicaHeader>

  <FatturaElettronicaBody>
    <DatiGenerali>
      <DatiGeneraliDocumento>
        <TipoDocumento>TD01</TipoDocumento>
        <Divisa>EUR</Divisa>
        <Data>{{ $ordine->data }}</Data>
        <Numero>{{ $ordine->codice }}</Numero>
      </DatiGeneraliDocumento>
    </DatiGenerali>

    <DatiBeniServizi>
      @foreach($ordine->libri as $index => $libro)
      <DettaglioLinee>
        <NumeroLinea>{{ $index + 1 }}</NumeroLinea>
        <Descrizione>{{ $libro->titolo }}</Descrizione>
        <Quantita>{{ $libro->pivot->quantita }}</Quantita>
        <PrezzoUnitario>{{ number_format($libro->pivot->prezzo_copertina, 2, '.', '') }}</PrezzoUnitario>
        <PrezzoTotale>{{ number_format($libro->pivot->valore_vendita_lordo, 2, '.', '') }}</PrezzoTotale>
        <Natura>N2.2</Natura>
        <RiferimentoNormativo>IVA assolta all'origine dall'editore, ai sensi dell'art.74 co. 1 lett. c del DPR 633/72</RiferimentoNormativo>
      </DettaglioLinee>
      @endforeach
    </DatiBeniServizi>

    <DatiPagamento>
      <Condizioni>TP02</Condizioni>
      <DettaglioPagamento>
        <ModalitaPagamento>MP01</ModalitaPagamento>
        <DataScadenzaPagamento>{{ \Carbon\Carbon::parse($ordine->data)->addDays(30)->toDateString() }}</DataScadenzaPagamento>
        <ImportoPagamento>{{ number_format($ordine->totale_netto_compilato, 2, '.', '') }}</ImportoPagamento>
      </DettaglioPagamento>
    </DatiPagamento>
  </FatturaElettronicaBody>
</FatturaElettronica>
