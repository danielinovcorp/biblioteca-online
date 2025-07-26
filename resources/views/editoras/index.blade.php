<x-guest-layout>
    <x-slot name="heading">🏢 Editoras</x-slot>

    <div class="w-full mx-auto px-16">
        <div class="bg-white shadow rounded-lg w-full overflow-x-auto pt-8">

            {{-- BARRA DE AÇÕES --}}
            <div class="flex justify-center gap-4 mb-6 px-4">
                @auth
                @if(Auth::user()->isAdmin())
                <form method="GET" action="{{ route('editoras.export') }}" id="export-form" class="flex items-center gap-4">
                    <input type="hidden" name="ids" id="selected-ids" />
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                    <button type="submit" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar Excel</button>
                </form>
                <label for="modal-add-editora" class="btn btn-primary">
                    <i class="fa-solid fa-circle-plus"></i>Editora
                </label>
                @endif
                @endauth
            </div>

            @php
            function sort_link_editora($field, $label, $currentField, $currentDirection) {
                $newDirection = ($currentField === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
                $icon = $currentField === $field ? ($currentDirection === 'asc' ? '▲' : '▼') : '';
                $query = request()->except(['page', 'sort', 'direction']);
                $query['sort'] = $field;
                $query['direction'] = $newDirection;
                $url = route('editoras.index', $query);
                return "<a href='{$url}' class='hover:underline font-medium'>{$label} {$icon}</a>";
            }
            @endphp

            {{-- TABELA DE EDITORAS --}}
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-gray-700 text-white">
                            @auth
                            @if(Auth::user()->isAdmin())
                            <th class="w-10"><input id="checkAll" type="checkbox" class="checkbox checkbox-primary" /></th>
                            @endif
                            @endauth
                            <th>Logótipo</th>
                            <th>{!! sort_link_editora('nome', 'Nome', $sortField, $sortDirection) !!}</th>
                            @auth
                            @if(Auth::user()->isAdmin())
                            <th class="text-center">Ações</th>
                            @endif
                            @endauth
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($editoras as $editora)
                        @if (request('edit') == $editora->id && Auth::user()?->isAdmin())
                        {{-- MODO EDIÇÃO --}}
                        <tr class="bg-yellow-50">
                            <form method="POST" action="{{ route('editoras.update', $editora->id) }}" enctype="multipart/form-data" class="contents">
                                @csrf
                                @method('PUT')

                                <td><input type="checkbox" class="checkbox checkbox-primary row-checkbox" /></td>

                                <td>
                                    <img src="{{ $editora->logotipo ?? asset('images/default-publisher.png') }}"
                                        class="w-[60px] h-[60px] rounded object-contain shadow" />
                                    <input type="file" name="logotipo" class="file-input file-input-bordered file-input-sm w-full mt-1 bg-white/90 border-gray-200 file:bg-gray-100 file:border-gray-300 file:text-gray-700" />
                                </td>

                                <td>
                                    <input type="text" name="nome" value="{{ $editora->nome }}"
                                        class="input input-bordered input-sm w-full bg-white/90 border-gray-200 focus:border-blue-300 focus:ring-1 focus:ring-blue-200" required>
                                </td>

                                <td class="whitespace-nowrap text-center space-x-1">
                                    <button type="submit" class="btn btn-sm btn-ghost hover:bg-gray-100">💾</button>
                                    <a href="{{ route('editoras.index') }}" class="btn btn-sm btn-ghost hover:bg-gray-100">❌</a>
                                </td>
                            </form>
                        </tr>
                        @else
                        {{-- MODO VISUAL --}}
                        <tr class="hover {{ $loop->odd ? 'bg-gray-50' : 'bg-white' }}" data-id="{{ $editora->id }}">
                            @auth
                            @if(Auth::user()->isAdmin())
                            <td><input type="checkbox" class="checkbox checkbox-primary row-checkbox" data-id="{{ $editora->id }}" /></td>
                            @endif
                            @endauth

                            <td>
                                <img src="{{ $editora->logotipo ?? asset('images/default-publisher.png') }}"
                                    alt="Logótipo de {{ $editora->nome }}"
                                    class="w-[60px] h-[60px] rounded object-contain shadow" />
                            </td>

                            <td class="font-medium">{{ $editora->nome }}</td>

                            @auth
                            @if(Auth::user()->isAdmin())
                            <td class="whitespace-nowrap text-center space-x-1">
                                <a href="{{ route('editoras.index', ['edit' => $editora->id]) }}" class="btn btn-sm btn-ghost hover:bg-gray-100">✏️</a>
                                <form method="POST" action="{{ route('editoras.destroy', $editora->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Deseja excluir esta editora?')" class="btn btn-sm btn-ghost hover:bg-gray-100">🗑️</button>
                                </form>
                            </td>
                            @endif
                            @endauth
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL ADICIONAR EDITORA --}}
    @auth
    @if(Auth::user()->isAdmin())
    <input type="checkbox" id="modal-add-editora" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box w-11/12 max-w-2xl" data-theme="light">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <i class="fas fa-building-circle-arrow-right"></i> Adicionar Editora
            </h3>

            <form method="POST" action="{{ route('editoras.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 gap-4 text-left">
                    {{-- NOME --}}
                    <div>
                        <label class="label">
                            <span class="label-text">Nome da Editora</span>
                        </label>
                        <input type="text" name="nome" class="input input-bordered w-full" required>
                    </div>

                    {{-- LOGOTIPO --}}
                    <div>
                        <label class="label">
                            <span class="label-text">Logótipo</span>
                        </label>
                        <input type="file" name="logotipo" class="file-input file-input-bordered w-full">
                    </div>

                    {{-- BOTÕES --}}
                    <div class="modal-action justify-end">
                        <label for="modal-add-editora" class="btn btn-ghost">Cancelar</label>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endauth

    {{-- SCRIPT PARA CHECKBOX --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('checkAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const exportForm = document.getElementById('export-form');
            const selectedIdsInput = document.getElementById('selected-ids');

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    rowCheckboxes.forEach(cb => cb.checked = checkAll.checked);
                });
            }

            if (exportForm) {
                exportForm.addEventListener('submit', function(e) {
                    const selected = Array.from(rowCheckboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.closest('tr').dataset.id);
                    selectedIdsInput.value = selected.join(',');
                });
            }
        });
    </script>
</x-guest-layout>
