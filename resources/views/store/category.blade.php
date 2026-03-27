@extends('layouts.store')

@section('content')
    <section class="hero">
        <div class="hero-copy stack">
            <div class="eyebrow">{{ $category->name }}</div>
            <h1>{{ $category->name }} menu</h1>
            <p>{{ $category->description }}</p>
            <div class="actions-row">
                <a href="{{ route('store.home') }}" class="button light">Back to menu</a>
                @auth
                    <a href="{{ route('store.cart.index') }}" class="button light">Open cart</a>
                @endif
            </div>
        </div>
        <div class="hero-side">
            @if ($category->image_source)
                <img src="{{ $category->image_source }}" alt="{{ $category->name }}">
            @endif
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div class="stack">
                <div class="eyebrow">Available now</div>
                <h2>{{ $products->total() }} menu items</h2>
            </div>
        </div>

        <div class="grid">
            @forelse ($products as $product)
                <article class="card">
                    <div class="media">
                        @if ($product->image_source)
                            <img src="{{ $product->image_source }}" alt="{{ $product->name }}">
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($product->is_featured)
                            <div class="badge">Most wanted</div>
                        @endif
                        <h3><a href="{{ route('store.products.show', $product) }}">{{ $product->name }}</a></h3>
                        <div class="meta">{{ $product->short_description }}</div>
                        <div class="actions-row" style="justify-content: space-between;">
                            <span class="price">${{ number_format((float) $product->currentPrice(), 2) }}</span>
                            @auth
                                <form method="POST" action="{{ route('store.cart.store') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="button">Add</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="button secondary">Login</a>
                            @endauth
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty">No menu items are active in this section yet.</div>
            @endforelse
        </div>
    </section>
@endsection
