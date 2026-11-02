<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = collect([
            'akcija',
            'bio',
            'jaunums',
            'latvijas produkts',
            'bezmaksas piegāde',
            'vegan',
        ])->map(fn (string $name): Tag => Tag::firstOrCreate(['name' => $name]));

        Product::query()->each(function (Product $product) use ($tags): void {
            $product->tags()->syncWithoutDetaching(
                $tags->random(2)->pluck('id')->all(),
            );
        });
    }
}
