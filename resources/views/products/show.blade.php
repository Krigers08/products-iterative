<x-layout :title="$product->name">
    <h1>{{ $product->name }}</h1>

    <p>{{ $product->description }}</p>
    <p>Daudzums: {{ $product->quantity }}</p>
    <p>Derīguma termiņš: {{ $product->expiration_date?->format('d.m.Y') ?? '—' }}</p>
    <p>Statuss: {{ $product->status }}</p>

    @if ($product->price !== null)
        <p>Cena: {{ number_format($product->price, 2) }} €</p>
    @endif

    <section class="quantity-controls" aria-labelledby="quantity-heading">
        <h2 id="quantity-heading">Daudzuma maiņa</h2>
        <p>Pašreizējais daudzums: <strong>{{ $product->quantity }}</strong></p>

        <form action="{{ route('products.quantity.increase', $product) }}" method="POST">
            @csrf

            <label for="quantity-amount">Daudzums</label>
            <input id="quantity-amount" type="number" name="amount" min="1" step="1"
                   value="{{ old('amount', 1) }}" required>

            <div class="product-actions">
                <button type="submit" class="btn btn-view">Pievedums (+)</button>
                <button type="submit" class="btn btn-danger"
                        formaction="{{ route('products.quantity.decrease', $product) }}"
                        @disabled($product->quantity === 0)>
                    Izpirkšana (−)
                </button>
            </div>
        </form>
    </section>

    <a href="{{ route('products.edit', $product) }}">Rediģēt</a>
    <a href="{{ route('products.index') }}">Atpakaļ uz sarakstu</a>
</x-layout>
