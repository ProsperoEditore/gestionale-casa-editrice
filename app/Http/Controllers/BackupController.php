<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use App\Models\Libro;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LibriExport;

class BackupController extends Controller
{
    public function index()
    {
        return view('backup.index');
    }

    public function download()
    {
        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');
        $password = config('database.connections.pgsql.password');
        $host = config('database.connections.pgsql.host');
        $backupFile = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';

        // Comando per dump
        $command = "PGPASSWORD={$password} pg_dump -h {$host} -U {$username} -d {$database} > storage/app/{$backupFile}";
        exec($command);

        return response()->download(storage_path("app/{$backupFile}"))->deleteFileAfterSend(true);
    }

    
    public function downloadSql()
    {
        $filename = 'backup_' . date('Ymd_His') . '.sql';
        $path = storage_path('app/' . $filename);

        // esegue il dump del database
        $command = sprintf(
            'PGPASSWORD=%s pg_dump -h %s -U %s -d %s -F p -f %s',
            env('DB_PASSWORD'),
            env('DB_HOST'),
            env('DB_USERNAME'),
            env('DB_DATABASE'),
            $path
        );

        putenv("PGPASSWORD=" . env('DB_PASSWORD'));
        exec($command);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function downloadExcel()
    {
        return Excel::download(new LibriExport, 'libri_backup_' . date('Ymd_His') . '.xlsx');
    }
}
