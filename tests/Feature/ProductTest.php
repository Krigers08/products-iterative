<?php

use App\Models\Product;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the product index lists products', function () {
    $products = Product::factory()->count(3)->create();

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee($products->first()->name);
});

test('the product seeder creates at least 20 products', function () {
    $this->seed(ProductSeeder::class);

    expect(Product::count())->toBeGreaterThanOrEqual(20);
});

test('the product create page is displayed', function () {
    $this->get(route('products.create'))
        ->assertOk()
        ->assertSee('Jauns produkts');
});

test('a product can be created', function () {
    $this->post(route('products.store'), [
        'name' => 'Jauns produkts',
        'quantity' => 12,
        'description' => 'Produkta apraksts',
        'expiration_date' => '2026-12-31',
        'status' => 'available',
        'price' => 12.5,
    ])->assertRedirect();

    $product = Product::firstWhere('name', 'Jauns produkts');

    expect($product)->not->toBeNull()
        ->and($product->quantity)->toBe(12)
        ->and($product->description)->toBe('Produkta apraksts')
        ->and($product->expiration_date->toDateString())->toBe('2026-12-31')
        ->and($product->status)->toBe('available')
        ->and((float) $product->price)->toBe(12.5);
});

test('the product show page is displayed', function () {
    $product = Product::factory()->create();

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee($product->name)
        ->assertSee($product->description);
});

test('the product edit page is displayed', function () {
    $product = Product::factory()->create();

    $this->get(route('products.edit', $product))
        ->assertOk()
        ->assertSee($product->name);
});

test('a product can be updated', function () {
    $product = Product::factory()->create();

    $this->put(route('products.update', $product), [
        'name' => 'Atjaunināts produkts',
        'quantity' => 7,
        'description' => 'Jauns apraksts',
        'expiration_date' => '2027-01-15',
        'status' => 'out_of_stock',
        'price' => 20,
    ])->assertRedirect(route('products.show', $product));

    expect($product->refresh())
        ->name->toBe('Atjaunināts produkts')
        ->quantity->toBe(7)
        ->description->toBe('Jauns apraksts')
        ->status->toBe('out_of_stock');

    expect($product->expiration_date->toDateString())->toBe('2027-01-15');
});

test('a product requires valid data', function () {
    $this->post(route('products.store'), [
        'name' => '',
        'quantity' => -1,
        'expiration_date' => 'nav-datums',
        'status' => '',
        'price' => 'nav-skaitlis',
    ])->assertSessionHasErrors(['name', 'quantity', 'expiration_date', 'status', 'price']);
});

test('validation errors are displayed in the flash errors component', function () {
    $this->from(route('products.create'))
        ->followingRedirects()
        ->post(route('products.store'), [])
        ->assertOk()
        ->assertSee('flash-errors', false)
        ->assertSee('Lūdzu, izlabojiet šādas kļūdas:');
});

test('success messages are displayed in the flash success component', function () {
    $this->followingRedirects()
        ->post(route('products.store'), [
            'name' => 'Flash produkts',
            'quantity' => 5,
            'description' => 'Testa apraksts',
            'expiration_date' => '2027-06-30',
            'status' => 'available',
        ])
        ->assertOk()
        ->assertSee('flash-success', false)
        ->assertSee('Produkts veiksmīgi izveidots!');
});

test('a product can be deleted', function () {
    $product = Product::factory()->create();

    $this->delete(route('products.destroy', $product))
        ->assertRedirect(route('products.index'));

    $this->assertModelMissing($product);
});

test('the product pages use the holy grail layout', function () {
    $this->get(route('products.index'))
        ->assertOk()
        ->assertSeeInOrder([
            '<header class="site-header"',
            '<aside class="site-nav"',
            '<main class="site-main"',
            '<aside class="site-info"',
            '<footer class="site-footer"',
        ], false);
});
