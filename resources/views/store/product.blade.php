@extends('layouts.store')

@section('content')
    <section class="hero">
        <div class="hero-side">
            @if ($product->image_source)
                <img src="{{ $product->image_source }}" alt="{{ $product->name }}">
            @endif
        </div>
        <div class="hero-copy stack">
            <div class="eyebrow">{{ $product->category->name }}</div>
            <h1>{{ $product->name }}</h1>
            <p>{{ $product->description ?: $product->short_description }}</p>
            <div>
                <span class="price" style="color: white; font-size: 1.8rem;">{{ $product->formattedCurrentPrice() }}</span>
                @if ($product->sale_price)
                    <span class="strike" style="color: rgba(255,255,255,0.75);">{{ $product->formattedOriginalPrice() }}</span>
                @endif
            </div>
            <div class="meta" style="color: rgba(255,255,255,0.82);">Kitchen favorite · Prepared fresh to order · SKU {{ $product->sku }}</div>

            <form method="POST" action="{{ route('store.cart.store') }}" class="stack">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <label style="color: white;">
                    Quantity
                    <input type="number" name="quantity" value="1" min="1">
                </label>
                <button type="submit" class="button">Add to cart</button>
            </form>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div class="stack">
                <div class="eyebrow">You may also like</div>
                <h2>More from {{ $product->category->name }}</h2>
            </div>
        </div>

        <div class="grid">
            @foreach ($relatedProducts as $relatedProduct)
                <article class="card">
                    <div class="media">
                        @if ($relatedProduct->image_source)
                            <img src="{{ $relatedProduct->image_source }}" alt="{{ $relatedProduct->name }}">
                        @endif
                    </div>
                    <div class="card-body">
                        <h3><a href="{{ route('store.products.show', $relatedProduct) }}">{{ $relatedProduct->name }}</a></h3>
                        <div class="meta">{{ $relatedProduct->short_description }}</div>
                        <div class="actions-row" style="justify-content: space-between;">
                            <span class="price">{{ $relatedProduct->formattedCurrentPrice() }}</span>
                            <form method="POST" action="{{ route('store.cart.store') }}" class="inline">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="button">Add</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
