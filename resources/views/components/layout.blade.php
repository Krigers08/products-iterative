
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Produkti' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav>
        <a href="{{ route('products.index') }}">Produkti</a>
        <a href="{{ route('products.create') }}">Pievienot produktu</a>
    </nav>

    <main>
        @if (session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif

        {{ $slot }}
    </main>
</body>
</html>
