@extends('layouts.store')

@section('content')
    <section class="section">
        <div class="section-head">
            <div class="stack">
                <div class="eyebrow">Bag summary</div>
                <h2>Review your order before checkout</h2>
            </div>
        </div>

        @if ($cartItems->isEmpty())
            <div class="empty">
                <h3>Your bag is empty.</h3>
                <p>Browse the menu and add your first meal.</p>
            </div>
        @else
            <div class="split">
                <div class="panel">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name }}</strong>
                                        <div class="meta">{{ $item->product->category->name }}</div>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('store.cart.update', $item) }}" class="stack">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1">
                                            <button type="submit" class="button secondary">Update</button>
                                        </form>
                                    </td>
                                    <td>{{ \App\Support\Currency::format($item->unit_price * $item->quantity, $item->currency) }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('store.cart.destroy', $item) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button secondary">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel stack">
                    <div class="eyebrow">Checkout summary</div>
                    <h3>Subtotal</h3>
                    <div class="price">{{ \App\Support\Currency::format($subtotal, $cartItems->first()?->currency) }}</div>
                    <p>{{ $cartItems->sum('quantity') }} items ready for checkout across {{ $cartItems->count() }} menu lines.</p>
                    <a href="{{ route('store.checkout') }}" class="button">Continue to checkout</a>
                </div>
            </div>
        @endif
    </section>
@endsection
