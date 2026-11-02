<x-layout :title="$product->name">
    <h1>{{ $product->name }}</h1>

    <p>{{ $product->description }}</p>
    <p>Daudzums: <span data-quantity-value>{{ $product->quantity }}</span></p>
    <p>Derīguma termiņš: {{ $product->expiration_date?->format('d.m.Y') ?? '—' }}</p>
    <p>Statuss: {{ $product->status }}</p>

    @if ($product->price !== null)
        <p>Cena: {{ number_format($product->price, 2) }} €</p>
    @endif

    <section class="tag-controls" aria-labelledby="tags-heading">
        <h2 id="tags-heading">Birkas</h2>

        @if ($product->tags->isEmpty())
            <p>Produktam vēl nav pievienotu birku.</p>
        @else
            <ul class="tag-list">
                @foreach ($product->tags as $tag)
                    <li class="tag-item">
                        <span>{{ $tag->name }}</span>

                        <form action="{{ route('products.tags.destroy', [$product, $tag]) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger">Noņemt</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif

        <form action="{{ route('products.tags.store', $product) }}" method="POST" class="tag-form">
            @csrf

            <label for="tag-name">Jauna birka</label>
            <input id="tag-name" type="text" name="name" maxlength="50"
                   value="{{ old('name') }}" required>

            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror

            <button type="submit" class="btn btn-view">Pievienot birku</button>
        </form>
    </section>

    <section class="quantity-controls" aria-labelledby="quantity-heading">
        <h2 id="quantity-heading">Daudzuma maiņa</h2>
        <p>Pašreizējais daudzums: <strong data-quantity-value>{{ $product->quantity }}</strong></p>

        <form action="{{ route('products.quantity.increase', $product) }}" method="POST"
              data-quantity-form
              data-quantity="{{ $product->quantity }}"
              data-increase-url="{{ route('products.quantity.increase', $product) }}"
              data-decrease-url="{{ route('products.quantity.decrease', $product) }}">
            @csrf

            <div class="flash" data-quantity-feedback role="status" aria-live="polite" hidden></div>

            <label for="quantity-amount">Daudzums</label>
            <input id="quantity-amount" type="number" name="amount" min="1" step="1"
                   value="{{ old('amount', 1) }}" required>

            <div class="product-actions">
                <button type="submit" class="btn btn-view" data-quantity-direction="increase">
                    Pievedums (+)
                </button>
                <button type="submit" class="btn btn-danger"
                        data-quantity-direction="decrease"
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
