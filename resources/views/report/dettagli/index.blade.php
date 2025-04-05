@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Report per il titolo: <strong>{{ $report->libro->titolo }}</strong></h2>

    <!-- Legenda percentuali -->
    <div class="mb-4">
    <h5>Percentuali royalties previste dal contratto:</h5>
    <ul>
        <li>Vendite dirette: <strong>{{ $percentuali['diretta'] }}%</strong></li>
        <li>Vendite indirette: <strong>{{ $percentuali['indiretta'] }}%</strong></li>
        <li>Eventi: <strong>{{ $percentuali['evento'] }}%</strong></li>
    </ul>
</div>


    <!-- Pulsante stampa/esporta PDF -->
        <form method="POST" action="{{ route('report.dettagli.exportPdf', $report->id) }}" target="_blank">
        @csrf
        <input type="hidden" name="anno" value="{{ $anno }}">
        <button type="submit" class="btn btn-primary mt-3">
            Esporta in PDF (A4 Orizzontale)
        </button>
        </form>

    <!-- Tabella -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Periodo</th>
                    <th>Luogo</th>
                    <th>Q.tà</th>
                    <th>Prezzo</th>
                    <th>Valore lordo</th>
                    <th>Canale</th>
                    <th style="background-color: #fff3cd;">Royalties</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dettagli as $riga)
                    <tr>
                        <td>{{ $riga->periodo }}</td>
                        <td>{{ $riga->luogo }}</td>
                        <td>{{ $riga->quantita }}</td>
                        <td>€ {{ number_format($riga->prezzo_unitario, 2, ',', '.') }}</td>
                        <td>€ {{ number_format($riga->valore_lordo, 2, ',', '.') }}</td>
                        <td>{{ $riga->canale }}</td>
                        <td style="background-color: #fff3cd;">€ {{ number_format($riga->royalties, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Nessun dato disponibile per questo report.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #fff3cd; font-weight: bold;">
                    <td>TOTALE</td>
                    <td></td> <!-- Colonna vuota per "Luogo" -->
                    <td>{{ $totali['quantita'] }}</td>
                    <td></td> <!-- Colonna vuota per "Prezzo unitario" -->
                    <td></td> <!-- Colonna vuota per "Valore lordo" -->
                    <td></td> <!-- Colonna vuota per "Canale" -->
                    <td>€ {{ number_format($totali['royalties'], 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
