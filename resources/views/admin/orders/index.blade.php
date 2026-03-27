@extends('layouts.admin')

@section('content')
    <div class="panel">
        <h3>Orders</h3>
        <p>Track placed checkouts and move orders through fulfillment states.</p>
        <table style="margin-top: 18px;">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Placed</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->number }}</td>
                        <td><span class="badge">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->items_count }}</td>
                        <td>${{ number_format((float) $order->subtotal, 2) }}</td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="button secondary">Open</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
