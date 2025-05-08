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


<!-- Esporta PDF con intervallo di date -->
    <form method="POST" action="{{ route('report.dettagli.exportPdf', $report->id) }}" target="_blank" class="d-flex align-items-end gap-3 flex-wrap">
        @csrf

        @php
            $oggi = \Carbon\Carbon::now();
            $fineAnnoPrecedente = $oggi->copy()->subYear()->endOfYear()->format('Y-m-d');
            $dataInizio = $dettagli->min('periodo') ? \Carbon\Carbon::parse($dettagli->min('periodo'))->format('Y-m-d') : '2022-01-01';
        @endphp

        <div>
            <label for="data_inizio"><strong>Dal:</strong></label>
            <input type="date" name="data_inizio" id="data_inizio" class="form-control" value="{{ $dataInizio }}">
        </div>

        <div>
            <label for="data_fine"><strong>Al:</strong></label>
            <input type="date" name="data_fine" id="data_fine" class="form-control" value="{{ $fineAnnoPrecedente }}">
        </div>

        <div>
            <button type="submit" class="btn btn-primary mt-2">
                Esporta in PDF (A4 Orizzontale)
            </button>
        </div>
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
