<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Encomenda;
use Illuminate\Http\Request;

class EncomendasAdminController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->string('estado')->toString(); // 'paga' | 'pendente' | ''
        $q      = $request->string('q')->toString();      // busca por nome/email/id

        $query = Encomenda::query()->with(['user'])->latest();

        if (in_array($estado, ['paga','pendente'])) {
            $query->where('estado', $estado);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('id', $q)
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $encomendas = $query->paginate(20)->withQueryString();

        return view('admin.encomendas.index', compact('encomendas', 'estado', 'q'));
    }

    public function show(Encomenda $encomenda)
    {
        $encomenda->load(['user', 'livros']);
        return view('admin.encomendas.show', compact('encomenda'));
    }
}
