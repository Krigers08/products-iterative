<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::post('products/{product}/quantity/increase', [ProductController::class, 'increaseQuantity'])
    ->name('products.quantity.increase');
Route::post('products/{product}/quantity/decrease', [ProductController::class, 'decreaseQuantity'])
    ->name('products.quantity.decrease');

Route::post('products/{product}/tags', [ProductController::class, 'attachTag'])
    ->name('products.tags.store');
Route::delete('products/{product}/tags/{tag}', [ProductController::class, 'detachTag'])
    ->name('products.tags.destroy');

Route::resource('products', ProductController::class);
