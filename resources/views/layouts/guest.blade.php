<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'Biblioteca') }}</title>

	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

	<!-- Scripts -->
	@vite(['resources/css/app.css', 'resources/js/app.js'])

	<!-- Livewire Styles -->
	@livewireStyles
</head>

<body class="bg-base-200 text-base-content min-h-screen flex flex-col">
	<!-- NAVBAR -->
	<nav class="bg-gray-800">
		<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
			<div class="flex h-16 items-center justify-between">
				<div class="flex items-center">
					<!-- Logo -->
					<a href="{{ route('home') }}">
						<img class="size-8" src="{{ asset('images/logo.svg') }}" alt="Biblioteca" />
					</a>

					<!-- Links -->
					<div class="block">
						<div class="ml-10 flex items-baseline space-x-4">
							<x-nav-link href="{{ url('/') }}" :active="request()->is('/')">Home</x-nav-link>
							<x-nav-link href="{{ route('livros.index') }}" :active="request()->routeIs('livros.*')">Livros</x-nav-link>
							<x-nav-link href="{{ route('autores.index') }}" :active="request()->routeIs('autores.*')">Autores</x-nav-link>
							<x-nav-link href="{{ route('editoras.index') }}" :active="request()->routeIs('editoras.*')">Editoras</x-nav-link>
							@auth
							<x-nav-link href="{{ route('requisicoes.index') }}" :active="request()->routeIs('requisicoes.*')">Requisições</x-nav-link>
							@endauth
						</div>
					</div>
				</div>

				<!-- Autenticação -->
				<div class="block">
					<div class="ml-4 flex items-center md:ml-6">
						@auth
						<div class="relative">
							<x-dropdown align="right" width="48">
								<x-slot name="trigger">
									@if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
									<button class="flex items-center text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
										<img class="h-8 w-8 rounded-full object-cover"
											src="{{ Auth::user()->profile_photo_path ? Auth::user()->profile_photo_url : asset('images/default-user.png') }}"
											alt="{{ Auth::user()->name }}" />
										<span class="ml-2 text-gray-300 hidden sm:inline">{{ Auth::user()->name }}</span>
									</button>
									@else
									<span class="inline-flex rounded-md">
										<button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-800 hover:text-white focus:outline-none focus:bg-gray-800">
											{{ Auth::user()->name }}
											<svg class="ml-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
											</svg>
										</button>
									</span>
									@endif
								</x-slot>

								<x-slot name="content">
									<div class="block px-4 py-2 text-xs text-gray-400">
										{{ __('Gerenciar conta') }}
									</div>

									<x-dropdown-link href="{{ route('profile.show') }}">
										<i class="fa-solid fa-gear mr-2"></i> {{ __('Perfil') }}
										@if (Auth::user()->role === 'admin')
										<span class="text-red-600 font-semibold ml-2">(Admin)</span>
										@elseif (Auth::user()->role === 'cidadao')
										<span class="text-green-600 font-semibold ml-2">(Cidadão)</span>
										@endif
									</x-dropdown-link>

									<div class="border-t border-gray-200"></div>

									<form method="POST" action="{{ route('logout') }}" x-data>
										@csrf
										<x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
											<i class="fa-solid fa-right-from-bracket mr-2"></i> {{ __('Log Out') }}
										</x-dropdown-link>
									</form>
								</x-slot>
							</x-dropdown>
						</div>
						@else
						<x-nav-link href="{{ route('login') }}" :active="request()->is('login')">Entrar</x-nav-link>
						<x-nav-link href="{{ route('register') }}" :active="request()->is('register')">Registar</x-nav-link>
						@endauth
					</div>
				</div>
			</div>
		</div>
	</nav>

	<!-- SIDEBAR FIXA COM ÍCONES GRANDES -->
	@auth
	<div class="fixed top-0 left-0 h-screen w-20 bg-gray-800 text-white z-50 flex flex-col items-center py-6">
		<div class="mt-48 flex flex-col space-y-8">
			<a href="{{ route('dashboard') }}" class="text-3xl" title="Dashboard"><i class="fas fa-home"></i></a>
			<a href="{{ route('livros.index') }}" class="text-3xl" title="Livros"><i class="fas fa-book"></i></a>
			<a href="{{ route('autores.index') }}" class="text-3xl" title="Autores"><i class="fas fa-user"></i></a>
			<a href="{{ route('editoras.index') }}" class="text-3xl" title="Editoras"><i class="fas fa-building"></i></a>
			<a href="{{ route('requisicoes.index') }}" class="text-3xl" title="Requisições"><i class="fas fa-file-alt"></i></a>

			@if (Auth::user()->role === 'admin')
			<a href="{{ route('admin.usuarios.index') }}" class="text-3xl" title="Usuários"><i class="fas fa-users"></i></a>
			@endif
		</div>
	</div>

	@endauth


	<!-- HEADER -->
	<header class="bg-white shadow-sm">
		<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 sm:flex sm:justify-between sm:items-center">
			<h1 class="text-3xl font-bold tracking-tight text-gray-900">
				{{ $heading ?? 'Biblioteca Online' }}
			</h1>

			{{-- PESQUISA CONDICIONAL POR ROTA --}}
			@if (request()->routeIs('livros.index'))
			<form method="GET" action="{{ route('livros.index') }}">
				<div class="relative">
					<input
						type="text"
						name="search"
						value="{{ request('search') }}"
						placeholder="Pesquisar por Título ou ISBN"
						class="input input-neutral w-80 bg-white text-gray-900 pr-10" />
					<button type="submit"
						class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black z-10 focus:outline-none">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
							stroke="currentColor" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18.5a7.5 7.5 0 006.15-3.85z" />
						</svg>
					</button>
				</div>
			</form>

			@elseif (request()->routeIs('autores.index'))
			<form method="GET" action="{{ route('autores.index') }}">
				<div class="relative">
					<input
						type="text"
						name="q"
						value="{{ request('q') }}"
						placeholder="Pesquisar por nome do autor"
						class="input input-neutral w-80 bg-white text-gray-900 pr-10" />
					<button type="submit"
						class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black z-10 focus:outline-none">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
							stroke="currentColor" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18.5a7.5 7.5 0 006.15-3.85z" />
						</svg>
					</button>
				</div>
			</form>

			@elseif (request()->routeIs('editoras.index'))
			<form method="GET" action="{{ route('editoras.index') }}">
				<div class="relative">
					<input
						type="text"
						name="q"
						value="{{ request('q') }}"
						placeholder="Pesquisar por nome da editora"
						class="input input-neutral w-80 bg-white text-gray-900 pr-10" />
					<button type="submit"
						class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black z-10 focus:outline-none">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
							stroke="currentColor" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18.5a7.5 7.5 0 006.15-3.85z" />
						</svg>
					</button>
				</div>
			</form>
			@endif
		</div>
	</header>

	<!-- MAIN -->
	<main class="@auth ml-20 @endauth flex-grow bg-gray-100 text-gray-600">
		<div class="flex flex-col items-center min-h-screen text-center px-4 py-6">
			{{ $slot }}
		</div>
	</main>

	<!-- RODAPÉ -->
	<footer class="footer footer-center p-10 py-6 bg-gray-800 text-base-content text-gray-300">
		<aside>
			<p>&copy; {{ now()->year }} - Todos os direitos reservados.</p>
		</aside>
		<div>
			<p class="text-sm">Biblioteca - Gerenciamento de Livros, Autores e Editoras</p>
		</div>
	</footer>

	@livewireScripts
</body>

</html>