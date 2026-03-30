<?php

namespace App\Models;

use App\Support\Currency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'category_id',
    'name',
    'slug',
    'short_description',
    'description',
    'sku',
    'price',
    'sale_price',
    'currency',
    'stock',
    'image_url',
    'image_path',
    'is_active',
    'is_featured',
])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function currentPrice(): string
    {
        return (string) ($this->sale_price ?? $this->price);
    }

    public function getCurrencyAttribute(?string $value): string
    {
        return Currency::currentCode();
    }

    public function formattedCurrentPrice(): string
    {
        return Currency::format($this->currentPrice(), null);
    }

    public function formattedOriginalPrice(): string
    {
        return Currency::format($this->price, null);
    }

    public function getImageSourceAttribute(): ?string
    {
        if ($this->image_path) {
            return '/storage/'.$this->image_path;
        }

        return $this->image_url;
    }
}
