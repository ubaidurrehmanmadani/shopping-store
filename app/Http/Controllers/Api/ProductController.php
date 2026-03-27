<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with('category:id,name,slug')
            ->where('is_active', true)
            ->when(
                $request->integer('category_id'),
                fn ($query, $categoryId) => $query->where('category_id', $categoryId)
            )
            ->when(
                $request->boolean('featured'),
                fn ($query) => $query->where('is_featured', true)
            )
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($searchQuery) use ($request): void {
                    $search = $request->string('search')->toString();

                    $searchQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                })
            )
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate($request->integer('per_page', 12));

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load('category:id,name,slug');

        return response()->json([
            'data' => $product,
        ]);
    }
}
