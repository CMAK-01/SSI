<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ParcInformatiqueController;
use App\Http\Controllers\RapportMensuelController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/services/by-direction/{directionId}', [ServiceController::class, 'byDirection'])
    ->name('services.by-direction');

Route::resource('rapport-mensuels', RapportMensuelController::class)->except(['show']);
Route::post('/rapport-mensuels-import', [RapportMensuelController::class, 'import'])
    ->name('rapport-mensuels.import');
Route::get('/rapport-mensuels-export/excel', [RapportMensuelController::class, 'exportExcel'])
    ->name('rapport-mensuels.export.excel');
Route::get('/rapport-mensuels-export/pdf', [RapportMensuelController::class, 'exportPdf'])
    ->name('rapport-mensuels.export.pdf');

Route::resource('parc-informatiques', ParcInformatiqueController::class)->except(['show']);
Route::post('/parc-informatiques-import', [ParcInformatiqueController::class, 'import'])
    ->name('parc-informatiques.import');
Route::get('/parc-informatiques-export/excel', [ParcInformatiqueController::class, 'exportExcel'])
    ->name('parc-informatiques.export.excel');
Route::get('/parc-informatiques-export/pdf', [ParcInformatiqueController::class, 'exportPdf'])
    ->name('parc-informatiques.export.pdf');

Route::resource('rapport-diagnostics', \App\Http\Controllers\RapportDiagnosticController::class)->except(['show']);
Route::get('/rapport-diagnostics/{rapport_diagnostic}/pdf', [\App\Http\Controllers\RapportDiagnosticController::class, 'exportSinglePdf'])
    ->name('rapport-diagnostics.export.single-pdf');
Route::get('/rapport-diagnostics-export/excel', [\App\Http\Controllers\RapportDiagnosticController::class, 'exportExcel'])
    ->name('rapport-diagnostics.export.excel');
Route::get('/rapport-diagnostics-export/pdf', [\App\Http\Controllers\RapportDiagnosticController::class, 'exportPdf'])
    ->name('rapport-diagnostics.export.pdf');
