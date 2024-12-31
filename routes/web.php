<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\PerputakanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('bar' , function() {
    return view('bar');
});

Route::get('/barcode', [BarcodeController::class, 'showForm'])->name('show.form');
Route::post('/barcode', [BarcodeController::class, 'generateBarcode'])->name('generate.barcode');


Route::get('/perputakan', [PerputakanController::class, 'showForm']);
Route::post('/perputakan', [PerputakanController::class, 'submitForm'])->name('pengunjung');

Route::put('/perputakan/{id}/update-status-keluar', [PerputakanController::class, 'updateStatusKeluar'])->name('perputakan.updateStatusKeluar');


// Route::get('/perputakan/check-nis/{nis}', [PerputakanController::class, 'checkNis']);
Route::post('/perputakan', [PerputakanController::class, 'store']);


Route::post('/check-nis', [PerputakanController::class, 'checkNis']);

Route::post('/handle-entry', [PerputakanController::class, 'handleEntry']);
Route::post('/handle-exit', [PerputakanController::class, 'handleExit']);
Route::get('/form', [PerputakanController::class, 'showForm'])->name('showForm');

Route::post('/check-status', [PerputakanController::class, 'checkStatus'])->name('check-status');
