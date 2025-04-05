@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dettagli Registro Tirature: {{ $registro->periodo }} {{ $registro->anno }}</h1>

    <a href="{{ route('registro-tirature.dettagli.create', $registro->id) }}" class="btn btn-success mb-3">Aggiungi Dettaglio</a>

    <form action="{{ route('registro-tirature.dettagli.import', $registro->id) }}" method="POST" enctype="multipart/form-data" class="mb-3">
    @csrf
    <div class="row g-2 align-items-center">
        <div class="col-auto">
            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-warning">Importa Excel</button>
        </div>
    </div>
    </form>

    <a href="{{ route('registro-tirature.dettagli.exportExcel', $registro->id) }}" class="btn btn-outline-success mb-3">Esporta in Excel</a>
    <a href="{{ route('registro-tirature.dettagli.exportPDF', $registro->id) }}" class="btn btn-outline-danger mb-3">Esporta in PDF</a>


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Data</th>
                <th>Titolo</th>
                <th>Copie Stampate</th>
                <th>Prezzo di Vendita IVA Compresa</th>
                <th>Imponibile Relativo</th>
                <th>Imponibile</th>
                <th>IVA 4%</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dettagli as $dettaglio)
            <tr>
                <td>{{ $dettaglio->data }}</td>
                <td>{{ $dettaglio->titolo->titolo }}</td>
                <td>{{ $dettaglio->copie_stampate }}</td>
                <td>{{ number_format($dettaglio->prezzo_vendita_iva, 3, ',', '') }}</td>
                <td>{{ number_format($dettaglio->imponibile_relativo, 3, ',', '') }}</td>
                <td>{{ number_format($dettaglio->imponibile, 3, ',', '') }}</td>
                <td>{{ number_format($dettaglio->iva_4percento, 3, ',', '') }}</td>
                <td>
                    <a href="{{ route('registro-tirature.dettagli.edit', ['registroTirature' => $registro->id, 'dettaglio' => $dettaglio->id]) }}" class="btn btn-primary btn-sm">Modifica</a>
                    <form action="{{ route('registro-tirature.dettagli.destroy', ['registroTirature' => $registro->id, 'dettaglio' => $dettaglio->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Elimina</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr class="table-warning">
                <th colspan="2">Totali</th>
                <th>{{ $dettagli->sum('copie_stampate') }}</th>
                <th></th>
                <th>{{ number_format($dettagli->sum('imponibile_relativo'), 3, ',', '') }}</th>
                <th>{{ number_format($dettagli->sum('imponibile'), 3, ',', '') }}</th>
                <th>{{ number_format($dettagli->sum('iva_4percento'), 3, ',', '') }}</th>
                <th></th>
            </tr>
        </tfoot>

    </table>
</div>
@endsection
