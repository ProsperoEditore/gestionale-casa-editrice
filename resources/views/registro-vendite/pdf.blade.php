<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registro Vendite</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #999;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        h3 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <h3>Registro Vendite - {{ $registro->anagrafica->nome }}</h3>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Periodo</th>
                <th>ISBN</th>
                <th style="width: 30%;">Titolo</th>
                <th>Quantità</th>
                <th>Prezzo</th>
                <th>Valore Lordo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registro->dettagli as $riga)
                <tr>
                    <td>{{ $riga->data }}</td>
                    <td>{{ $riga->periodo }}</td>
                    <td>{{ $riga->isbn }}</td>
                    <td>{{ $riga->titolo }}</td>
                    <td>{{ $riga->quantita }}</td>
                    <td>{{ number_format($riga->prezzo, 2, ',', '.') }}</td>
                    <td>{{ number_format($riga->quantita * $riga->prezzo, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;">Nessuna vendita presente.</td></tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
