@extends('layouts.store')

@section('content')
    <section class="section">
        <div class="section-head">
            <div class="stack">
                <div class="eyebrow">Checkout</div>
                <h2>Confirm pickup or delivery details</h2>
            </div>
        </div>

        <div class="split">
            <div class="panel">
                <form method="POST" action="{{ route('store.checkout.store') }}" class="stack">
                    @csrf
                    <label>
                        Customer name
                        <input type="text" name="customer_name" value="{{ old('customer_name', $user->name) }}" required>
                    </label>
                    <label>
                        Customer email
                        <input type="email" name="customer_email" value="{{ old('customer_email', $user->email) }}" required>
                    </label>
                    <label>
                        Phone
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', $user->phone) }}">
                    </label>

                    <div class="panel" style="background: var(--paper-soft);">
                        <div class="stack">
                            <div>
                                <div class="eyebrow" style="background: rgba(244, 93, 34, 0.1); color: var(--brand-dark);">Saved profile address</div>
                                <h3 style="margin-top: 10px;">Use your saved address for delivery</h3>
                            </div>
                            <div class="meta">
                                @if ($user->defaultAddress())
                                    {{ $user->defaultAddress() }}
                                @else
                                    No saved profile address yet. Uncheck below and enter a delivery address.
                                @endif
                            </div>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input
                                    type="hidden"
                                    name="use_profile_address"
                                    value="0"
                                >
                                <input
                                    type="checkbox"
                                    name="use_profile_address"
                                    value="1"
                                    style="width: auto;"
                                    @checked(old('use_profile_address', 1))
                                    onchange="document.getElementById('different-address-section').style.display = this.checked ? 'none' : 'grid';"
                                >
                                Same delivery address as my profile
                            </label>
                        </div>
                    </div>

                    <div
                        id="different-address-section"
                        class="stack"
                        style="display: {{ old('use_profile_address', 1) ? 'none' : 'grid' }};"
                    >
                        <div>
                            <div class="eyebrow" style="background: rgba(244, 93, 34, 0.1); color: var(--brand-dark);">Different delivery address</div>
                            <h3 style="margin-top: 10px;">Deliver to another location</h3>
                        </div>
                        <label>
                            Street / address line
                            <input type="text" name="address_line" value="{{ old('address_line') }}">
                        </label>
                        <label>
                            City
                            <input type="text" name="city" value="{{ old('city') }}">
                        </label>
                        <label>
                            Area / state
                            <input type="text" name="area" value="{{ old('area') }}">
                        </label>
                        <label>
                            Postal code
                            <input type="text" name="postal_code" value="{{ old('postal_code') }}">
                        </label>
                    </div>

                    <label>
                        Kitchen notes
                        <textarea name="notes">{{ old('notes') }}</textarea>
                    </label>
                    <button type="submit" class="button">Place food order</button>
                </form>
            </div>
            <div class="panel">
                <div class="eyebrow">Order summary</div>
                <h3>{{ \App\Support\Currency::format($subtotal, $cartItems->first()?->currency) }}</h3>
                <table>
                    <tbody>
                        @foreach ($cartItems as $item)
                            <tr>
                                <td>{{ $item->product->name }} × {{ $item->quantity }}</td>
                                <td>{{ \App\Support\Currency::format($item->unit_price * $item->quantity, $item->currency) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
