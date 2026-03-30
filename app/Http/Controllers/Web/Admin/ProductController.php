<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::query()
                ->with('category:id,name')
                ->orderByDesc('is_featured')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);
        $validated['image_path'] = $this->storeImage($request);

        if ($validated['image_path']) {
            $validated['image_url'] = null;
        }

        Product::query()->create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validatePayload($request, $product);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->storeImage($request, $product->image_path);
            $validated['image_url'] = null;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product?->id)],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product?->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);
        $validated['currency'] = Currency::currentCode();
        $validated['stock'] = $product?->stock ?? 0;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        unset($validated['image']);

        return $validated;
    }

    private function storeImage(Request $request, ?string $existingPath = null): ?string
    {
        if (! $request->hasFile('image')) {
            return $existingPath;
        }

        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        return $request->file('image')->store('products', 'public');
    }
}
