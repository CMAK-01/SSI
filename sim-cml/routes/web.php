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
