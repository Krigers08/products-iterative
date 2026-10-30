<div>
    <label for="name">Nosaukums</label>
    <input id="name" type="text" name="name"
           value="{{ old('name', $product->name ?? '') }}" required>
    @error('name')
        <p class="error">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="description">Apraksts</label>
    <textarea id="description" name="description">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <p class="error">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="price">Cena (€)</label>
    <input id="price" type="number" name="price" step="0.01" min="0"
           value="{{ old('price', $product->price ?? '') }}" required>
    @error('price')
        <p class="error">{{ $message }}</p>
    @enderror
</div>