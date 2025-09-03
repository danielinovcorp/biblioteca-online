<x-guest-layout>
    <x-slot name="heading">📦 Encomendas (Admin)</x-slot>

    <div class="max-w-6xl mx-auto p-4 md:p-8">
        <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="form-control">
                <label class="label"><span class="label-text">Estado</span></label>
                <select name="estado" class="bg-white select select-bordered">
                    <option value="">Todos</option>
                    <option value="pendente" @selected(request('estado')==='pendente')>Pendente</option>
                    <option value="paga" @selected(request('estado')==='paga')>Paga</option>
                </select>
            </div>
            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text">Pesquisar</span></label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="ID, nome ou email"
                       class="bg-white input input-bordered w-full" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text">&nbsp;</span></label>
                <button class="btn btn-primary w-full">Filtrar</button>
            </div>
        </form>

        <div class="overflow-x-auto w-full">
            <table class="table w-full">
                <thead class="bg-gray-700 text-white font-semibold">
                    <tr>
                        <th>#</th>
                        <th>Utilizador</th>
                        <th>Email</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                @forelse($encomendas as $e)
                    <tr class="hover text-neutral odd:bg-gray-100 even:bg-white">
                        <td>{{ $e->id }}</td>
                        <td>{{ $e->user->name ?? '-' }}</td>
                        <td>{{ $e->user->email ?? '-' }}</td>
                        <td>{{ number_format($e->total,2,',','.') }} €</td>
                        <td>
                            <span class="badge {{ $e->estado==='paga' ? 'badge-success' : 'badge-ghost' }}">
                                {{ ucfirst($e->estado) }}
                            </span>
                        </td>
                        <td>{{ $e->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.encomendas.show', $e) }}" class="btn btn-sm">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-8 opacity-70">Sem resultados</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $encomendas->links() }}
        </div>
    </div>
</x-guest-layout>
