<x-guest-layout>
	<x-slot name="heading">
		🏠 Home
	</x-slot>
    <div class="text-center">
        <h1 class="text-4xl font-bold mb-4">Bem-vindo à Biblioteca</h1>
        <p class="text-lg mb-6">Gerencie livros, autores e editoras de forma prática e eficiente.</p>

		<div class="mt-8 space-x-8">
            <a href="{{ route('livros.index') }}" class="btn h-20 w-40 btn-xl btn-primary">Livros</a>
            <a href="{{ route('autores.index') }}" class="btn h-20 w-40 btn-xl btn-secondary">Autores</a>
            <a href="{{ route('editoras.index') }}" class="btn h-20 w-40 btn-xl btn-accent">Editoras</a>
        </div>
    </div>
</x-guest-layout>
