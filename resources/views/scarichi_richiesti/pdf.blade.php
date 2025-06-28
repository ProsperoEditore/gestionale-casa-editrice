<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Scarichi da approvare</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Scarichi da approvare</h2>
    <table>
        <thead>
            <tr>
                <th>Ordine</th>
                <th>Destinatario</th>
                <th>ISBN</th>
                <th>Titolo</th>
                <th>Magazzino</th>
                <th>Giacenza attuale</th>                
                <th>Quantità richiesta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($richieste as $r)
                <tr>
                    <td>{{ $r->ordine->codice }}</td>
                    <td>{{ $r->destinatario ?? 'N/D' }}</td>
                    <td>{{ $r->libro->isbn }}</td>
                    <td>{{ $r->libro->titolo }}</td>
                    <td>{{ $r->magazzino_nome ?? 'N/D' }}</td>
                    <td>{{ $r->quantita_disponibile ?? 'N/D' }}</td>
                    <td><strong style="color: red">{{ $r->quantita }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
