<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientQuantityException;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'expiration_date' => 'required|date',
            'status' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
        ]);

        $product = Product::create($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Produkts veiksmīgi izveidots!');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function increaseQuantity(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $validated = $this->validateQuantityChange($request);

        $updatedQuantity = $product->increaseQuantity($validated['amount']);

        $message = "Produkta daudzums palielināts par {$validated['amount']}!";

        if ($request->expectsJson()) {
            return response()->json([
                'quantity' => $updatedQuantity,
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('products.show', $product)
            ->with('success', $message);
    }

    public function decreaseQuantity(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $validated = $this->validateQuantityChange($request);

        try {
            $updatedQuantity = $product->decreaseQuantity($validated['amount']);
        } catch (InsufficientQuantityException $exception) {
            throw ValidationException::withMessages([
                'amount' => $exception->getMessage(),
            ]);
        }

        $message = "Produkta daudzums samazināts par {$validated['amount']}!";

        if ($request->expectsJson()) {
            return response()->json([
                'quantity' => $updatedQuantity,
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('products.show', $product)
            ->with('success', $message);
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'expiration_date' => 'required|date',
            'status' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
        ]);

        $quantity = (int) $validated['quantity'];
        unset($validated['quantity']);

        $product->update($validated);
        $product->setQuantity($quantity);

        return redirect()->route('products.show', $product)
            ->with('success', 'Produkts veiksmīgi atjaunināts!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produkts veiksmīgi dzēsts!');
    }

    /**
     * @return array{amount: int}
     */
    private function validateQuantityChange(Request $request): array
    {
        return $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:1000000'],
        ]);
    }
}
