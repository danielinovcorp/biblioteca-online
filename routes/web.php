<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LivrosExport;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\RequisicaoController;
use App\Http\Controllers\AdminUsuarioController;
use App\Http\Controllers\LivroImportController;
use App\Http\Controllers\Admin\ReviewAdminController;
use App\Http\Controllers\ReviewController;
use App\Models\Livro;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Admin\EncomendasAdminController;

Route::get('/', [HomeController::class, 'index'])->name('home');

/**
 * LIVROS – rotas específicas ANTES da genérica
 */

// Livros relacionados (JSON) p/ modal da index
Route::get('livros/{livro}/relacionados', function (Livro $livro) {
	// related(8) deve devolver uma Eloquent Collection de modelos Livro
	$itens = $livro->related(8)->load(['editora', 'autores']);

	return response()->json(
		$itens->map(function ($l) {
			return [
				'id'           => $l->id,
				'nome'         => $l->nome,
				'isbn'         => $l->isbn,
				'imagem_capa'  => $l->imagem_capa,
				// string simples; o front já normaliza para {nome: ...}
				'editora'      => optional($l->editora)->nome,
				// autores no shape [{id, nome}]
				'autores'      => $l->autores->map(fn($a) => ['id' => $a->id, 'nome' => $a->nome])->values(),
				'preco'        => (float) $l->preco,
				'bibliografia' => $l->bibliografia,
				'disponivel'   => (bool) $l->disponivel,

				// NÃO envie 'url' aqui (Opção B não navega)
			];
		})->values()
	);
})->whereNumber('livro')->name('livros.relacionados');

// Listar reviews ativas (JSON)
Route::get('livros/{livro}/reviews', [ReviewController::class, 'listAtivos'])
	->whereNumber('livro')
	->name('reviews.ativos');

// Rota para obter as MINHAS devoluções de um livro (para o formulário de review)
Route::get('/livros/{livro}/minhas-devolucoes', [RequisicaoController::class, 'minhasDevolvidasPorLivro'])
	->middleware('auth')  // só logado
	->whereNumber('livro')
	->name('livros.minhas-devolucoes');

// (Opcional) Detalhe – mantida, mas com constraint para não conflitar com "exportar", etc.
Route::get('livros/{livro}', [LivroController::class, 'show'])
	->whereNumber('livro')
	->name('livros.show');

/**
 * REST resources
 */
Route::resource('livros', LivroController::class)->except(['show']);
Route::resource('autores', AutorController::class)->except(['show']);
Route::resource('editoras', EditoraController::class)->except(['show', 'create', 'edit']);

/**
 * Exportações
 */
Route::get('/livros/exportar', function (Request $request) {
	return Excel::download(new LivrosExport(
		$request->input('ids'),
		$request->input('editora_id'),
		$request->input('autor_id')
	), 'livros.xlsx');
})->name('livros.export');

Route::get('/autores/export', [AutorController::class, 'export'])->name('autores.export');
Route::get('/editoras/exportar', [EditoraController::class, 'export'])->name('editoras.export');

Route::middleware(['auth'])->group(function () {
	Route::redirect('settings', 'settings/profile');

	Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
	Volt::route('settings/password', 'settings.password')->name('settings.password');
	Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';

Route::middleware([
	'auth:sanctum',
	config('jetstream.auth_session'),
	'verified',
])->group(function () {
	Route::get('/dashboard', function () {
		return view('dashboard');
	})->name('dashboard');
});

/**
 * Requisições (usuário)
 */
Route::middleware(['auth'])->group(function () {
	Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
	Route::get('/requisicoes/criar', [RequisicaoController::class, 'create'])->name('requisicoes.create');
	Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');

	Route::post('/livros/alertar-disponibilidade', [LivroController::class, 'alertarDisponibilidade'])
		->name('livros.alertar-disponibilidade');
});

/**
 * Área Admin
 */
Route::middleware(['auth', IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/usuarios', [AdminUsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [AdminUsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{user}/edit', [AdminUsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{user}', [AdminUsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{user}', [AdminUsuarioController::class, 'destroy'])->name('usuarios.destroy');
});

Route::post('/requisicoes/{requisicao}/confirmar-devolucao', [RequisicaoController::class, 'confirmarDevolucao'])
	->middleware(['auth']) // ou ['auth', 'is_admin'] se tiver
	->name('requisicoes.confirmar-devolucao');

Route::middleware(['auth', IsAdmin::class])->group(function () {
	Route::get('/importar-livros', [LivroImportController::class, 'index'])->name('livros.importar');
	Route::post('/importar-livros', [LivroImportController::class, 'pesquisar'])->name('livros.pesquisar');
	Route::post('/importar-livros/salvar', [LivroImportController::class, 'salvar'])->name('livros.importar.salvar');
});

Route::middleware(['auth', IsAdmin::class])
	->prefix('admin')->name('admin.')
	->group(function () {
		Route::get('/reviews', [ReviewAdminController::class, 'index'])->name('reviews.index');
		Route::get('/reviews/{review}', [ReviewAdminController::class, 'show'])->name('reviews.show');
		Route::patch('/reviews/{review}/status', [ReviewAdminController::class, 'updateStatus'])->name('reviews.updateStatus');
	});

/**
 * Reviews (cidadão)
 */
Route::post('/requisicoes/{requisicao}/reviews', [ReviewController::class, 'store'])
	->middleware(['auth'])
	->name('reviews.store');

Route::middleware(['auth'])->group(function () {
    // Carrinho
    Route::get('carrinho',            [CarrinhoController::class, 'index'])->name('carrinho.index');
    Route::post('livros/{livro}/add', [CarrinhoController::class, 'add'])->name('carrinho.add');
    Route::patch('carrinho/{livro}',  [CarrinhoController::class, 'update'])->name('carrinho.update');
    Route::delete('carrinho/{livro}', [CarrinhoController::class, 'remove'])->name('carrinho.remove');
    Route::delete('carrinho',         [CarrinhoController::class, 'clear'])->name('carrinho.clear');

    // Checkout (passo 2: morada  → passo 3: pagamento)
    Route::get('checkout/morada',   [CheckoutController::class, 'showMorada'])->name('checkout.morada');
    Route::post('checkout/morada',  [CheckoutController::class, 'storeMorada'])->name('checkout.morada.store');
    Route::get('checkout/sucesso',  [CheckoutController::class, 'sucesso'])->name('checkout.sucesso');
    Route::get('checkout/cancelado',[CheckoutController::class, 'cancelado'])->name('checkout.cancelado');
});

// Stripe webhook (sem auth)
Route::post('stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Admin – encomendas
Route::middleware(['auth', IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('encomendas', [EncomendasAdminController::class, 'index'])->name('encomendas.index');
    Route::get('encomendas/{encomenda}', [EncomendasAdminController::class, 'show'])->name('encomendas.show');
});
