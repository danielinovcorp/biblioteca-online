<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequisicaoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $requisicoes = $user->isAdmin()
            ? Requisicao::with('livro', 'user')->latest()->get()
            : $user->requisicoes()->with('livro')->latest()->get();

        return view('requisicoes.index', compact('requisicoes'));
    }

    public function create()
    {
        $livrosDisponiveis = Livro::whereDoesntHave('requisicoes', function ($query) {
            $query->where('status', 'ativa');
        })->get();

        return view('requisicoes.create', compact('livrosDisponiveis'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Verifica limite de 3 requisições ativas
        $ativas = $user->requisicoes()->where('status', 'ativa')->count();
        if ($ativas >= 3) {
            return back()->with('error', 'Você já tem 3 livros requisitados.');
        }

        $request->validate([
            'livro_id' => 'required|exists:livros,id',
            'foto_cidadao' => 'required|image|max:2048',
        ]);

        // Verifica se o livro está ocupado
        $livroOcupado = Requisicao::where('livro_id', $request->livro_id)
            ->where('status', 'ativa')
            ->exists();

        if ($livroOcupado) {
            return back()->with('error', 'Este livro já está requisitado por outro cidadão.');
        }

        // Upload da foto
        $path = $request->file('foto_cidadao')->store('fotos_cidadao', 'public');

        // Número único da requisição
        $numero = 'REQ-' . str_pad(Requisicao::count() + 1, 4, '0', STR_PAD_LEFT);

        // Criação da requisição
        $requisicao = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $request->livro_id,
            'numero' => $numero,
            'foto_cidadao' => $path,
            'status' => 'ativa',
            'data_inicio' => now()->toDateString(),
            'data_fim_prevista' => now()->addDays(5)->toDateString(),
        ]);

        // Futuro: enviar e-mail

        return redirect()->route('requisicoes.index')->with('success', 'Requisição criada com sucesso.');
    }
}
