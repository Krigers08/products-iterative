
<x-layout title="Jauns produkts">
    <h1>Jauns produkts</h1>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        @include('products.form')

        <button type="submit">Saglabāt</button>
    </form>
</x-layout>
