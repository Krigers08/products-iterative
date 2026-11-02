<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a product through the form and shows it on the show and index pages', function () {
    $this->get(route('products.create'))
        ->assertOk()
        ->assertSee('Jauns produkts')
        ->assertSee('name="name"', false)
        ->assertSee('name="quantity"', false);

    $this->followingRedirects()
        ->post(route('products.store'), [
            'name' => 'Integrācijas produkts',
            'quantity' => 8,
            'description' => 'Pilna scenārija produkts',
            'expiration_date' => '2027-05-20',
            'status' => 'available',
            'price' => 4.50,
        ])
        ->assertOk()
        ->assertSee('Produkts veiksmīgi izveidots!')
        ->assertSee('Integrācijas produkts')
        ->assertSee('Pilna scenārija produkts')
        ->assertSee('20.05.2027')
        ->assertSee('data-quantity-value>8</span>', false);

    $product = Product::firstWhere('name', 'Integrācijas produkts');

    expect($product)->not->toBeNull()
        ->and($product->quantity)->toBe(8)
        ->and($product->description)->toBe('Pilna scenārija produkts')
        ->and($product->expiration_date->toDateString())->toBe('2027-05-20')
        ->and($product->status)->toBe('available')
        ->and((float) $product->price)->toBe(4.50);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Integrācijas produkts')
        ->assertSee('Daudzums: 8')
        ->assertSee('Derīguma termiņš: 20.05.2027');
});

it('edits a product through the form and shows the updated values', function () {
    $product = Product::factory()->create([
        'name' => 'Vecais nosaukums',
        'quantity' => 5,
        'description' => 'Vecais apraksts',
        'status' => 'available',
    ]);

    $this->get(route('products.edit', $product))
        ->assertOk()
        ->assertSee('Rediģēt produktu')
        ->assertSee('value="Vecais nosaukums"', false)
        ->assertSee('Vecais apraksts');

    $this->followingRedirects()
        ->put(route('products.update', $product), [
            'name' => 'Jaunais nosaukums',
            'quantity' => 12,
            'description' => 'Jauns apraksts',
            'expiration_date' => '2028-01-10',
            'status' => 'out_of_stock',
            'price' => 9.99,
        ])
        ->assertOk()
        ->assertSee('Produkts veiksmīgi atjaunināts!')
        ->assertSee('Jaunais nosaukums')
        ->assertSee('Jauns apraksts')
        ->assertSee('out_of_stock')
        ->assertSee('10.01.2028')
        ->assertSee('data-quantity-value>12</span>', false);

    expect($product->refresh())
        ->name->toBe('Jaunais nosaukums')
        ->quantity->toBe(12)
        ->description->toBe('Jauns apraksts')
        ->status->toBe('out_of_stock')
        ->and($product->expiration_date->toDateString())->toBe('2028-01-10');

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Jaunais nosaukums')
        ->assertSee('Daudzums: 12');
});

it('increases the quantity from the product page and persists the change', function () {
    $product = Product::factory()->create(['quantity' => 7]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Pašreizējais daudzums:')
        ->assertSee('data-quantity-value>7</span>', false);

    $this->followingRedirects()
        ->post(route('products.quantity.increase', $product), ['amount' => 6])
        ->assertOk()
        ->assertSee('Produkta daudzums palielināts par 6!')
        ->assertSee('data-quantity-value>13</span>', false);

    expect($product->refresh()->quantity)->toBe(13);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Daudzums: 13');
});

it('decreases the quantity from the product page and persists the change', function () {
    $product = Product::factory()->create(['quantity' => 10]);

    $this->followingRedirects()
        ->post(route('products.quantity.decrease', $product), ['amount' => 4])
        ->assertOk()
        ->assertSee('Produkta daudzums samazināts par 4!')
        ->assertSee('data-quantity-value>6</span>', false);

    expect($product->refresh()->quantity)->toBe(6);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Daudzums: 6');
});

it('rejects a decrease larger than the stock and keeps the product unchanged', function () {
    $product = Product::factory()->create(['quantity' => 3]);

    $this->from(route('products.show', $product))
        ->followingRedirects()
        ->post(route('products.quantity.decrease', $product), ['amount' => 5])
        ->assertOk()
        ->assertSee('flash-errors', false)
        ->assertSee('Lūdzu, izlabojiet šādas kļūdas:')
        ->assertSee('Nepietiekams produkta daudzums. Pieejams: 3.');

    expect($product->refresh()->quantity)->toBe(3);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Daudzums: 3');
});

it('handles quantity changes through ajax and renders the persisted values on the next page load', function () {
    $product = Product::factory()->create(['quantity' => 10]);

    $this->get(route('products.show', $product))->assertOk();

    $this->postJson(route('products.quantity.increase', $product), ['amount' => 3])
        ->assertOk()
        ->assertJsonPath('quantity', 13)
        ->assertJsonPath('message', 'Produkta daudzums palielināts par 3!');

    $this->postJson(route('products.quantity.decrease', $product), ['amount' => 1])
        ->assertOk()
        ->assertJsonPath('quantity', 12)
        ->assertJsonPath('message', 'Produkta daudzums samazināts par 1!');

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('data-quantity-value>12</span>', false);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Daudzums: 12');
});

it('returns ajax validation errors when the requested amount exceeds the stock', function () {
    $product = Product::factory()->create(['quantity' => 2]);

    $this->postJson(route('products.quantity.decrease', $product), ['amount' => 5])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'amount' => 'Nepietiekams produkta daudzums. Pieejams: 2.',
        ]);

    expect($product->refresh()->quantity)->toBe(2);
});

it('disables the decrease control for an empty product and enables it after a delivery', function () {
    $product = Product::factory()->create(['quantity' => 0]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Izpirkšana (−)')
        ->assertSee('disabled', false);

    $this->followingRedirects()
        ->post(route('products.quantity.increase', $product), ['amount' => 2])
        ->assertOk()
        ->assertSee('Produkta daudzums palielināts par 2!')
        ->assertDontSee('disabled', false);

    expect($product->refresh()->quantity)->toBe(2);
});
