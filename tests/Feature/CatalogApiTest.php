<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_endpoint_returns_seeded_catalog_groups(): void
    {
        $this->seed();

        $response = $this->getJson('/api/v1/categories');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.slug', 'pizza');
    }

    public function test_products_endpoint_can_filter_featured_items(): void
    {
        $this->seed();

        $response = $this->getJson('/api/v1/products?featured=1');

        $response
            ->assertOk()
            ->assertJsonFragment(['sku' => 'BG-DOUBLE-SMASH'])
            ->assertJsonFragment(['sku' => 'PZ-FIREHOUSE-PEP']);
    }
}
