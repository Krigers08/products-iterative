<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the product index lists products', function () {
    $products = Product::factory()->count(3)->create();

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee($products->first()->name);
});

test('the product create page is displayed', function () {
    $this->get(route('products.create'))
        ->assertOk()
        ->assertSee('Jauns produkts');
});

test('a product can be created', function () {
    $this->post(route('products.store'), [
        'name' => 'Jauns produkts',
        'description' => 'Produkta apraksts',
        'price' => 12.5,
    ])->assertRedirect();

    $product = Product::firstWhere('name', 'Jauns produkts');

    expect($product)->not->toBeNull()
        ->and($product->description)->toBe('Produkta apraksts')
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
        'description' => 'Jauns apraksts',
        'price' => 20,
    ])->assertRedirect(route('products.show', $product));

    expect($product->refresh())
        ->name->toBe('Atjaunināts produkts')
        ->description->toBe('Jauns apraksts');
});

test('a product requires valid data', function () {
    $this->post(route('products.store'), [
        'name' => '',
        'price' => 'nav-skaitlis',
    ])->assertSessionHasErrors(['name', 'price']);
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
