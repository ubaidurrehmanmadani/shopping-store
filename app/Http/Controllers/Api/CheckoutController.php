<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckoutController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        $order = DB::transaction(function () use ($user, $validated): Order {
            $cartItems = $user->cartItems()->with('product')->lockForUpdate()->get();

            if ($cartItems->isEmpty()) {
                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Cart is empty.');
            }

            foreach ($cartItems as $cartItem) {
                $product = Product::query()->lockForUpdate()->findOrFail($cartItem->product_id);

                abort_if(! $product->is_active, 422, "{$product->name} is unavailable.");
                abort_if($product->stock < $cartItem->quantity, 422, "Insufficient stock for {$product->name}.");
            }

            $subtotal = $cartItems->sum(fn ($item) => (float) $item->unit_price * $item->quantity);
            $currency = $cartItems->first()->currency;

            $order = $user->orders()->create([
                ...$validated,
                'number' => 'ORD-'.Str::upper(Str::random(10)),
                'status' => 'placed',
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'currency' => $currency,
            ]);

            foreach ($cartItems as $cartItem) {
                $product = Product::query()->lockForUpdate()->findOrFail($cartItem->product_id);
                $lineTotal = (float) $cartItem->unit_price * $cartItem->quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'line_total' => number_format($lineTotal, 2, '.', ''),
                    'currency' => $cartItem->currency,
                ]);

                $product->decrement('stock', $cartItem->quantity);
            }

            $user->cartItems()->delete();

            return $order->load('items');
        });

        return response()->json([
            'data' => $order,
        ], Response::HTTP_CREATED);
    }
}
