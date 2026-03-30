<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CheckoutService;
use App\Support\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CartController extends Controller
{
    private const GUEST_CART_KEY = 'guest_cart';

    public function index(Request $request): View
    {
        $cartItems = $this->cartItems($request);

        return view('store.cart', [
            'cartItems' => $cartItems,
            'subtotal' => $this->subtotal($cartItems),
            'requiresAuthForCheckout' => $request->user() === null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::query()->where('is_active', true)->findOrFail($validated['product_id']);
        $quantityToAdd = $validated['quantity'];

        if ($request->user()) {
            $cartItem = $request->user()->cartItems()->firstOrNew([
                'product_id' => $product->id,
            ]);

            $newQuantity = ($cartItem->exists ? $cartItem->quantity : 0) + $quantityToAdd;

            $cartItem->fill([
                'quantity' => $newQuantity,
                'unit_price' => $product->currentPrice(),
                'currency' => Currency::currentCode(),
            ])->save();

            return redirect()->route('store.cart.index')->with('success', "{$product->name} added to cart.");
        }

        $cart = $request->session()->get(self::GUEST_CART_KEY, []);
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $quantityToAdd;
        $request->session()->put(self::GUEST_CART_KEY, $cart);

        return redirect()->route('store.cart.index')->with('success', "{$product->name} added to cart.");
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        abort_unless($product->is_active, 404);

        if ($request->user()) {
            $cartItem = $request->user()->cartItems()->where('product_id', $product->id)->firstOrFail();

            $cartItem->update([
                'quantity' => $validated['quantity'],
                'unit_price' => $product->currentPrice(),
                'currency' => Currency::currentCode(),
            ]);

            return redirect()->route('store.cart.index')->with('success', 'Cart updated.');
        }

        $cart = $request->session()->get(self::GUEST_CART_KEY, []);
        abort_unless(array_key_exists($product->id, $cart), 404);
        $cart[$product->id] = $validated['quantity'];
        $request->session()->put(self::GUEST_CART_KEY, $cart);

        return redirect()->route('store.cart.index')->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        if ($request->user()) {
            $cartItem = $request->user()->cartItems()->where('product_id', $product->id)->first();
            abort_unless($cartItem !== null, 404);
            $cartItem->delete();

            return redirect()->route('store.cart.index')->with('success', 'Item removed from cart.');
        }

        $cart = $request->session()->get(self::GUEST_CART_KEY, []);
        abort_unless(array_key_exists($product->id, $cart), 404);
        unset($cart[$product->id]);
        $request->session()->put(self::GUEST_CART_KEY, $cart);

        return redirect()->route('store.cart.index')->with('success', 'Item removed from cart.');
    }

    public function checkout(Request $request): View
    {
        $cartItems = $this->cartItems($request);

        if ($cartItems->isEmpty()) {
            return redirect()->route('store.cart.index')->with('success', 'Your cart is empty.');
        }

        if (! $request->user()) {
            $request->session()->put('url.intended', route('store.checkout'));

            return redirect()
                ->route('login')
                ->with('success', 'Please login or sign up to continue to checkout.');
        }

        $user = $request->user();

        return view('store.checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $this->subtotal($cartItems),
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

    /**
     * @return Collection<int, object>
     */
    private function cartItems(Request $request): Collection
    {
        if ($request->user()) {
            return $request->user()
                ->cartItems()
                ->with('product.category:id,name,slug')
                ->orderByDesc('updated_at')
                ->get();
        }

        $guestCart = collect($request->session()->get(self::GUEST_CART_KEY, []))
            ->filter(fn ($quantity, $productId) => (int) $productId > 0 && (int) $quantity > 0);

        if ($guestCart->isEmpty()) {
            return collect();
        }

        $products = Product::query()
            ->with('category:id,name,slug')
            ->where('is_active', true)
            ->whereIn('id', $guestCart->keys())
            ->get()
            ->keyBy('id');

        return $guestCart->map(function ($quantity, $productId) use ($products) {
            $product = $products->get((int) $productId);

            if (! $product) {
                return null;
            }

            return (object) [
                'product_id' => $product->id,
                'quantity' => (int) $quantity,
                'unit_price' => $product->currentPrice(),
                'currency' => Currency::currentCode(),
                'product' => $product,
            ];
        })->filter()->values();
    }

    private function subtotal(Collection $cartItems): string
    {
        return number_format(
            $cartItems->sum(fn ($item) => (float) $item->unit_price * $item->quantity),
            2,
            '.',
            ''
        );
    }
}
