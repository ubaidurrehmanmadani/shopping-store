<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount([
                'products' => fn ($query) => $query->where('is_active', true),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load([
            'products' => fn ($query) => $query
                ->where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('name'),
        ]);

        return response()->json([
            'data' => $category,
        ]);
    }
}
