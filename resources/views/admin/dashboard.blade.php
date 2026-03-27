@extends('layouts.admin')

@section('content')
    <div class="grid" style="margin-bottom: 18px;">
        <div class="card">
            <div class="meta">Products</div>
            <div class="stat">{{ $stats['products'] }}</div>
        </div>
        <div class="card">
            <div class="meta">Categories</div>
            <div class="stat">{{ $stats['categories'] }}</div>
        </div>
        <div class="card">
            <div class="meta">Orders</div>
            <div class="stat">{{ $stats['orders'] }}</div>
        </div>
        <div class="card">
            <div class="meta">Customers</div>
            <div class="stat">{{ $stats['customers'] }}</div>
        </div>
        <div class="card">
            <div class="meta">Revenue</div>
            <div class="stat">${{ $stats['revenue'] }}</div>
        </div>
    </div>

    <div class="grid">
        <div class="panel">
            <div class="toolbar" style="justify-content: space-between; margin-bottom: 16px;">
                <h3>Recent orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="button secondary">All orders</a>
            </div>
            <table>
                <tbody>
                    @foreach ($recentOrders as $order)
                        <tr>
                            <td>{{ $order->number }}</td>
                            <td><span class="badge">{{ ucfirst($order->status) }}</span></td>
                            <td>${{ number_format((float) $order->subtotal, 2) }}</td>
                            <td><a href="{{ route('admin.orders.show', $order) }}">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="panel">
            <div class="toolbar" style="justify-content: space-between; margin-bottom: 16px;">
                <h3>Featured menu items</h3>
                <a href="{{ route('admin.products.index') }}" class="button secondary">Inventory</a>
            </div>
            <table>
                <tbody>
                    @forelse ($featuredProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td><span class="badge warn">{{ $product->category?->name }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td>No featured menu items.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
