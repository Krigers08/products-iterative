
<x-layout title="Produktu saraksts">
    <h1>Produkti</h1>

    <a href="{{ route('products.create') }}">Pievienot produktu</a>

    @forelse ($products as $product)
        <article>
            <h2>{{ $product->name }}</h2>
            <p>Daudzums: {{ $product->quantity }}</p>
            <p>Derīguma termiņš: {{ $product->expiration_date?->format('d.m.Y') ?? '—' }}</p>
            <p>Statuss: {{ $product->status }}</p>

            @if ($product->price !== null)
                <p>Cena: {{ number_format($product->price, 2) }} €</p>
            @endif

            <a href="{{ route('products.show', $product) }}">Apskatīt</a>
            <a href="{{ route('products.edit', $product) }}">Rediģēt</a>

            <form action="{{ route('products.destroy', $product) }}"
                  method="POST"
                  onsubmit="return confirm('Vai tiešām dzēst produktu?')">
                @csrf
                @method('DELETE')
                <button type="submit">Dzēst</button>
            </form>
        </article>
    @empty
        <p>Produktu vēl nav.</p>
    @endforelse
</x-layout>
