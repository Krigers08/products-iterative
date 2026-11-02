<?php

namespace App\Models;

use App\Exceptions\InsufficientQuantityException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'description',
        'expiration_date',
        'status',
        'price',
    ];

    /**
     * Increase the quantity by the given amount (delivery).
     *
     * @return int The updated quantity.
     *
     * @throws InvalidArgumentException
     */
    public function increaseQuantity(int $amount): int
    {
        $this->assertPositiveAmount($amount);

        return DB::transaction(function () use ($amount): int {
            $product = $this->lockForQuantityChange();

            $product->increment('quantity', $amount);

            return $this->syncQuantityFrom($product);
        });
    }

    /**
     * Decrease the quantity by the given amount (purchase/redemption).
     *
     * @return int The updated quantity.
     *
     * @throws InvalidArgumentException
     * @throws InsufficientQuantityException
     */
    public function decreaseQuantity(int $amount): int
    {
        $this->assertPositiveAmount($amount);

        return DB::transaction(function () use ($amount): int {
            $product = $this->lockForQuantityChange();

            if ($product->quantity < $amount) {
                throw new InsufficientQuantityException($product->quantity);
            }

            $product->decrement('quantity', $amount);

            return $this->syncQuantityFrom($product);
        });
    }

    /**
     * Set the quantity to an absolute value through the quantity operations.
     *
     * @return int The updated quantity.
     *
     * @throws InvalidArgumentException
     */
    public function setQuantity(int $quantity): int
    {
        if ($quantity < 0) {
            throw new InvalidArgumentException('Daudzums nevar būt negatīvs.');
        }

        return DB::transaction(function () use ($quantity): int {
            $product = $this->lockForQuantityChange();

            $difference = $quantity - $product->quantity;

            if ($difference > 0) {
                return $this->increaseQuantity($difference);
            }

            if ($difference < 0) {
                return $this->decreaseQuantity(abs($difference));
            }

            return $this->syncQuantityFrom($product);
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    private function assertPositiveAmount(int $amount): void
    {
        if ($amount < 1) {
            throw new InvalidArgumentException('Daudzumam jābūt pozitīvam veselam skaitlim.');
        }
    }

    private function lockForQuantityChange(): self
    {
        return self::query()->lockForUpdate()->findOrFail($this->getKey());
    }

    private function syncQuantityFrom(self $product): int
    {
        $product->refresh();

        $this->setAttribute('quantity', $product->quantity);

        return $product->quantity;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'expiration_date' => 'date',
            'price' => 'decimal:2',
        ];
    }
}
