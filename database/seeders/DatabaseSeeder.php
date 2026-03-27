<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        $categories = collect([
            [
                'name' => 'Electronics',
                'description' => 'Phones, headphones, accessories, and smart devices.',
                'image_url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9',
                'sort_order' => 1,
            ],
            [
                'name' => 'Fashion',
                'description' => 'Everyday essentials and premium wardrobe picks.',
                'image_url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8',
                'sort_order' => 2,
            ],
            [
                'name' => 'Home',
                'description' => 'Decor, storage, and practical living upgrades.',
                'image_url' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85',
                'sort_order' => 3,
            ],
        ])->mapWithKeys(function (array $category): array {
            $category['slug'] = Str::slug($category['name']);
            $record = Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );

            return [$record->slug => $record];
        });

        $products = [
            [
                'category_slug' => 'electronics',
                'name' => 'Nova X Smartphone',
                'sku' => 'EL-NOVA-X',
                'short_description' => '128GB 5G phone with OLED display.',
                'description' => 'A fast, modern smartphone designed for an MVP storefront demo.',
                'price' => 699.00,
                'sale_price' => 649.00,
                'stock' => 18,
                'is_featured' => true,
                'image_url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9',
            ],
            [
                'category_slug' => 'electronics',
                'name' => 'Pulse Wireless Earbuds',
                'sku' => 'EL-PULSE-BUDS',
                'short_description' => 'Noise-isolating earbuds with charging case.',
                'description' => 'Compact wireless earbuds with strong battery life and a clean profile.',
                'price' => 129.00,
                'sale_price' => 99.00,
                'stock' => 40,
                'is_featured' => true,
                'image_url' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df',
            ],
            [
                'category_slug' => 'fashion',
                'name' => 'Urban Classic Jacket',
                'sku' => 'FA-URBAN-JACKET',
                'short_description' => 'Lightweight jacket for everyday wear.',
                'description' => 'A versatile jacket that works for casual and semi-formal looks.',
                'price' => 89.00,
                'sale_price' => null,
                'stock' => 26,
                'is_featured' => false,
                'image_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab',
            ],
            [
                'category_slug' => 'home',
                'name' => 'Luma Table Lamp',
                'sku' => 'HO-LUMA-LAMP',
                'short_description' => 'Minimal lamp with warm light finish.',
                'description' => 'A simple lamp to give the catalog immediate range across categories.',
                'price' => 59.00,
                'sale_price' => 49.00,
                'stock' => 14,
                'is_featured' => false,
                'image_url' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85',
            ],
        ];

        foreach ($products as $product) {
            $category = $categories[$product['category_slug']];

            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']),
                    'short_description' => $product['short_description'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'sale_price' => $product['sale_price'],
                    'currency' => 'USD',
                    'stock' => $product['stock'],
                    'image_url' => $product['image_url'],
                    'is_active' => true,
                    'is_featured' => $product['is_featured'],
                ]
            );
        }
    }
}
