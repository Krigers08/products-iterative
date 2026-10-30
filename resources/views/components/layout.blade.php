<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Produkti' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="page">
        <header class="site-header">
            <h1>Produktu veikals</h1>
            <p>Vienkāršs produktu pārvaldības rīks</p>
        </header>

        <aside class="site-nav" aria-label="Galvenā navigācija">
            <nav>
                <ul class="nav-list">
                    <li>
                        <a href="{{ route('products.index') }}">Produkti</a>
                    </li>
                    <li>
                        <a href="{{ route('products.create') }}">Pievienot produktu</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="site-main">
            @if (session('success'))
                <p class="success">{{ session('success') }}</p>
            @endif

            {{ $slot }}
        </main>

        <aside class="site-info" aria-label="Informācija">
            <h2>Par veikalu</h2>
            <p>Šeit var apskatīt, pievienot, rediģēt un dzēst produktus.</p>
        </aside>

        <footer class="site-footer">
            <p>&copy; {{ date('Y') }} Produktu veikals</p>
        </footer>
    </div>
</body>
</html>
