@extends('layouts.store')

@section('content')
    <section class="section">
        <div class="stack" style="margin-bottom: 18px;">
            <div class="eyebrow">Order history</div>
            <h1 style="font-size: 3rem;">Your recent orders</h1>
        </div>

        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->number }}</td>
                            <td><span class="badge">{{ ucfirst($order->status) }}</span></td>
                            <td>{{ $order->items_count }}</td>
                            <td>${{ number_format((float) $order->subtotal, 2) }}</td>
                            <td><a href="{{ route('store.orders.show', $order) }}" class="button secondary">Open</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
