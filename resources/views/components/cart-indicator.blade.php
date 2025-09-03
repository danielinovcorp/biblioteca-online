@php
    $hasItems = $count > 0;
@endphp

@if(auth()->check())
    <a href="{{ route('carrinho.index') }}"
       class="btn btn-ghost btn-sm normal-case gap-2"
       title="Abrir carrinho"
       id="cart-indicator">
        {{-- Ícone (usa Font Awesome se estiver carregado; senão, usa emoji) --}}
        @if (class_exists(\Illuminate\Support\Str::class)) {{-- placeholder só pra não dar warning --}}
            <i class="fas fa-shopping-cart"></i>
        @else
            <span>🛒</span>
        @endif

        <span class="hidden sm:inline">Carrinho</span>

        <span class="badge {{ $hasItems ? 'badge-primary' : 'badge-ghost' }}"
              id="cart-count">{{ $count }}</span>
    </a>
@else
    {{-- Visitante: leva para login --}}
    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm normal-case gap-2" title="Entrar para usar o carrinho">
        <i class="fas fa-shopping-cart"></i>
        <span class="hidden sm:inline">Carrinho</span>
        <span class="badge badge-ghost">0</span>
    </a>
@endif
