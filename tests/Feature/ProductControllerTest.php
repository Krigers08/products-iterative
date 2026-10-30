<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('index', function () {
    it('renders the product list', function () {
        $products = Product::factory()->count(3)->create();

        $this->get(route('products.index'))
            ->assertOk()
            ->assertViewIs('products.index')
            ->assertViewHas(
                'products',
                fn ($viewProducts) => $viewProducts->pluck('id')->contains($products->first()->id)
            );
    });
});

describe('create', function () {
    it('renders the create form', function () {
        $this->get(route('products.create'))
            ->assertOk()
            ->assertViewIs('products.create');
    });
});

describe('store', function () {
    it('creates a product and redirects to its page', function () {
        $response = $this->post(route('products.store'), [
            'name' => 'Testa produkts',
            'quantity' => 10,
            'description' => 'Produkta apraksts',
            'expiration_date' => '2027-01-01',
            'status' => 'available',
            'price' => 9.99,
        ]);

        $product = Product::firstWhere('name', 'Testa produkts');

        expect($product)->not->toBeNull()
            ->and($product->quantity)->toBe(10)
            ->and($product->description)->toBe('Produkta apraksts')
            ->and($product->expiration_date->toDateString())->toBe('2027-01-01')
            ->and($product->status)->toBe('available')
            ->and((float) $product->price)->toBe(9.99);

        $response
            ->assertRedirect(route('products.show', $product))
            ->assertSessionHas('success', 'Produkts veiksmīgi izveidots!');
    });

    it('rejects invalid product data', function () {
        $this->post(route('products.store'), [
            'name' => '',
            'quantity' => -1,
            'expiration_date' => 'nav-datums',
            'status' => '',
            'price' => 'nav-skaitlis',
        ])->assertSessionHasErrors(['name', 'quantity', 'expiration_date', 'status', 'price']);
    });
});

describe('show', function () {
    it('renders a product', function () {
        $product = Product::factory()->create();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertViewIs('products.show')
            ->assertViewHas('product', fn (Product $viewProduct) => $viewProduct->is($product));
    });

    it('returns 404 for an unknown product', function () {
        $this->get(route('products.show', 999999))->assertNotFound();
    });
});

describe('edit', function () {
    it('renders the edit form', function () {
        $product = Product::factory()->create();

        $this->get(route('products.edit', $product))
            ->assertOk()
            ->assertViewIs('products.edit')
            ->assertViewHas('product', fn (Product $viewProduct) => $viewProduct->is($product));
    });
});

describe('update', function () {
    it('updates a product and redirects to its page', function () {
        $product = Product::factory()->create();

        $this->put(route('products.update', $product), [
            'name' => 'Atjaunināts produkts',
            'quantity' => 7,
            'description' => 'Jauns apraksts',
            'expiration_date' => '2027-02-01',
            'status' => 'out_of_stock',
            'price' => 15.50,
        ])
            ->assertRedirect(route('products.show', $product))
            ->assertSessionHas('success', 'Produkts veiksmīgi atjaunināts!');

        expect($product->refresh())
            ->name->toBe('Atjaunināts produkts')
            ->quantity->toBe(7)
            ->description->toBe('Jauns apraksts')
            ->status->toBe('out_of_stock');

        expect($product->expiration_date->toDateString())->toBe('2027-02-01');
    });

    it('rejects invalid product data', function () {
        $product = Product::factory()->create();

        $this->put(route('products.update', $product), [
            'name' => '',
            'quantity' => -1,
            'expiration_date' => 'nav-datums',
            'status' => '',
        ])->assertSessionHasErrors(['name', 'quantity', 'expiration_date', 'status']);
    });
});

describe('destroy', function () {
    it('deletes a product and redirects to the index', function () {
        $product = Product::factory()->create();

        $this->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success', 'Produkts veiksmīgi dzēsts!');

        $this->assertModelMissing($product);
    });
});
