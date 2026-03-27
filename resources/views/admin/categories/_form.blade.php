@csrf
@if ($category->exists)
    @method('PUT')
@endif

<div class="form-grid">
    <label>
        Name
        <input type="text" name="name" value="{{ old('name', $category->name) }}" required>
    </label>
    <label>
        Slug
        <input type="text" name="slug" value="{{ old('slug', $category->slug) }}">
    </label>
    <label class="span-2">
        Description
        <textarea name="description">{{ old('description', $category->description) }}</textarea>
    </label>
    <label>
        Category image
        <input type="file" name="image" accept="image/*">
    </label>
    <label>
        Sort order
        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0" required>
    </label>
    @if ($category->image_source)
        <div class="span-2">
            <div class="meta" style="margin-bottom: 8px;">Current image</div>
            <img src="{{ $category->image_source }}" alt="{{ $category->name }}" style="width: 220px; max-width: 100%; border-radius: 18px; border: 1px solid var(--line);">
        </div>
    @endif
    <label style="align-content: center;">
        <span>Visibility</span>
        <span class="toolbar">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) style="width: auto;">
            Active
        </span>
    </label>
</div>

<div class="toolbar" style="margin-top: 18px;">
    <button type="submit" class="button">{{ $submitLabel }}</button>
    <a href="{{ route('admin.categories.index') }}" class="button secondary">Cancel</a>
</div>
