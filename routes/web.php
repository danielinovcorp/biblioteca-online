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
use App\Http\Controllers\RequisicaoController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AdminUsuarioController;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('livros', LivroController::class)->except(['show']);
Route::resource('autores', AutorController::class)->except(['show']);
Route::resource('editoras', EditoraController::class)->except(['show', 'create', 'edit']);

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

Route::middleware(['auth'])->group(function () {
    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
    Route::get('/requisicoes/criar', [RequisicaoController::class, 'create'])->name('requisicoes.create');
    Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/usuarios', [AdminUsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [AdminUsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{user}/edit', [AdminUsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{user}', [AdminUsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{user}', [AdminUsuarioController::class, 'destroy'])->name('usuarios.destroy');
});


Route::post('/requisicoes/{requisicao}/confirmar-devolucao', [RequisicaoController::class, 'confirmarDevolucao'])
    ->middleware(['auth']) // ou ['auth', 'is_admin'] se tiver
    ->name('requisicoes.confirmar-devolucao');
