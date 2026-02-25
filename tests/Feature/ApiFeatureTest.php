<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user_with_profile(): void
    {
        $payload = [
            'name' => 'Integration User',
            'email' => 'integration@example.com',
            'password' => 'password123',
            'phone' => '+14150001111',
            'address' => '5th Avenue 1',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.email', 'integration@example.com')
            ->assertJsonPath('data.profile.phone', '+14150001111');

        $this->assertDatabaseHas('users', [
            'email' => 'integration@example.com',
        ]);

        $userId = $response->json('data.id');

        $this->assertDatabaseHas('profiles', [
            'user_id' => $userId,
            'phone' => '+14150001111',
        ]);
    }

    public function test_can_create_order_and_attach_item_recalculates_total(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 99.50,
        ]);

        $orderResponse = $this->postJson('/api/orders', [
            'user_id' => $user->id,
        ]);

        $orderResponse->assertCreated();
        $orderId = (int) $orderResponse->json('data.id');

        $attachResponse = $this->postJson("/api/orders/{$orderId}/items", [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $attachResponse
            ->assertOk()
            ->assertJsonPath('data.total', '298.50');

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $product->id,
            'quantity' => 3,
            'price_at_purchase' => '99.50',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'total' => '298.50',
        ]);
    }

    public function test_products_filter_by_category(): void
    {
        $targetCategory = Category::factory()->create();
        $otherCategory = Category::factory()->create();

        Product::factory()->count(3)->create(['category_id' => $targetCategory->id]);
        Product::factory()->count(2)->create(['category_id' => $otherCategory->id]);

        $response = $this->getJson('/api/products?category_id='.$targetCategory->id.'&per_page=10');

        $response->assertOk();

        $products = $response->json('data');

        $this->assertCount(3, $products);
        foreach ($products as $product) {
            $this->assertSame($targetCategory->id, $product['category_id']);
        }
    }
}
