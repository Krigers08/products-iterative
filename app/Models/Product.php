<?php

namespace App\Models;

use App\Exceptions\InsufficientQuantityException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
     */
    public function increaseQuantity(int $amount): int
    {
        return DB::transaction(function () use ($amount): int {
            $product = self::query()->lockForUpdate()->findOrFail($this->getKey());

            $product->increment('quantity', $amount);
            $product->refresh();

            $this->setAttribute('quantity', $product->quantity);

            return $product->quantity;
        });
    }

    /**
     * Decrease the quantity by the given amount (purchase/redemption).
     *
     * @return int The updated quantity.
     *
     * @throws InsufficientQuantityException
     */
    public function decreaseQuantity(int $amount): int
    {
        return DB::transaction(function () use ($amount): int {
            $product = self::query()->lockForUpdate()->findOrFail($this->getKey());

            if ($product->quantity < $amount) {
                throw new InsufficientQuantityException($product->quantity);
            }

            $product->decrement('quantity', $amount);
            $product->refresh();

            $this->setAttribute('quantity', $product->quantity);

            return $product->quantity;
        });
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
