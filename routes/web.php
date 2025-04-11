<?php

use App\Http\Controllers\ArchivoController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Gestión de archivos (protección por permisos individuales)
Route::middleware(['auth'])->group(function () {
    Route::get('/archivos', [ArchivoController::class, 'index'])
        ->middleware('permission:ver archivos')
        ->name('files.index');

    Route::post('/archivos', [ArchivoController::class, 'store'])
        ->middleware('permission:subir archivos')
        ->name('files.store');

    Route::delete('/archivos/{archivo}', [ArchivoController::class, 'destroy'])
        ->middleware('permission:eliminar archivos')
        ->name('files.destroy');

    Route::get('/archivos/nombres', function () {
        return \App\Models\Archivo::pluck('nombre');
    })->middleware('auth');

    Route::patch('/archivos/{archivo}/renombrar', [ArchivoController::class, 'renombrar'])->name('files.rename');
    Route::post('/archivos/unir', [ArchivoController::class, 'unir'])->name('files.merge');
});
require __DIR__ . '/auth.php';
