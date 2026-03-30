@csrf
@if ($product->exists)
    @method('PUT')
@endif

<div class="form-grid">
    <label>
        Category
        <select name="category_id" required>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </label>
    <label>
        Name
        <input type="text" name="name" value="{{ old('name', $product->name) }}" required>
    </label>
    <label>
        Slug
        <input type="text" name="slug" value="{{ old('slug', $product->slug) }}">
    </label>
    <label>
        SKU
        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required>
    </label>
    <label class="span-2">
        Short description
        <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}">
    </label>
    <label class="span-2">
        Description
        <textarea name="description">{{ old('description', $product->description) }}</textarea>
    </label>
    <label>
        Price
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" required>
    </label>
    <label>
        Sale price
        <input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}">
    </label>
    <label class="span-2">
        Product image
        <input type="file" name="image" accept="image/*">
    </label>
    @if ($product->image_source)
        <div class="span-2">
            <div class="meta" style="margin-bottom: 8px;">Current image</div>
            <img src="{{ $product->image_source }}" alt="{{ $product->name }}" style="width: 220px; max-width: 100%; border-radius: 18px; border: 1px solid var(--line);">
        </div>
    @endif
    <label>
        <span>Visibility</span>
        <span class="toolbar">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) style="width: auto;">
            Active
        </span>
    </label>
    <label>
        <span>Featured</span>
        <span class="toolbar">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false)) style="width: auto;">
            Featured
        </span>
    </label>
</div>

<div class="toolbar" style="margin-top: 18px;">
    <button type="submit" class="button">{{ $submitLabel }}</button>
    <a href="{{ route('admin.products.index') }}" class="button secondary">Cancel</a>
</div>
