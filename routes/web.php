<?php

use Illuminate\Support\Facades\Route;
use Laranova\Http\Controllers\LaranovaController;

Route::get('/', [LaranovaController::class, 'index'])->name('index');
Route::post('/resolve', [LaranovaController::class, 'resolve'])->name('resolve');
Route::get('/history', [LaranovaController::class, 'history'])->name('history');
Route::post('/history', [LaranovaController::class, 'storeHistory'])->name('history.store');
Route::delete('/history', [LaranovaController::class, 'clearHistory'])->name('history.clear');
Route::get('/routes', [LaranovaController::class, 'routes'])->name('routes');
