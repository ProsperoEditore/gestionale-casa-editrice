@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4 text-center">Backup Database</h3>
    <p>Puoi scaricare un backup completo del database in formato SQL o Excel.</p>

    <div class="d-flex gap-3 mb-4">
        <a href="{{ route('backup.sql') }}" class="btn btn-success">Scarica Backup .SQL</a>
        <a href="{{ route('backup.excel') }}" class="btn btn-outline-primary">Scarica Backup .XLSX</a>
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
