<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_catalog_records(): void
    {
        $this->seed();

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $token = $admin->issueApiToken('ipad')['access_token'];

        $createCategoryResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/categories', [
                'name' => 'Wraps',
                'description' => 'Grilled wraps and handheld fast lunch picks.',
                'sort_order' => 4,
            ]);

        $createCategoryResponse
            ->assertCreated()
            ->assertJsonPath('data.slug', 'wraps');

        $categoryId = $createCategoryResponse->json('data.id');

        $createProductResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/products', [
                'category_id' => $categoryId,
                'name' => 'Spicy Chicken Wrap',
                'sku' => 'WR-SPICY-CHICKEN',
                'price' => 39.99,
                'sale_price' => 29.99,
                'is_featured' => true,
            ]);

        $createProductResponse
            ->assertCreated()
            ->assertJsonPath('data.category.slug', 'wraps');

        $productId = $createProductResponse->json('data.id');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/v1/admin/products/{$productId}", [
                'category_id' => $categoryId,
                'name' => 'Spicy Chicken Wrap Combo',
                'sku' => 'WR-SPICY-CHICKEN',
                'price' => 44.99,
                'sale_price' => 34.99,
                'is_active' => true,
                'is_featured' => false,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Spicy Chicken Wrap Combo');
    }

    public function test_non_admin_user_cannot_access_admin_catalog_routes(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $category = Category::query()->firstOrFail();
        $token = $user->issueApiToken('pixel')['access_token'];

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson("/api/v1/admin/categories/{$category->id}")
            ->assertForbidden();
    }
}
