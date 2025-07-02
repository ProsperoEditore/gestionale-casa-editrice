<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Sollecito di pagamento</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; background-color: #f8f9fa; padding: 20px;">
    <div style="background-color: #fff; padding: 20px; border-radius: 6px; max-width: 600px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ asset('images/logo-prospero.png') }}" alt="Logo Prospero" style="max-width: 180px;">
        </div>

        <p>Gentile <strong>{{ $nome }}</strong>,</p>

        <p>la contattiamo per ricordarle il saldo dell’<strong>ordine {{ $ordine->codice ?? '---' }}</strong>, emesso in data <strong>{{ \Carbon\Carbon::parse($ordine->created_at)->format('d/m/Y') }}</strong>.</p>

        <p>Qualora il pagamento fosse già stato effettuato, la preghiamo di ignorare questa comunicazione.</p>

        <p>Per qualsiasi chiarimento restiamo a disposizione.</p>

        <p style="margin-top: 30px;">Grazie per la collaborazione.</p>

        <p>Cordiali saluti,<br>
        <strong>{{ $profilo->denominazione ?? 'Prospero Editore' }}</strong></p>

        <hr style="margin: 30px 0;">

        <p style="font-size: 12px; color: #666;">
            {{ $profilo->denominazione ?? '' }}<br>
            P.IVA: {{ $profilo->partita_iva ?? '-' }}<br>
            Email: {{ $profilo->email ?? '-' }}<br>
            Telefono: {{ $profilo->telefono ?? '-' }}<br>
            IBAN: {{ $profilo->iban ?? '-' }}
        </p>
    </div>
</body>
</html>
