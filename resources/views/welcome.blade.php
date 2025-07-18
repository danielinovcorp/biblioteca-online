<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Online</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">

    <!-- NAVBAR -->
    <div class="navbar bg-base-100 shadow-md px-4">
        <div class="flex-1">
            <a class="text-xl font-bold">📚 Biblioteca Online</a>
        </div>
        <div class="flex-none">
            <a href="{{ route('login') }}" class="btn btn-ghost">Entrar</a>
            <a href="{{ route('register') }}" class="btn btn-primary ml-2">Registrar</a>
        </div>
    </div>

    <!-- HERO -->
    <div class="hero flex-1 bg-base-200">
        <div class="hero-content text-center">
            <div class="max-w-md">
                <h1 class="text-5xl font-bold">Bem-vindo à Biblioteca!</h1>
                <p class="py-6">Explore livros, autores e editoras de forma moderna, rápida e organizada.</p>
                <a href="{{ route('login') }}" class="btn btn-primary">Comece agora</a>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer footer-center p-4 bg-base-300 text-base-content">
        <aside>
            <p>&copy; {{ date('Y') }} Biblioteca Online. Todos os direitos reservados.</p>
        </aside>
    </footer>

</body>
</html>
