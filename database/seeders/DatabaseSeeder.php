<?php

namespace Database\Seeders;

use App\Models\AppSetting;
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
        collect([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'phone' => '+1 555 0100',
                'address_line' => '12 Main Boulevard',
                'city' => 'Downtown',
                'area' => 'City Center',
                'postal_code' => '10001',
                'password' => 'password',
                'is_admin' => true,
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '+1 555 0101',
                'address_line' => '45 Market Street',
                'city' => 'Brookfield',
                'area' => 'West Side',
                'postal_code' => '10002',
                'password' => 'password',
                'is_admin' => false,
            ],
            [
                'name' => 'Demo Customer',
                'email' => 'customer@example.com',
                'phone' => '+1 555 0102',
                'address_line' => '89 Sunset Avenue',
                'city' => 'Lakeside',
                'area' => 'North Block',
                'postal_code' => '10003',
                'password' => 'password',
                'is_admin' => false,
            ],
        ])->each(function (array $user): void {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'phone' => $user['phone'],
                    'address_line' => $user['address_line'],
                    'city' => $user['city'],
                    'area' => $user['area'],
                    'postal_code' => $user['postal_code'],
                    'password' => $user['password'],
                    'is_admin' => $user['is_admin'],
                ]
            );
        });

        AppSetting::storeMany([
            'site_name' => 'RushBite',
            'site_tagline' => 'Fast burgers, loaded pizza, and late-night cravings delivered hot.',
            'contact_phone' => '+1 555 RUSHBITE',
            'contact_email' => 'hello@rushbite.test',
            'contact_address' => '21 Flavor Street, Downtown Food District',
            'site_currency' => 'USD',
            'site_logo_path' => null,
        ]);

        $categories = collect([
            [
                'name' => 'Pizza',
                'description' => 'Stone-baked favorites loaded with house sauce, cheese, and bold toppings.',
                'image_url' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591',
                'sort_order' => 1,
            ],
            [
                'name' => 'Burgers',
                'description' => 'Smashed patties, stacked buns, and signature fast-food comfort.',
                'image_url' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd',
                'sort_order' => 2,
            ],
            [
                'name' => 'Sides & Drinks',
                'description' => 'Fries, wings, shakes, and drinks built for combo meals and cravings.',
                'image_url' => 'https://images.unsplash.com/photo-1577805947697-89e18249d767',
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

        $products = collect([
            [
                'category_slug' => 'pizza',
                'name' => 'Firehouse Pepperoni Pizza',
                'sku' => 'PZ-FIREHOUSE-PEP',
                'short_description' => 'Pepperoni, mozzarella, crushed chili, and charred crust.',
                'description' => 'Our crowd favorite pepperoni pizza with bubbling mozzarella, spicy beef pepperoni, and a crisp fire-baked finish.',
                'price' => 18.99,
                'sale_price' => 16.99,
                'stock' => 0,
                'is_featured' => true,
                'image_url' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591',
            ],
            [
                'category_slug' => 'pizza',
                'name' => 'Loaded BBQ Chicken Pizza',
                'sku' => 'PZ-BBQ-CHICKEN',
                'short_description' => 'BBQ chicken, red onion, smoked cheese, and ranch finish.',
                'description' => 'Sweet smoky barbecue sauce layered with roasted chicken, red onion, and a creamy ranch drizzle.',
                'price' => 20.99,
                'sale_price' => null,
                'stock' => 0,
                'is_featured' => true,
                'image_url' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38',
            ],
            [
                'category_slug' => 'burgers',
                'name' => 'Double Smash Burger',
                'sku' => 'BG-DOUBLE-SMASH',
                'short_description' => 'Double beef patties, cheddar, pickles, and secret sauce.',
                'description' => 'Two smashed beef patties with melted cheddar, crisp pickles, diced onion, and our late-night burger sauce.',
                'price' => 12.49,
                'sale_price' => 10.99,
                'stock' => 0,
                'is_featured' => true,
                'image_url' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd',
            ],
            [
                'category_slug' => 'burgers',
                'name' => 'Crispy Chicken Burger',
                'sku' => 'BG-CRISPY-CHICKEN',
                'short_description' => 'Crunchy fried chicken, lettuce, pickles, and spicy mayo.',
                'description' => 'Golden crispy chicken stacked high with shredded lettuce, dill pickles, and a fast-food spicy mayo kick.',
                'price' => 11.49,
                'sale_price' => null,
                'stock' => 0,
                'is_featured' => false,
                'image_url' => 'https://images.unsplash.com/photo-1615297928064-24977384d0da',
            ],
            [
                'category_slug' => 'sides-drinks',
                'name' => 'Loaded Fries Box',
                'sku' => 'SD-LOADED-FRIES',
                'short_description' => 'Crispy fries with cheese sauce, jalapenos, and smoky seasoning.',
                'description' => 'A generous fries box topped with molten cheese sauce, crunchy jalapenos, and smoky house seasoning.',
                'price' => 6.99,
                'sale_price' => null,
                'stock' => 0,
                'is_featured' => false,
                'image_url' => 'https://images.unsplash.com/photo-1630384060421-cb20d0e0649d',
            ],
            [
                'category_slug' => 'sides-drinks',
                'name' => 'Chocolate Oreo Shake',
                'sku' => 'SD-OREO-SHAKE',
                'short_description' => 'Cold, thick shake blended with Oreo crumbs and chocolate.',
                'description' => 'A dessert-style chocolate milkshake blended thick with Oreo cookies and finished with whipped cream.',
                'price' => 5.49,
                'sale_price' => 4.99,
                'stock' => 0,
                'is_featured' => true,
                'image_url' => 'https://images.unsplash.com/photo-1577805947697-89e18249d767',
            ],
        ]);

        Product::query()
            ->whereNotIn('sku', $products->pluck('sku'))
            ->delete();

        Category::query()
            ->whereNotIn('slug', $categories->keys())
            ->delete();

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
