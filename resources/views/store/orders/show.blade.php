@extends('layouts.store')

@section('content')
    <section class="section">
        <div class="split">
            <div class="panel stack">
                <div class="eyebrow">Order detail</div>
                <h1 style="font-size: 2.8rem;">{{ $order->number }}</h1>
                <div class="badge">{{ ucfirst($order->status) }}</div>
                <p>Placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
                <p><strong>Ship to:</strong><br>{{ $order->shipping_address }}</p>
            </div>
            <div class="panel">
                <h3>Items</h3>
                <table>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }} × {{ $item->quantity }}</td>
                                <td>{{ \App\Support\Currency::format($item->line_total, $item->currency) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td><strong>Subtotal</strong></td>
                            <td><strong>{{ \App\Support\Currency::format($order->subtotal, $order->currency) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
