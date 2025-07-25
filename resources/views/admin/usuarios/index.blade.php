<x-guest-layout>
	<x-slot name="heading">
		👥 Gestão de Utilizadores
	</x-slot>

	<div class="w-full mx-auto px-16">
		<div class="bg-white shadow rounded-lg w-full overflow-x-auto pt-8 pb-6">

			{{-- BOTÃO DE NOVO --}}
			<div class="flex justify-between items-end px-6 mb-4">
				<form method="GET" action="{{ route('admin.usuarios.index') }}" class="flex gap-4">
					<select name="role" class="select select-bordered bg-gray-100 text-gray-900">
						<option value="">Todos os Perfis</option>
						<option value="admin" @selected(request('role')==='admin' )>Admin</option>
						<option value="cidadao" @selected(request('role')==='cidadao' )>Cidadão</option>
					</select>
					<button type="submit" class="btn btn-outline btn-info">Filtrar</button>
					<a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline btn-error">Limpar</a>
				</form>

				<button id="mostrar-formulario" class="btn btn-primary"><i class="fa-solid fa-circle-plus"></i> Utilizador</button>
			</div>

			{{-- FORMULÁRIO OCULTO --}}
			<form method="POST" action="{{ route('admin.usuarios.store') }}" id="formulario-novo" class="hidden px-6 space-y-6 mb-8">
				@csrf

				{{-- LINHA 1: Nome + Perfil --}}
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<input type="text" name="name" placeholder="Nome"
						class="input w-full border border-gray-300 bg-gray-100 text-gray-900 placeholder:text-gray-700 focus:border-blue-500 focus:outline-none" required>

					<select name="role"
						class="select w-full border border-gray-300 bg-gray-100 text-gray-900 focus:border-blue-500 focus:outline-none" required>
						<option value="">Escolha o perfil</option>
						<option value="admin">Admin</option>
						<option value="cidadao">Cidadão</option>
					</select>
				</div>

				{{-- LINHA 2: Email, Senha, Confirmar --}}
				<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
					<input type="email" name="email" placeholder="Email"
						class="input w-full border border-gray-300 bg-gray-100 text-gray-900 placeholder:text-gray-700 focus:border-blue-500 focus:outline-none" required>

					<input type="password" name="password" placeholder="Senha"
						class="input w-full border border-gray-300 bg-gray-100 text-gray-900 placeholder:text-gray-700 focus:border-blue-500 focus:outline-none" required>

					<input type="password" name="password_confirmation" placeholder="Confirmar Senha"
						class="input w-full border border-gray-300 bg-gray-100 text-gray-900 placeholder:text-gray-700 focus:border-blue-500 focus:outline-none" required>
				</div>

				<div class="text-center">
					<button type="submit" class="btn btn-success hover:bg-emerald-500 text-white px-6">
						💾 Salvar
					</button>
				</div>
			</form>


			{{-- FUNÇÃO DE ORDENAR --}}
			@php
			function sort_link_user($field, $label, $currentField, $currentDirection) {
			$newDirection = ($currentField === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
			$icon = $currentField === $field ? ($currentDirection === 'asc' ? '▲' : '▼') : '';
			$query = request()->except(['page', 'sort', 'direction']);
			$query['sort'] = $field;
			$query['direction'] = $newDirection;
			$url = route('admin.usuarios.index', $query);
			return "<a href='{$url}' class='hover:underline font-medium'>{$label} {$icon}</a>";
			}
			@endphp

			{{-- TABELA DE USUÁRIOS --}}
			<table class="table w-full">
				<thead class="bg-gray-700 text-white font-semibold">
					<tr>
						<th>Foto</th>
						<th>{!! sort_link_user('name', 'Nome', $sortField, $sortDirection) !!}</th>
						<th>{!! sort_link_user('email', 'Email', $sortField, $sortDirection) !!}</th>
						<th>{!! sort_link_user('role', 'Perfil', $sortField, $sortDirection) !!}</th>
						<th class="text-center">Ações</th>
					</tr>
				</thead>
				<tbody>
					@foreach($usuarios as $user)
					@if(request('edit') == $user->id)
					{{-- LINHA EM MODO DE EDIÇÃO --}}
					<tr class="bg-yellow-50">
						<form method="POST" action="{{ route('admin.usuarios.update', $user->id) }}">
							@csrf
							@method('PUT')
							<td>
								<img src="{{ $user->profile_photo_path ? $user->profile_photo_url : asset('images/default-user.png') }}"
									alt="Foto de {{ $user->name }}"
									class="w-[50px] h-[50px] rounded-full object-cover shadow" />
							</td>
							<td><input type="text" name="name" value="{{ $user->name }}" class="input input-bordered w-full bg-white text-black" required></td>
							<td><input type="email" name="email" value="{{ $user->email }}" class="input input-bordered w-full bg-white text-black" required></td>
							<td>
								<select name="role" class="select select-bordered w-full bg-white text-black" required>
									<option value="admin" @selected($user->role === 'admin')>Admin</option>
									<option value="cidadao" @selected($user->role === 'cidadao')>Cidadão</option>
								</select>
							</td>
							<td class="whitespace-nowrap space-x-2 text-center">
								<button type="submit" class="btn btn-sm btn-ghost hover:bg-gray-100">💾</button>
								<a href="{{ route('admin.usuarios.index', request()->except('edit')) }}" class="btn btn-sm btn-ghost hover:bg-gray-100">❌</a>
							</td>
						</form>
					</tr>
					@else
					{{-- LINHA NORMAL --}}
					<tr class="hover text-neutral odd:bg-gray-100 even:bg-white">
						<td>
							<img src="{{ $user->profile_photo_path ? $user->profile_photo_url : asset('images/default-user.png') }}"
								alt="Foto de {{ $user->name }}"
								class="w-[50px] h-[50px] rounded-full object-cover shadow" />
						</td>
						<td class="font-medium">{{ $user->name }}</td>
						<td>{{ $user->email }}</td>
						<td class="{{ $user->role === 'admin' ? 'text-red-600 font-bold' : 'text-blue-600' }}">
							{{ ucfirst($user->role) }}
						</td>
						<td class="space-x-2 whitespace-nowrap text-center">
							<a href="{{ route('admin.usuarios.index', array_merge(request()->query(), ['edit' => $user->id])) }}"
								class="btn btn-sm btn-ghost hover:bg-gray-100">✏️</a>

							<form method="POST" action="{{ route('admin.usuarios.destroy', $user->id) }}" class="inline">
								@csrf
								@method('DELETE')
								<button type="submit" onclick="return confirm('Deseja excluir este utilizador?')" class="btn btn-sm btn-ghost hover:bg-gray-100">🗑️</button>
							</form>
						</td>
					</tr>
					@endif
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

	{{-- JS: Mostrar formulário --}}
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const btnMostrar = document.getElementById('mostrar-formulario');
			const form = document.getElementById('formulario-novo');
			btnMostrar.addEventListener('click', () => {
				form.classList.toggle('hidden');
			});
		});
	</script>
</x-guest-layout>