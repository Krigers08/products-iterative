
<x-layout title="Rediģēt produktu">
    <h1>Rediģēt produktu</h1>

    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

        @include('products.form')

        <button type="submit">Atjaunināt</button>
    </form>
</x-layout>
