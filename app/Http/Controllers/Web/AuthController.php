<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Support\Currency;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const PHONE_COUNTRY_CODES = [
        '+44' => 'United Kingdom (+44)',
        '+1' => 'United States / Canada (+1)',
        '+353' => 'Ireland (+353)',
        '+61' => 'Australia (+61)',
        '+971' => 'United Arab Emirates (+971)',
        '+92' => 'Pakistan (+92)',
    ];

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
        return view('auth.register', [
            'phoneCountryCodes' => self::PHONE_COUNTRY_CODES,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_country_code' => ['required', 'string', Rule::in(array_keys(self::PHONE_COUNTRY_CODES))],
            'phone_number' => ['required', 'string', 'max:32', 'regex:/^[0-9][0-9\s\-()]{5,31}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => trim($validated['phone_country_code'].' '.$validated['phone_number']),
            'password' => $validated['password'],
            'email_verified_at' => null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $this->mergeGuestCart($request, $user);
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with('success', 'Account created. Please verify your email before checkout.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('store.home')->with('success', 'You have been signed out.');
    }

    public function showVerifyNotice(Request $request): View|RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->route('store.home');
        }

        return view('auth.verify-email');
    }

    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            $request->fulfill();
        }

        return redirect()->intended(route('store.home'))->with('success', 'Email verified successfully.');
    }

    public function sendVerificationNotification(Request $request): RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->route('store.home');
        }

        $request->user()?->sendEmailVerificationNotification();

        return back()->with('success', 'A new verification email has been sent.');
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
