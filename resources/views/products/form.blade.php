<div>
    <label for="name">Nosaukums</label>
    <input id="name" type="text" name="name"
           value="{{ old('name', $product->name ?? '') }}" required>
    @error('name')
        <p class="error">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="quantity">Daudzums</label>
    <input id="quantity" type="number" name="quantity" min="0" step="1"
           value="{{ old('quantity', $product->quantity ?? 0) }}" required>
    @error('quantity')
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
    <label for="expiration_date">Derīguma termiņš</label>
    <input id="expiration_date" type="date" name="expiration_date"
           value="{{ old('expiration_date', isset($product) ? $product->expiration_date?->format('Y-m-d') : '') }}" required>
    @error('expiration_date')
        <p class="error">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="status">Statuss</label>
    <input id="status" type="text" name="status"
           value="{{ old('status', $product->status ?? '') }}" required>
    @error('status')
        <p class="error">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="price">Cena (€)</label>
    <input id="price" type="number" name="price" step="0.01" min="0"
           value="{{ old('price', $product->price ?? '') }}">
    @error('price')
        <p class="error">{{ $message }}</p>
    @enderror
</div>
