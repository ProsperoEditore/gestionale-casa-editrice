<?php

namespace App\Http\Controllers;

use App\Exports\MultiSheetExport;
use App\Exports\SingoloExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

// Importa i modelli da esportare
use App\Models\Libro;
use App\Models\Magazzino;
use App\Models\Contratto;
use App\Models\Ordine;
use App\Models\RegistroVendite;
use App\Models\RegistroTirature;
use App\Models\Report;
use App\Models\Scarico;
use App\Models\Anagrafica;

class BackupController extends Controller
{
    public function index()
    {
        return view('backup.index');
    }

    public function downloadSingolo($sezione)
    {
        $modelMap = [
            'libri' => Libro::class,
            'magazzini' => Magazzino::class,
            'contratti' => Contratto::class,
            'ordini' => Ordine::class,
            'registro-vendite' => RegistroVendite::class,
            'registro-tirature' => RegistroTirature::class,
            'report' => Report::class,
            'scarichi' => Scarico::class,
            'anagrafiche' => Anagrafica::class,
        ];

        if (!array_key_exists($sezione, $modelMap)) {
            abort(404);
        }

        $model = $modelMap[$sezione];
        $nomeFile = 'backup_' . $sezione . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new SingoloExport($model), $nomeFile);
    }

    public function downloadCompleto()
    {
        return Excel::download(new MultiSheetExport, 'backup_completo_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function downloadSql()
    {
        $db = config('database.connections.pgsql'); // Modifica se usi MySQL: 'mysql'
        $filename = 'backup_database_' . now()->format('Ymd_His') . '.sql';
        $path = storage_path("app/{$filename}");
    
        // Se usi Postgres su Heroku
        $url = parse_url(env('DATABASE_URL'));
        $host = $url['host'];
        $port = $url['port'];
        $database = ltrim($url['path'], '/');
        $username = $url['user'];
        $password = $url['pass'];
    
        $process = new Process([
            'pg_dump',
            '-h', $host,
            '-p', $port,
            '-U', $username,
            '-d', $database,
            '-f', $path
        ]);
    
        $process->setEnv(['PGPASSWORD' => $password]);
        $process->run();
    
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    
        return response()->download($path)->deleteFileAfterSend(true);
    }

}
