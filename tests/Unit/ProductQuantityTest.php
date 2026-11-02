<?php

use App\Exceptions\InsufficientQuantityException;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('increaseQuantity', function () {
    it('increases the quantity and returns the new value', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        expect($product->increaseQuantity(4))->toBe(14)
            ->and($product->quantity)->toBe(14)
            ->and($product->refresh()->quantity)->toBe(14);
    });

    it('increases the quantity from zero', function () {
        $product = Product::factory()->create(['quantity' => 0]);

        expect($product->increaseQuantity(5))->toBe(5)
            ->and($product->refresh()->quantity)->toBe(5);
    });

    it('uses the current database value when the model is stale', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        Product::query()->whereKey($product->getKey())->update(['quantity' => 20]);

        expect($product->increaseQuantity(5))->toBe(25)
            ->and($product->quantity)->toBe(25)
            ->and($product->refresh()->quantity)->toBe(25);
    });

    it('rejects a zero amount', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        expect(fn () => $product->increaseQuantity(0))
            ->toThrow(InvalidArgumentException::class, 'Daudzumam jābūt pozitīvam veselam skaitlim.');

        expect($product->refresh()->quantity)->toBe(10);
    });

    it('rejects a negative amount', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        expect(fn () => $product->increaseQuantity(-3))
            ->toThrow(InvalidArgumentException::class, 'Daudzumam jābūt pozitīvam veselam skaitlim.');

        expect($product->refresh()->quantity)->toBe(10);
    });
});

describe('decreaseQuantity', function () {
    it('decreases the quantity and returns the new value', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        expect($product->decreaseQuantity(4))->toBe(6)
            ->and($product->quantity)->toBe(6)
            ->and($product->refresh()->quantity)->toBe(6);
    });

    it('allows decreasing the quantity to zero', function () {
        $product = Product::factory()->create(['quantity' => 5]);

        expect($product->decreaseQuantity(5))->toBe(0)
            ->and($product->refresh()->quantity)->toBe(0);
    });

    it('uses the current database value when the model is stale', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        Product::query()->whereKey($product->getKey())->update(['quantity' => 20]);

        expect($product->decreaseQuantity(5))->toBe(15)
            ->and($product->quantity)->toBe(15)
            ->and($product->refresh()->quantity)->toBe(15);
    });

    it('throws when the requested amount exceeds the available quantity', function () {
        $product = Product::factory()->create(['quantity' => 2]);

        expect(fn () => $product->decreaseQuantity(3))
            ->toThrow(
                InsufficientQuantityException::class,
                'Nepietiekams produkta daudzums. Pieejams: 2.',
            );

        expect($product->refresh()->quantity)->toBe(2);
    });

    it('exposes the available quantity on the exception', function () {
        $product = Product::factory()->create(['quantity' => 2]);

        try {
            $product->decreaseQuantity(3);

            $this->fail('InsufficientQuantityException netika izmests.');
        } catch (InsufficientQuantityException $exception) {
            expect($exception->availableQuantity)->toBe(2);
        }
    });

    it('rejects a zero amount', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        expect(fn () => $product->decreaseQuantity(0))
            ->toThrow(InvalidArgumentException::class, 'Daudzumam jābūt pozitīvam veselam skaitlim.');

        expect($product->refresh()->quantity)->toBe(10);
    });

    it('rejects a negative amount', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        expect(fn () => $product->decreaseQuantity(-3))
            ->toThrow(InvalidArgumentException::class, 'Daudzumam jābūt pozitīvam veselam skaitlim.');

        expect($product->refresh()->quantity)->toBe(10);
    });
});

describe('setQuantity', function () {
    it('increases the quantity to a higher value', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        expect($product->setQuantity(25))->toBe(25)
            ->and($product->quantity)->toBe(25)
            ->and($product->refresh()->quantity)->toBe(25);
    });

    it('decreases the quantity to a lower value', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        expect($product->setQuantity(4))->toBe(4)
            ->and($product->quantity)->toBe(4)
            ->and($product->refresh()->quantity)->toBe(4);
    });

    it('keeps the quantity unchanged when the value is the same', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        expect($product->setQuantity(10))->toBe(10)
            ->and($product->refresh()->quantity)->toBe(10);
    });

    it('sets the quantity to zero', function () {
        $product = Product::factory()->create(['quantity' => 5]);

        expect($product->setQuantity(0))->toBe(0)
            ->and($product->refresh()->quantity)->toBe(0);
    });

    it('uses the current database value when the model is stale', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        Product::query()->whereKey($product->getKey())->update(['quantity' => 20]);

        expect($product->setQuantity(12))->toBe(12)
            ->and($product->quantity)->toBe(12)
            ->and($product->refresh()->quantity)->toBe(12);
    });

    it('rejects a negative quantity', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        expect(fn () => $product->setQuantity(-1))
            ->toThrow(InvalidArgumentException::class, 'Daudzums nevar būt negatīvs.');

        expect($product->refresh()->quantity)->toBe(10);
    });
});
