<x-layout :title="$product->name">
    <h1>{{ $product->name }}</h1>

    <p>{{ $product->description }}</p>
    <p>Daudzums: {{ $product->quantity }}</p>
    <p>Derīguma termiņš: {{ $product->expiration_date?->format('d.m.Y') ?? '—' }}</p>
    <p>Statuss: {{ $product->status }}</p>

    @if ($product->price !== null)
        <p>Cena: {{ number_format($product->price, 2) }} €</p>
    @endif

    <a href="{{ route('products.edit', $product) }}">Rediģēt</a>
    <a href="{{ route('products.index') }}">Atpakaļ uz sarakstu</a>
</x-layout>
