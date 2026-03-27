<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function home(): View
    {
        return view('store.home', [
            'categories' => Category::query()
                ->where('is_active', true)
                ->withCount([
                    'products' => fn ($query) => $query->where('is_active', true),
                ])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'featuredProducts' => Product::query()
                ->with('category:id,name,slug')
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('name')
                ->take(6)
                ->get(),
            'latestProducts' => Product::query()
                ->with('category:id,name,slug')
                ->where('is_active', true)
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }

    public function category(Category $category): View
    {
        abort_unless($category->is_active, 404);

        return view('store.category', [
            'category' => $category,
            'products' => $category->products()
                ->where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('name')
                ->paginate(12),
        ]);
    }

    public function product(Product $product): View
    {
        abort_unless($product->is_active, 404);

        return view('store.product', [
            'product' => $product->load('category:id,name,slug'),
            'relatedProducts' => Product::query()
                ->where('is_active', true)
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->take(4)
                ->get(),
        ]);
    }
}
