<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * @param  array<string, mixed>  $validated
     */
    public function placeOrder(User $user, array $validated): Order
    {
        return DB::transaction(function () use ($user, $validated): Order {
            $cartItems = $user->cartItems()->with('product')->lockForUpdate()->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Cart is empty.',
                ]);
            }

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;

                if (! $product->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => "{$product->name} is unavailable.",
                    ]);
                }
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
                $product = $cartItem->product;
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
            }

            $user->cartItems()->delete();

            return $order->load('items');
        });
    }
}
