<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with('category:id,name,slug')
            ->when(
                $request->integer('category_id'),
                fn ($query, $categoryId) => $query->where('category_id', $categoryId)
            )
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $product = Product::query()->create($this->validatePayload($request));

        return response()->json([
            'data' => $product->load('category:id,name,slug'),
        ], Response::HTTP_CREATED);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'data' => $product->load('category:id,name,slug'),
        ]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $product->update($this->validatePayload($request, $product));

        return response()->json([
            'data' => $product->fresh()->load('category:id,name,slug'),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    private function validatePayload(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product?->id),
            ],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($product?->id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['slug'] ?? $validated['name']);
        $validated['currency'] = Currency::currentCode();
        $validated['stock'] = $product?->stock ?? 0;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['is_featured'] = $validated['is_featured'] ?? false;

        return $validated;
    }
}
