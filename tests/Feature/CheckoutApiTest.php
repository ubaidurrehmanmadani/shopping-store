<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_to_cart_and_checkout(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $token = $user->issueApiToken('galaxy')['access_token'];
        $product = Product::query()->where('sku', 'PZ-FIREHOUSE-PEP')->firstOrFail();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'quantity' => 2,
            ])
            ->assertCreated()
            ->assertJsonPath('data.quantity', 2);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/cart')
            ->assertOk()
            ->assertJsonPath('data.summary.total_quantity', 2);

        $checkoutResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/checkout', [
                'customer_name' => 'Checkout User',
                'customer_email' => 'checkout@example.com',
                'customer_phone' => '+1-555-0100',
                'shipping_address' => '123 Market Street, San Francisco, CA',
                'notes' => 'Leave at the front desk.',
            ]);

        $checkoutResponse
            ->assertCreated()
            ->assertJsonPath('data.items.0.product_sku', 'PZ-FIREHOUSE-PEP')
            ->assertJsonPath('data.status', 'placed');

        $this->assertDatabaseCount('cart_items', 0);
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'customer_email' => 'checkout@example.com',
        ]);
    }

    public function test_checkout_requires_authenticated_user(): void
    {
        $this->postJson('/api/v1/checkout', [
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'shipping_address' => 'Nowhere',
        ])->assertUnauthorized();
    }
}
