<?php

namespace App\Http\Controllers;

use App\Models\Carrinho;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{
    public function index(Request $request)
    {
        $carrinho = Carrinho::forUser(Auth::id())->load(['livros']);
        // Renderiza uma view (criaremos a blade já já)
        return view('carrinho.index', [
            'carrinho' => $carrinho,
            'itens'    => $carrinho->livros,
            'total'    => $carrinho->total(),
        ]);
    }

    public function add(Request $request, Livro $livro)
    {
        $request->validate([
            'quantidade' => 'nullable|integer|min:1|max:99',
        ]);
        $qtd = (int) ($request->input('quantidade', 1));

        $carrinho = Carrinho::forUser(Auth::id());
        $carrinho->addLivro($livro, $qtd);

        return back()->with('success', 'Livro adicionado ao carrinho.');
    }

    public function update(Request $request, Livro $livro)
    {
        $request->validate([
            'quantidade' => 'required|integer|min:0|max:99',
        ]);

        $carrinho = Carrinho::forUser(Auth::id());
        $carrinho->setQuantidade($livro, (int)$request->integer('quantidade'));

        return back()->with('success', 'Quantidade atualizada.');
    }

    public function remove(Request $request, Livro $livro)
    {
        $carrinho = Carrinho::forUser(Auth::id());
        $carrinho->removeLivro($livro);

        return back()->with('success', 'Livro removido do carrinho.');
    }

    public function clear(Request $request)
    {
        $carrinho = Carrinho::forUser(Auth::id());
        $carrinho->limpar();

        return back()->with('success', 'Carrinho limpo.');
    }
}
