<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\IsAdmin;

class AdminUsuarioController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado');
        }

        $query = User::query();

        // Filtro por role (admin / cidadao)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Ordenação
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        // Segurança: só permitir ordenar por campos válidos
        if (!in_array($sortField, ['name', 'email', 'role'])) {
            $sortField = 'name';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $usuarios = $query
            ->with(['requisicoes.livro']) // ← carrega histórico de requisições + livros
            ->orderBy($sortField, $sortDirection)
            ->get();

        return view('admin.usuarios.index', compact('usuarios', 'sortField', 'sortDirection'));
    }


    public function store(Request $request)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,cidadao',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);

        return back()->with('success', 'Utilizador criado com sucesso!');
    }

    public function edit(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acesso negado');
        }

        return view('admin.usuarios.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,cidadao',
        ]);

        $user->update($request->only('name', 'email', 'role'));

        return redirect()->route('admin.usuarios.index')->with('success', 'Utilizador atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acesso negado');
        }

        $user->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Utilizador excluído com sucesso!');
    }
}
