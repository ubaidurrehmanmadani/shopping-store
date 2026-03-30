<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Support\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();
        $this->mergeGuestCart($request, $user);

        if ($user?->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('store.home'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->create($validated);

        Auth::login($user);
        $request->session()->regenerate();
        $this->mergeGuestCart($request, $user);

        return redirect()->intended(route('store.home'))->with('success', 'Your account is ready.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('store.home')->with('success', 'You have been signed out.');
    }

    private function mergeGuestCart(Request $request, ?User $user): void
    {
        if (! $user) {
            return;
        }

        $guestCart = collect($request->session()->get('guest_cart', []))
            ->filter(fn ($quantity, $productId) => (int) $productId > 0 && (int) $quantity > 0);

        if ($guestCart->isEmpty()) {
            return;
        }

        $products = Product::query()
            ->where('is_active', true)
            ->whereIn('id', $guestCart->keys())
            ->get()
            ->keyBy('id');

        foreach ($guestCart as $productId => $quantity) {
            $product = $products->get((int) $productId);

            if (! $product) {
                continue;
            }

            $cartItem = $user->cartItems()->firstOrNew([
                'product_id' => $product->id,
            ]);

            $cartItem->fill([
                'quantity' => ($cartItem->exists ? $cartItem->quantity : 0) + (int) $quantity,
                'unit_price' => $product->currentPrice(),
                'currency' => Currency::currentCode(),
            ])->save();
        }

        $request->session()->forget('guest_cart');
    }
}
