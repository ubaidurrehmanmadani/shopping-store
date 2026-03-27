<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cartItems = $request->user()
            ->cartItems()
            ->with('product.category:id,name,slug')
            ->orderByDesc('updated_at')
            ->get();

        return view('store.cart', [
            'cartItems' => $cartItems,
            'subtotal' => number_format(
                $cartItems->sum(fn (CartItem $item) => (float) $item->unit_price * $item->quantity),
                2,
                '.',
                ''
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse
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

        return redirect()->route('store.cart.index')->with('success', "{$product->name} added to cart.");
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
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

        return redirect()->route('store.cart.index')->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->user_id === $request->user()->id, 404);

        $cartItem->delete();

        return redirect()->route('store.cart.index')->with('success', 'Item removed from cart.');
    }

    public function checkout(Request $request): View
    {
        $user = $request->user();
        $cartItems = $request->user()->cartItems()->with('product')->get();

        return view('store.checkout', [
            'cartItems' => $cartItems,
            'subtotal' => number_format(
                $cartItems->sum(fn (CartItem $item) => (float) $item->unit_price * $item->quantity),
                2,
                '.',
                ''
            ),
            'user' => $user,
        ]);
    }

    public function placeOrder(Request $request, CheckoutService $checkoutService): RedirectResponse
    {
        $useProfileAddress = $request->boolean('use_profile_address', true);

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:255'],
            'use_profile_address' => ['nullable', 'boolean'],
            'address_line' => [$useProfileAddress ? 'nullable' : 'required', 'string', 'max:255'],
            'city' => [$useProfileAddress ? 'nullable' : 'required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        $addressLine = $useProfileAddress ? $user->address_line : $validated['address_line'];
        $city = $useProfileAddress ? $user->city : $validated['city'];
        $area = $useProfileAddress ? $user->area : ($validated['area'] ?? null);
        $postalCode = $useProfileAddress ? $user->postal_code : ($validated['postal_code'] ?? null);

        if (! $addressLine || ! $city) {
            return back()->withErrors([
                'address_line' => 'Please complete your saved profile address or enter a different delivery address.',
            ])->withInput();
        }

        $validated['shipping_address'] = collect([
            $addressLine,
            $city,
            $area,
            $postalCode,
        ])->filter()->implode(', ');

        $user->update([
            'name' => $validated['customer_name'],
            'phone' => $validated['customer_phone'] ?? null,
            'address_line' => $useProfileAddress ? $user->address_line : $addressLine,
            'city' => $useProfileAddress ? $user->city : $city,
            'area' => $useProfileAddress ? $user->area : $area,
            'postal_code' => $useProfileAddress ? $user->postal_code : $postalCode,
        ]);

        $order = $checkoutService->placeOrder($user, $validated);

        return redirect()->route('store.orders.show', $order)->with('success', 'Order placed successfully.');
    }
}
