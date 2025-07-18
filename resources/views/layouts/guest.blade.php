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
					<div class="shrink-0">
						<a href="{{ route('home') }}">
							<img class="size-8" src="https://laracasts.com/images/logo/logo-triangle.svg" alt="Biblioteca" />
						</a>
					</div>
					<!-- LINKS VISÍVEIS PARA TODOS -->
					<div class="block">
						<div class="ml-10 flex items-baseline space-x-4">
							<x-nav-link href="{{ url('/') }}" :active="request()->is('/')">Home</x-nav-link>
							<x-nav-link href="{{ route('livros.index') }}" :active="request()->routeIs('livros.*')">Livros</x-nav-link>
							<x-nav-link href="{{ route('autores.index') }}" :active="request()->routeIs('autores.*')">Autores</x-nav-link>
							<x-nav-link href="{{ route('editoras.index') }}" :active="request()->routeIs('editoras.*')">Editoras</x-nav-link>
						</div>
					</div>
				</div>

				<!-- BOTOES DE LOGIN/REGISTAR SOMENTE PARA VISITANTES -->
				<div class="block">
					<div class="ml-4 flex items-center md:ml-6">
						@auth
							<form method="POST" action="{{ route('logout') }}">
								@csrf
								<button type="submit" class="btn btn-primary text-white">Log out</button>
								<x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        			{{ __('Dashboard') }}
                    			</x-nav-link>
							</form>
						@else
							<x-nav-link href="{{ route('login') }}" :active="request()->is('login')">Entrar</x-nav-link>
							<x-nav-link href="{{ route('register') }}" :active="request()->is('register')">Registar</x-nav-link>
						@endauth
					</div>
				</div>
			</div>
		</div>
	</nav>

	<!-- HEADER -->
	<header class="bg-white shadow-sm">
		<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 sm:flex sm:justify-between sm:items-center">
			<h1 class="text-3xl font-bold tracking-tight text-gray-900">
				{{ $heading ?? 'Livros' }}
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
							class="input input-neutral w-80 bg-white text-gray-900 pr-10"
						/>
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
							class="input input-neutral w-80 bg-white text-gray-900 pr-10"
						/>
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
							class="input input-neutral w-80 bg-white text-gray-900 pr-10"
						/>
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

	<!-- CONTEÚDO PRINCIPAL -->
	<main class="bg-gray-100 text-gray-600 flex-grow container mx-auto px-4 py-6">
		{{ $slot }}
	</main>

	<!-- RODAPÉ -->
	<footer class="footer footer-center p-10 py-6 bg-gray-800 text-base-content text-gray-300">
		<aside>
			<p>Copyright © {{ now()->year }} - Todos os direitos reservados.</p>
		</aside>
		<div>
			<p class="text-sm">Biblioteca - Gerenciamento de Livros, Autores e Editoras</p>
		</div>
	</footer>

	<!-- Livewire Scripts -->
	@livewireScripts
</body>
</html>
