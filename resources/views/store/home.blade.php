@extends('layouts.store')

@section('content')
    <section class="hero">
        <div class="hero-copy stack">
            <div class="eyebrow">Fast Delivery • Hot Food • Daily Deals</div>
            <h1>Pizza nights, burger cravings, and combo meals built for quick checkout.</h1>
            <p>RushBite is now configured as a fast-food ordering experience. Browse the menu, add items straight from the cards, and place pickup or delivery orders in minutes.</p>

            <div class="actions-row">
                <a href="#featured" class="button">Order featured meals</a>
                <a href="#categories" class="button light">Explore menu</a>
            </div>

            <div class="quick-stats">
                <div class="quick-stat">
                    <strong>{{ $featuredProducts->count() }}</strong>
                    Featured menu picks
                </div>
                <div class="quick-stat">
                    <strong>{{ $categories->count() }}</strong>
                    Core menu sections
                </div>
                <div class="quick-stat">
                    <strong>20 min</strong>
                    Average kitchen prep target
                </div>
            </div>
        </div>
        <div class="hero-side">
            <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591" alt="Fast food spread">
        </div>
    </section>

    <section class="section" id="categories">
        <div class="section-head">
            <div class="stack">
                <div class="eyebrow">Menu sections</div>
                <h2>Pick what you are in the mood for</h2>
            </div>
            <div class="meta" style="color: rgba(255,255,255,0.78);">Direct order buttons are available on every menu card.</div>
        </div>

        <div class="grid">
            @foreach ($categories as $category)
                <a href="{{ route('store.categories.show', $category) }}" class="card">
                    <div class="media">
                        @if ($category->image_source)
                            <img src="{{ $category->image_source }}" alt="{{ $category->name }}">
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="badge">{{ $category->products_count }} items</div>
                        <h3>{{ $category->name }}</h3>
                        <div class="meta">{{ $category->description }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="section" id="featured">
        <div class="section-head">
            <div class="stack">
                <div class="eyebrow">Best sellers</div>
                <h2>Featured menu items everyone orders first</h2>
            </div>
            <a href="{{ route('store.cart.index') }}" class="button light">Open cart</a>
        </div>

        <div class="grid">
            @foreach ($featuredProducts as $product)
                <article class="card">
                    <div class="media">
                        @if ($product->image_source)
                            <img src="{{ $product->image_source }}" alt="{{ $product->name }}">
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="badge">{{ $product->category->name }}</div>
                        <h3><a href="{{ route('store.products.show', $product) }}">{{ $product->name }}</a></h3>
                        <div class="meta">{{ $product->short_description }}</div>
                        <div>
                            <span class="price">{{ $product->formattedCurrentPrice() }}</span>
                            @if ($product->sale_price)
                                <span class="strike">{{ $product->formattedOriginalPrice() }}</span>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('store.cart.store') }}" class="stack">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="button">Add to cart</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div class="stack">
                <div class="eyebrow">Tonight's lineup</div>
                <h2>Fresh from the menu board</h2>
            </div>
        </div>

        <div class="grid">
            @foreach ($latestProducts as $product)
                <article class="card">
                    <div class="media">
                        @if ($product->image_source)
                            <img src="{{ $product->image_source }}" alt="{{ $product->name }}">
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="meta">{{ $product->category->name }}</div>
                        <h3><a href="{{ route('store.products.show', $product) }}">{{ $product->name }}</a></h3>
                        <div class="meta">{{ $product->short_description }}</div>
                        <div class="actions-row" style="justify-content: space-between;">
                            <span class="price">{{ $product->formattedCurrentPrice() }}</span>
                            <form method="POST" action="{{ route('store.cart.store') }}" class="inline">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="button">Add</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="menu-cta">
            <div class="panel">
                <div class="eyebrow">Fast checkout</div>
                <h3>Order as a customer</h3>
                <p>Create an account once and reuse saved details for future orders.</p>
            </div>
            <div class="panel">
                <div class="eyebrow">Operator control</div>
                <h3>Manage the menu in admin</h3>
                <p>Upload food images, feature best sellers, and track incoming orders.</p>
            </div>
        </div>
    </section>
@endsection
