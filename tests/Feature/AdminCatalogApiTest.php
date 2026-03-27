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
                'name' => 'Beauty',
                'description' => 'Skincare and cosmetics.',
                'sort_order' => 4,
            ]);

        $createCategoryResponse
            ->assertCreated()
            ->assertJsonPath('data.slug', 'beauty');

        $categoryId = $createCategoryResponse->json('data.id');

        $createProductResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/products', [
                'category_id' => $categoryId,
                'name' => 'Hydra Serum',
                'sku' => 'BE-HYDRA-SERUM',
                'price' => 39.99,
                'sale_price' => 29.99,
                'stock' => 12,
                'is_featured' => true,
            ]);

        $createProductResponse
            ->assertCreated()
            ->assertJsonPath('data.category.slug', 'beauty');

        $productId = $createProductResponse->json('data.id');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/v1/admin/products/{$productId}", [
                'category_id' => $categoryId,
                'name' => 'Hydra Serum Plus',
                'sku' => 'BE-HYDRA-SERUM',
                'price' => 44.99,
                'sale_price' => 34.99,
                'stock' => 10,
                'is_active' => true,
                'is_featured' => false,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Hydra Serum Plus');
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
