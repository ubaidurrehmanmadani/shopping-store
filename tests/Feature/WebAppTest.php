<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_homepage_renders_catalog_content(): void
    {
        $this->seed();

        $this->get('/')
            ->assertOk()
            ->assertSee('RushBite')
            ->assertSee('Featured menu items')
            ->assertSee('Firehouse Pepperoni Pizza');
    }

    public function test_storefront_renders_selected_product_currency(): void
    {
        $this->seed();

        AppSetting::storeMany([
            'site_currency' => 'PKR',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Rs16.99', false)
            ->assertDontSee('$16.99', false);
    }

    public function test_admin_user_can_open_dashboard(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Admin Panel')
            ->assertSee('Recent orders');
    }

    public function test_customer_can_add_product_to_cart_from_web(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $product = Product::query()->where('sku', 'PZ-FIREHOUSE-PEP')->firstOrFail();
        $csrfToken = 'web-test-token';

        $this->actingAs($user)
            ->withSession(['_token' => $csrfToken])
            ->post('/cart', [
                '_token' => $csrfToken,
                'product_id' => $product->id,
                'quantity' => 1,
            ])
            ->assertRedirect(route('store.cart.index'));

        $this->actingAs($user)
            ->get('/cart')
            ->assertOk()
            ->assertSee('Review your order before checkout')
            ->assertSee('Firehouse Pepperoni Pizza');
    }

    public function test_customer_can_update_saved_profile_address(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'customer@example.com')->firstOrFail();
        $csrfToken = 'profile-update-token';

        $this->actingAs($user)
            ->withSession(['_token' => $csrfToken])
            ->put('/profile', [
                '_token' => $csrfToken,
                'name' => 'Updated Customer',
                'phone' => '+1 222 333 4444',
                'address_line' => '99 Pizza Lane',
                'city' => 'Flavor Town',
                'area' => 'East Block',
                'postal_code' => '44556',
            ])
            ->assertRedirect(route('store.profile.edit'));

        $user->refresh();

        $this->assertSame('Updated Customer', $user->name);
        $this->assertSame('99 Pizza Lane', $user->address_line);
        $this->assertSame('Flavor Town', $user->city);
    }

    public function test_checkout_can_use_different_delivery_address_than_profile(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'customer@example.com')->firstOrFail();
        $product = Product::query()->where('sku', 'PZ-FIREHOUSE-PEP')->firstOrFail();
        $csrfToken = 'checkout-different-address-token';

        $this->actingAs($user)
            ->withSession(['_token' => $csrfToken])
            ->post('/cart', [
                '_token' => $csrfToken,
                'product_id' => $product->id,
                'quantity' => 1,
            ]);

        $this->actingAs($user)
            ->withSession(['_token' => $csrfToken])
            ->post('/checkout', [
                '_token' => $csrfToken,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'use_profile_address' => '0',
                'address_line' => '200 Office Street',
                'city' => 'Metro City',
                'area' => 'Business Bay',
                'postal_code' => '55667',
                'notes' => 'Call on arrival.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'shipping_address' => '200 Office Street, Metro City, Business Bay, 55667',
        ]);
    }

    public function test_admin_can_upload_product_image_from_admin_panel(): void
    {
        Storage::fake('public');
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $csrfToken = 'admin-image-token';

        $response = $this->actingAs($admin)
            ->withSession(['_token' => $csrfToken])
            ->post('/admin/products', [
                '_token' => $csrfToken,
                'category_id' => 1,
                'name' => 'Uploaded Image Product',
                'slug' => 'uploaded-image-product',
                'sku' => 'UP-IMAGE-001',
                'price' => 49.99,
                'sale_price' => 39.99,
                'is_active' => '1',
                'is_featured' => '0',
                'image' => UploadedFile::fake()->image('product-cover.jpg'),
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::query()->where('sku', 'UP-IMAGE-001')->firstOrFail();

        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_admin_can_replace_existing_product_image(): void
    {
        Storage::fake('public');
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $product = Product::query()->where('sku', 'PZ-FIREHOUSE-PEP')->firstOrFail();
        $csrfToken = 'admin-update-image-token';

        $this->actingAs($admin)
            ->withSession(['_token' => $csrfToken])
            ->put("/admin/products/{$product->id}", [
                '_token' => $csrfToken,
                'category_id' => $product->category_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'short_description' => $product->short_description,
                'description' => $product->description,
                'sku' => $product->sku,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'is_active' => '1',
                'is_featured' => $product->is_featured ? '1' : '0',
                'image' => UploadedFile::fake()->image('replacement-cover.jpg'),
            ])
            ->assertRedirect(route('admin.products.index'));

        $product->refresh();

        $this->assertNotNull($product->image_path);
        $this->assertNull($product->image_url);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_admin_can_update_brand_name_and_logo(): void
    {
        Storage::fake('public');
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $csrfToken = 'settings-update-token';

        $this->actingAs($admin)
            ->withSession(['_token' => $csrfToken])
            ->post('/admin/settings', [
                '_token' => $csrfToken,
                'site_name' => 'BurgerBurst',
                'site_tagline' => 'Fast food with a louder flavor profile.',
                'contact_phone' => '+1 999 888 7777',
                'contact_email' => 'admin@burgerburst.test',
                'contact_address' => '11 Burger Avenue',
                'site_currency' => 'GBP',
                'logo' => UploadedFile::fake()->image('brand-logo.png'),
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $this->actingAs($admin)
            ->get('/')
            ->assertOk()
            ->assertSee('BurgerBurst')
            ->assertSee('£16.99', false);
    }
}
