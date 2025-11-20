<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Montage;
use App\Models\Order;
use App\Models\User;
use Tests\RefreshTenantDatabase;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshTenantDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_auto_generates_order_number_on_creation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $order1 = Order::factory()->create();
        $order2 = Order::factory()->create();
        $order3 = Order::factory()->create();
        
        // Each order should have an incrementing number
        $this->assertNotNull($order1->number);
        $this->assertNotNull($order2->number);
        $this->assertNotNull($order3->number);
        
        // Numbers should be sequential
        $this->assertEquals((int)$order1->number + 1, (int)$order2->number);
        $this->assertEquals((int)$order2->number + 1, (int)$order3->number);
    }

    public function test_sets_user_id_automatically_when_authenticated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $order = Order::factory()->create(['user_id' => null]);
        
        // Should automatically set the authenticated user
        $this->assertEquals($user->id, $order->user_id);
    }

    public function test_respects_manually_set_order_number(): void
    {
        $order = Order::factory()->create(['number' => '12345']);
        
        $this->assertEquals('12345', $order->number);
    }

    public function test_finishing_order_confirms_all_montages(): void
    {
        $order = Order::factory()->create(['is_finished' => false]);
        
        // Create unconfirmed montages
        Montage::factory()->count(3)->create([
            'order_id' => $order->id,
            'confirmed' => false,
        ]);
        
        // Finish the order
        $order->update(['is_finished' => true]);
        
        // All montages should now be confirmed
        $this->assertEquals(3, $order->montages()->where('confirmed', true)->count());
    }

    public function test_redistributes_work_hours_when_order_finished_with_time_left(): void
    {
        $order = Order::factory()->create(['is_finished' => false]);
        $article = Article::factory()->create();
        
        // Attach article with montage time
        $order->articles()->attach($article, [
            'quantity' => 2,
            'montage_time' => 5.0, // 2 × 5 = 10 hours total
            'workers_count' => 2,
            'price' => 100,
        ]);
        
        // Create montage with only 6 hours worked (4 hours left)
        Montage::factory()->create([
            'order_id' => $order->id,
            'duration' => 6.0,
        ]);
        
        $order = $order->fresh(['articles', 'montages']);
        
        // Verify time left before finishing
        $this->assertEquals(4.0, $order->montage_time_left);
        
        // Finish the order
        $order->update(['is_finished' => true]);
        
        // Reload the pivot data
        $order = $order->fresh(['articles']);
        $pivot = $order->articles->first()->pivot;
        
        // Montage time should have been reduced
        // Original: 5.0
        // Denominator: 2 qty × 2 workers = 4
        // Per unit: 4.0 / 4 = 1.0
        // New montage time: 5.0 - 1.0 = 4.0
        $this->assertEquals(4.0, $pivot->montage_time);
    }

    public function test_does_not_redistribute_when_no_time_left(): void
    {
        $order = Order::factory()->create(['is_finished' => false]);
        $article = Article::factory()->create();
        
        $order->articles()->attach($article, [
            'quantity' => 2,
            'montage_time' => 5.0,
            'workers_count' => 2,
            'price' => 100,
        ]);
        
        // All time is worked
        Montage::factory()->create([
            'order_id' => $order->id,
            'duration' => 10.0,
        ]);
        
        $order = $order->fresh(['articles', 'montages']);
        $originalMontageTime = $order->articles->first()->pivot->montage_time;
        
        // Finish the order
        $order->update(['is_finished' => true]);
        
        // Montage time should remain unchanged
        $order = $order->fresh(['articles']);
        $this->assertEquals($originalMontageTime, $order->articles->first()->pivot->montage_time);
    }

    public function test_finishing_order_does_not_affect_other_orders(): void
    {
        $order1 = Order::factory()->create(['is_finished' => false]);
        $order2 = Order::factory()->create(['is_finished' => false]);
        
        Montage::factory()->create([
            'order_id' => $order1->id,
            'confirmed' => false,
        ]);
        
        Montage::factory()->create([
            'order_id' => $order2->id,
            'confirmed' => false,
        ]);
        
        // Finish order 1
        $order1->update(['is_finished' => true]);
        
        // Order 1 montages should be confirmed
        $this->assertTrue($order1->montages()->first()->confirmed);
        
        // Order 2 montages should remain unconfirmed
        $this->assertFalse($order2->fresh()->montages()->first()->confirmed);
    }
}

