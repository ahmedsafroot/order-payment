<?php

namespace Tests\Feature\Orders;

use Tests\TestCase;
use App\Models\User;

class OrderFeatureTest extends TestCase
{
    public function test_can_create_order(): void
    {
        $user = User::factory()->create();

        $order = [
            'items' => [
                ['product_name' => 'Keyboard', 'quantity' => 2, 'price' => 450],
                ['product_name' => 'Mouse',    'quantity' => 1, 'price' => 250],
            ],
        ];

        $res = $this->actingAsApi($user)->postJson('/api/orders', $order);

        $res->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.order.total_price', 1150)
            ->assertJsonStructure(['data' => ['order'=>['id','items']]]);
    }


    public function test_update_non_existent_returns_404(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAsApi($user)->putJson('/api/orders/999999', ['status' => 'pending']);

        $res->assertStatus(404)
            ->assertJsonPath('message', 'Not Found');
    }
}
