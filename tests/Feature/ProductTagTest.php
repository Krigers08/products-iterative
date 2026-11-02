<?php

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the tag controls and existing tags on the product page', function () {
    $product = Product::factory()->create();
    $tag = Tag::factory()->create(['name' => 'bio']);
    $product->tags()->attach($tag);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Birkas')
        ->assertSee('bio')
        ->assertSee('Pievienot birku')
        ->assertSee(route('products.tags.store', $product), false)
        ->assertSee(route('products.tags.destroy', [$product, $tag]), false);
});

it('attaches a new tag to a product', function () {
    $product = Product::factory()->create();

    $this->post(route('products.tags.store', $product), ['name' => 'Akcija'])
        ->assertRedirect(route('products.show', $product))
        ->assertSessionHas('success', 'Birka "akcija" pievienota!');

    $tag = Tag::firstWhere('name', 'akcija');

    expect($tag)->not->toBeNull()
        ->and($product->tags()->pluck('tags.id')->all())->toBe([$tag->id]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('akcija');
});

it('attaches the same tag to multiple products without creating duplicates', function () {
    $tag = Tag::factory()->create(['name' => 'bio']);
    $firstProduct = Product::factory()->create();
    $secondProduct = Product::factory()->create();

    $this->post(route('products.tags.store', $firstProduct), ['name' => 'bio'])
        ->assertRedirect(route('products.show', $firstProduct));
    $this->post(route('products.tags.store', $secondProduct), ['name' => 'bio'])
        ->assertRedirect(route('products.show', $secondProduct));

    expect(Tag::count())->toBe(1)
        ->and($tag->products()->count())->toBe(2);

    $this->assertDatabaseCount('product_tag', 2);
});

it('attaches multiple tags to one product', function () {
    $product = Product::factory()->create();

    $this->post(route('products.tags.store', $product), ['name' => 'bio']);
    $this->post(route('products.tags.store', $product), ['name' => 'akcija']);
    $this->post(route('products.tags.store', $product), ['name' => 'jaunums']);

    expect($product->fresh()->tags()->pluck('name')->sort()->values()->all())
        ->toBe(['akcija', 'bio', 'jaunums']);
});

it('keeps existing tags when attaching another tag', function () {
    $product = Product::factory()->create();
    $product->tags()->attach(Tag::factory()->create(['name' => 'bio']));

    $this->post(route('products.tags.store', $product), ['name' => 'akcija'])
        ->assertRedirect(route('products.show', $product));

    expect($product->fresh()->tags()->pluck('name')->sort()->values()->all())
        ->toBe(['akcija', 'bio']);
});

it('does not duplicate a tag that is already attached', function () {
    $product = Product::factory()->create();
    $tag = Tag::factory()->create(['name' => 'bio']);
    $product->tags()->attach($tag);

    $this->post(route('products.tags.store', $product), ['name' => 'bio'])
        ->assertRedirect(route('products.show', $product));

    expect(Tag::count())->toBe(1)
        ->and($product->tags()->count())->toBe(1);

    $this->assertDatabaseCount('product_tag', 1);
});

it('normalizes whitespace and letter case when attaching a tag', function () {
    $product = Product::factory()->create();

    $this->post(route('products.tags.store', $product), ['name' => '  Laba   Cena  '])
        ->assertRedirect(route('products.show', $product));

    expect(Tag::first()->name)->toBe('laba cena')
        ->and($product->tags()->first()->name)->toBe('laba cena');

    $this->post(route('products.tags.store', $product), ['name' => 'laba cena'])
        ->assertRedirect(route('products.show', $product));

    expect(Tag::count())->toBe(1);
});

it('requires a tag name', function () {
    $product = Product::factory()->create();

    $this->from(route('products.show', $product))
        ->post(route('products.tags.store', $product), ['name' => ''])
        ->assertRedirect(route('products.show', $product))
        ->assertSessionHasErrors('name');

    expect(Tag::count())->toBe(0);
});

it('rejects a tag name longer than 50 characters', function () {
    $product = Product::factory()->create();

    $this->post(route('products.tags.store', $product), ['name' => str_repeat('a', 51)])
        ->assertSessionHasErrors('name');

    expect(Tag::count())->toBe(0);
});

it('detaches a tag from a product but keeps it for other products', function () {
    $tag = Tag::factory()->create(['name' => 'bio']);
    $firstProduct = Product::factory()->create();
    $secondProduct = Product::factory()->create();
    $firstProduct->tags()->attach($tag);
    $secondProduct->tags()->attach($tag);

    $this->delete(route('products.tags.destroy', [$firstProduct, $tag]))
        ->assertRedirect(route('products.show', $firstProduct))
        ->assertSessionHas('success', 'Birka "bio" noņemta!');

    expect($firstProduct->fresh()->tags()->count())->toBe(0)
        ->and($secondProduct->fresh()->tags()->count())->toBe(1);

    $this->assertModelExists($tag);
    $this->assertDatabaseCount('product_tag', 1);
});

it('returns 404 when detaching an unknown tag', function () {
    $product = Product::factory()->create();

    $this->delete(route('products.tags.destroy', [$product, 999999]))
        ->assertNotFound();
});

it('escapes tag names on the product page', function () {
    $product = Product::factory()->create();
    $tag = Tag::factory()->create(['name' => '<script>alert(1)</script>']);
    $product->tags()->attach($tag);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false);
});
