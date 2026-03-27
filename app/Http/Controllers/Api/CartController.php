<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cartItems = $request->user()
            ->cartItems()
            ->with('product.category:id,name,slug')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'data' => [
                'items' => $cartItems,
                'summary' => [
                    'items_count' => $cartItems->count(),
                    'total_quantity' => $cartItems->sum('quantity'),
                    'subtotal' => number_format(
                        $cartItems->sum(fn (CartItem $item) => (float) $item->unit_price * $item->quantity),
                        2,
                        '.',
                        ''
                    ),
                    'currency' => $cartItems->first()?->currency ?? 'USD',
                ],
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::query()->where('is_active', true)->findOrFail($validated['product_id']);

        $cartItem = $request->user()->cartItems()->firstOrNew([
            'product_id' => $product->id,
        ]);

        $newQuantity = ($cartItem->exists ? $cartItem->quantity : 0) + $validated['quantity'];

        $cartItem->fill([
            'quantity' => $newQuantity,
            'unit_price' => $product->currentPrice(),
            'currency' => $product->currency,
        ])->save();

        return response()->json([
            'data' => $cartItem->load('product.category:id,name,slug'),
        ], 201);
    }

    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        abort_unless($cartItem->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'unit_price' => $cartItem->product->currentPrice(),
            'currency' => $cartItem->product->currency,
        ]);

        return response()->json([
            'data' => $cartItem->fresh()->load('product.category:id,name,slug'),
        ]);
    }

    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        abort_unless($cartItem->user_id === $request->user()->id, 404);

        $cartItem->delete();

        return response()->json([], 204);
    }
}
