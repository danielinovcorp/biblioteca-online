<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Exports\LivrosExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/livros', [LivroController::class, 'index'])->name('livros.index');
Route::get('/autores', [AutorController::class, 'index'])->name('autores.index');
Route::get('/editoras', [EditoraController::class, 'index'])->name('editoras.index');

Route::get('/livros/exportar', function (Request $request) {
    return Excel::download(new LivrosExport(
        $request->input('ids'),
        $request->input('editora_id'),
        $request->input('autor_id')
    ), 'livros.xlsx');
})->name('livros.export');

Route::get('/autores/export', [AutorController::class, 'export'])->name('autores.export');
Route::get('/editoras/exportar', [EditoraController::class, 'export'])->name('editoras.export');


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
