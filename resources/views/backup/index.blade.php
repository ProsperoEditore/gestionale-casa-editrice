@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4 text-center">Backup Database</h3>
    <p>Puoi scaricare un backup completo del database oppure esportare una singola sezione.</p>

    {{-- Pulsanti completi --}}
    <div class="d-flex flex-wrap gap-3 mb-4">
        <a href="{{ route('backup.sql') }}" class="btn btn-primary">Backup Completo .SQL</a>
        <a href="{{ route('backup.excel') }}" class="btn btn-success">Backup Completo .XLSX</a>
    </div>

    <hr>

    <h5 class="mt-4">Esporta singole sezioni (.xlsx)</h5>
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('backup.singolo', ['sezione' => 'libri']) }}" class="btn btn-outline-success btn-sm">Libri</a>
        <a href="{{ route('backup.singolo', ['sezione' => 'anagrafiche']) }}" class="btn btn-outline-success btn-sm">Anagrafiche</a>
        <a href="{{ route('backup.singolo', ['sezione' => 'contratti']) }}" class="btn btn-outline-success btn-sm">Contratti</a>
        <a href="{{ route('backup.singolo', ['sezione' => 'magazzini']) }}" class="btn btn-outline-success btn-sm">Magazzini</a>
        <a href="{{ route('backup.singolo', ['sezione' => 'ordini']) }}" class="btn btn-outline-success btn-sm">Ordini</a>
        <a href="{{ route('backup.singolo', ['sezione' => 'scarichi']) }}" class="btn btn-outline-success btn-sm">Spedizioni</a>
        <a href="{{ route('backup.singolo', ['sezione' => 'registro-tirature']) }}" class="btn btn-outline-success btn-sm">Registro Tirature</a>
        <a href="{{ route('backup.singolo', ['sezione' => 'registro-vendite']) }}" class="btn btn-outline-success btn-sm">Registro Vendite</a>
        <a href="{{ route('backup.singolo', ['sezione' => 'report']) }}" class="btn btn-outline-success btn-sm">Report</a>
    </div>

    <hr>

    <h5 class="mt-4">Backup via Heroku (manuale da terminale)</h5>
    <pre>
heroku pg:backups:capture --app gestionale-prospero
heroku pg:backups:download --app gestionale-prospero
    </pre>

    <h5 class="mt-3">Ripristino backup (da terminale)</h5>
    <pre>
heroku pg:backups:restore 'URL_DEL_BACKUP' DATABASE_URL --app gestionale-prospero
    </pre>
</div>
@endsection

