<x-layout :title="$product->name">
    <h1>{{ $product->name }}</h1>

    <p>{{ $product->description }}</p>
    <p>Cena: {{ number_format($product->price, 2) }} €</p>

    <a href="{{ route('products.edit', $product) }}">Rediģēt</a>
    <a href="{{ route('products.index') }}">Atpakaļ uz sarakstu</a>
</x-layout>
