@extends('layouts.admin')

@section('content')
    <div class="grid">
        <div class="panel">
            <div class="toolbar" style="justify-content: space-between; margin-bottom: 16px;">
                <div>
                    <h3>{{ $order->number }}</h3>
                    <p>{{ $order->customer_name }} · {{ $order->customer_email }}</p>
                </div>
                <span class="badge">{{ ucfirst($order->status) }}</span>
            </div>

            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="toolbar" style="margin-bottom: 18px;">
                @csrf
                @method('PATCH')
                <select name="status" style="max-width: 240px;">
                    @foreach (['placed', 'processing', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="button">Update status</button>
            </form>

            <p><strong>Shipping address</strong><br>{{ $order->shipping_address }}</p>
            @if ($order->notes)
                <p style="margin-top: 12px;"><strong>Notes</strong><br>{{ $order->notes }}</p>
            @endif
        </div>
        <div class="panel">
            <h3>Items</h3>
            <table>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->product_name }}
                                <div class="meta">{{ $item->product_sku }}</div>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"><strong>Subtotal</strong></td>
                        <td><strong>${{ number_format((float) $order->subtotal, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
