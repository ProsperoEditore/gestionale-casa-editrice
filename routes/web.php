<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnagraficaController;
use App\Http\Controllers\ContrattoController;
use App\Http\Controllers\GiacenzaController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\MagazzinoController;
use App\Http\Controllers\MarchioEditorialeController;
use App\Http\Controllers\OrdineController;
use App\Http\Controllers\RegistroTiratureController;
use App\Http\Controllers\RegistroTiraturaDettaglioController;
use App\Http\Controllers\RegistroVenditeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportDettaglioController;
use App\Http\Controllers\ScaricoController;
use App\Http\Controllers\BackupController;

Route::middleware('auth')->group(function () {

    // Home
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // ✅ Fix: alias della route 'home'
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');

// Profilo utente
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/utenti/{utente}/edit', [UserController::class, 'edit'])->name('utenti.edit');
Route::put('/utenti/{utente}', [UserController::class, 'update'])->name('utenti.update');

});

// Anagrafiche
Route::resource('anagrafiche', AnagraficaController::class);
Route::get('/anagrafiche/autocomplete', [AnagraficaController::class, 'autocomplete'])->name('anagrafiche.autocomplete');

// Contratti
Route::get('contratti', [ContrattoController::class, 'index'])->name('contratti.index');
Route::get('contratti/create', [ContrattoController::class, 'create'])->name('contratti.create');
Route::post('contratti', [ContrattoController::class, 'store'])->name('contratti.store');
Route::get('contratti/{contratto}', [ContrattoController::class, 'show'])->name('contratti.show');
Route::get('contratti/{contratto}/edit', [ContrattoController::class, 'edit'])->name('contratti.edit');
Route::put('contratti/{contratto}', [ContrattoController::class, 'update'])->name('contratti.update');
Route::delete('contratti/{contratto}', [ContrattoController::class, 'destroy'])->name('contratti.destroy');

// Marchi editoriali
Route::resource('marchi-editoriali', MarchioEditorialeController::class);

// Libri
Route::resource('libri', LibroController::class);
Route::post('/libri/import', [LibroController::class, 'import'])->name('libri.import');
Route::get('/libri/autocomplete', [LibroController::class, 'autocomplete'])->name('libri.autocomplete');


// Magazzini
Route::get('/magazzini', [MagazzinoController::class, 'index'])->name('magazzini.index');
Route::get('/magazzini/create', [MagazzinoController::class, 'create'])->name('magazzini.create');
Route::post('/magazzini/store', [MagazzinoController::class, 'store'])->name('magazzini.store');
Route::delete('/magazzini/{id}', [MagazzinoController::class, 'destroy'])->name('magazzini.destroy');
Route::put('/magazzini/{id}/update-scadenza', [MagazzinoController::class, 'updateScadenza'])->name('magazzini.updateScadenza');

// Giacenze collegate a un magazzino
Route::get('/magazzini/{magazzino}/giacenze', [GiacenzaController::class, 'create'])->name('giacenze.create');
Route::post('/magazzini/{magazzino}/giacenze', [GiacenzaController::class, 'store'])->name('giacenze.store');
Route::post('/magazzini/{magazzino}/giacenze/import', [GiacenzaController::class, 'importGiacenze'])->name('giacenze.import');
Route::get('/magazzini/{magazzino}/giacenze/export', [GiacenzaController::class, 'exportGiacenze'])->name('giacenze.export');

// Azioni dirette su una giacenza specifica
Route::get('/giacenze/{giacenza}/edit', [GiacenzaController::class, 'edit'])->name('giacenze.edit');
Route::put('/giacenze/{giacenza}', [GiacenzaController::class, 'update'])->name('giacenze.update');
Route::delete('/giacenze/{id}', [GiacenzaController::class, 'destroy'])->name('giacenze.destroy');

// Rotta fallback per accesso diretto a giacenze.create senza magazzino
Route::get('/giacenze/create', function () {
    return redirect()->route('magazzini.index')->with('error', '⚠ Devi selezionare prima un magazzino.');
});


// Ordini
Route::get('/ordini/autocomplete-anagrafica', [OrdineController::class, 'autocompleteAnagrafica'])->name('ordini.autocomplete-anagrafica');
Route::resource('ordini', OrdineController::class);
Route::post('/ordini/{id}/import', [OrdineController::class, 'importLibri'])->name('ordini.import.libri');
Route::get('/ordini/{id}/stampa', [OrdineController::class, 'stampa'])->name('ordini.stampa');
Route::get('/ordini/{id}/libri', [OrdineController::class, 'gestioneLibri'])->name('ordini.gestione_libri');
Route::post('/ordini/{id}/libri', [OrdineController::class, 'storeLibri'])->name('ordini.libri.store');
Route::get('/ordini/search', [OrdineController::class, 'search'])->name('ordini.search');





// Registro Tirature
Route::resource('registro-tirature', RegistroTiratureController::class);
Route::get('/registro-tirature/{registroTiratureId}/dettagli', [RegistroTiraturaDettaglioController::class, 'index'])->name('registro-tirature.dettagli.index');
Route::get('/registro-tirature/{registroTiratureId}/dettagli/create', [RegistroTiraturaDettaglioController::class, 'create'])->name('registro-tirature.dettagli.create');
Route::post('/registro-tirature/{registroTirature}/dettagli', [RegistroTiraturaDettaglioController::class, 'store'])->name('registro-tirature.dettagli.store');
Route::get('/registro-tirature/{registroTirature}/dettagli/{dettaglio}/edit', [RegistroTiraturaDettaglioController::class, 'edit'])->name('registro-tirature.dettagli.edit');
Route::put('/registro-tirature/{registroTirature}/dettagli/{dettaglio}', [RegistroTiraturaDettaglioController::class, 'update'])->name('registro-tirature.dettagli.update');
Route::delete('/registro-tirature/{registroTirature}/dettagli/{dettaglio}', [RegistroTiraturaDettaglioController::class, 'destroy'])->name('registro-tirature.dettagli.destroy');
Route::post('/registro-tirature/{registroTirature}/import-excel', [RegistroTiraturaDettaglioController::class, 'importExcel'])->name('registro-tirature.dettagli.import');
Route::get('/registro-tirature/{registroTirature}/export-excel', [RegistroTiraturaDettaglioController::class, 'exportExcel'])->name('registro-tirature.dettagli.exportExcel');
Route::get('/registro-tirature/{id}/export-pdf', [RegistroTiraturaDettaglioController::class, 'exportPDF'])->name('registro-tirature.dettagli.exportPDF');

// Registro Vendite
Route::resource('registro-vendite', RegistroVenditeController::class)->except(['store']);
Route::post('/registro-vendite', [RegistroVenditeController::class, 'store'])->name('registro-vendite.store');
Route::get('/registro-vendite/{id}/gestione', [RegistroVenditeController::class, 'gestione'])->name('registro-vendite.gestione');
Route::post('/registro-vendite/{id}/import', [RegistroVenditeController::class, 'importExcel'])->name('registro-vendite.import');
Route::post('/registro-vendite/{id}/salva-dettagli', [RegistroVenditeController::class, 'salvaDettagli'])->name('registro-vendite.salvaDettagli');
Route::delete('/registro-vendite/dettaglio/{id}', [RegistroVenditeController::class, 'destroyDettaglio'])
    ->name('registro-vendite.dettagli.destroy');

// Autocomplete Libri
Route::get('/libri/autocomplete', [LibroController::class, 'autocomplete'])->name('libri.autocomplete');


// Report
Route::resource('report', ReportController::class)->except(['show']);
Route::get('/report/{reportId}/dettagli', [ReportDettaglioController::class, 'index'])->name('report.dettagli.index');
Route::get('/report/{reportId}/dettagli/pdf', [ReportDettaglioController::class, 'exportPdf'])->name('report.dettagli.pdf');
Route::get('/report/autocomplete-libro', [App\Http\Controllers\ReportController::class, 'autocompleteLibro'])->name('report.autocomplete-libro');
Route::post('report/{reportId}/dettagli/pdf', [ReportDettaglioController::class, 'exportPdf'])->name('report.dettagli.exportPdf');

// Scarichi
Route::resource('scarichi', ScaricoController::class)->except(['show']);
Route::patch('/scarichi/{id}/update-info', [ScaricoController::class, 'updateInfoSpedizione'])->name('scarichi.updateInfoSpedizione');
Route::get('/scarichi/autocomplete-ordini', [\App\Http\Controllers\ScaricoController::class, 'autocompleteOrdini'])
    ->name('scarichi.autocomplete-ordini');
Route::put('/scarichi/{id}/update-stato', [ScaricoController::class, 'updateStato'])->name('scarichi.updateStato');




Route::resource('utenti', UserController::class)->except(['show', 'edit', 'update']);



// Pagina principale backup
Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');

// Backup singoli per ogni sezione
Route::get('/backup/libri', [BackupController::class, 'downloadSingolo'])->name('backup.libri')->defaults('sezione', 'libri');
Route::get('/backup/magazzini', [BackupController::class, 'downloadSingolo'])->name('backup.magazzini')->defaults('sezione', 'magazzini');
Route::get('/backup/contratti', [BackupController::class, 'downloadSingolo'])->name('backup.contratti')->defaults('sezione', 'contratti');
Route::get('/backup/ordini', [BackupController::class, 'downloadSingolo'])->name('backup.ordini')->defaults('sezione', 'ordini');
Route::get('/backup/registro-vendite', [BackupController::class, 'downloadSingolo'])->name('backup.registro-vendite')->defaults('sezione', 'registro-vendite');
Route::get('/backup/registro-tirature', [BackupController::class, 'downloadSingolo'])->name('backup.registro-tirature')->defaults('sezione', 'registro-tirature');
Route::get('/backup/report', [BackupController::class, 'downloadSingolo'])->name('backup.report')->defaults('sezione', 'report');
Route::get('/backup/scarichi', [BackupController::class, 'downloadSingolo'])->name('backup.scarichi')->defaults('sezione', 'scarichi');
Route::get('/backup/anagrafiche', [BackupController::class, 'downloadSingolo'])->name('backup.anagrafiche')->defaults('sezione', 'anagrafiche');

// Backup completo in un unico file con più fogli
Route::get('/backup/excel', [BackupController::class, 'downloadCompleto'])->name('backup.excel');

// Backup SQL (generato tramite comando artisan)
Route::get('/backup/sql', [BackupController::class, 'downloadSql'])->name('backup.sql');

});


// Auth
require __DIR__.'/auth.php';

