@extends('layouts.admin')

@section('content')
    <div class="panel">
        <div class="toolbar" style="justify-content: space-between; margin-bottom: 16px;">
            <div>
                <h3>Products</h3>
                <p>Manage menu pricing, categories, images, and featured highlights.</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="button">New product</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            <div class="meta">{{ $product->sku }}</div>
                        </td>
                        <td>{{ $product->category?->name }}</td>
                        <td>{{ $product->formattedCurrentPrice() }}</td>
                        <td>
                            @if ($product->is_featured)
                                <span class="badge warn">Featured</span>
                            @endif
                            <span @class(['badge', 'good' => $product->is_active])>{{ $product->is_active ? 'Active' : 'Hidden' }}</span>
                        </td>
                        <td>
                            <div class="toolbar">
                                <a href="{{ route('admin.products.edit', $product) }}" class="button secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
