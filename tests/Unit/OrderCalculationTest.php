<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Article;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Montage;
use App\Models\Order;
use Tests\RefreshTenantDatabase;
use Tests\TestCase;

class OrderCalculationTest extends TestCase
{
    use RefreshTenantDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tests will use the default test database with RefreshDatabase
    }

    public function test_calculates_montage_time_from_articles(): void
    {
        $order = Order::factory()->create();
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();
        
        // Attach articles with quantities and montage times
        $order->articles()->attach($article1, [
            'quantity' => 2,
            'montage_time' => 3.5,
            'price' => 100,
        ]);
        
        $order->articles()->attach($article2, [
            'quantity' => 1,
            'montage_time' => 4.0,
            'price' => 150,
        ]);
        
        // Refresh to load relationships
        $order = $order->fresh(['articles']);
        
        // Expected: (2 × 3.5) + (1 × 4.0) = 7 + 4 = 11
        $this->assertEquals(11.0, $order->montage_time);
    }

    public function test_calculates_budget_from_articles(): void
    {
        $order = Order::factory()->create();
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();
        
        $order->articles()->attach($article1, [
            'quantity' => 2,
            'price' => 100,
            'montage_time' => 1,
        ]);
        
        $order->articles()->attach($article2, [
            'quantity' => 3,
            'price' => 50,
            'montage_time' => 1,
        ]);
        
        $order = $order->fresh(['articles']);
        
        // Expected: (2 × 100) + (3 × 50) = 200 + 150 = 350
        $this->assertEquals(350.0, $order->budget);
    }

    public function test_calculates_montage_time_worked(): void
    {
        $order = Order::factory()->create();
        
        // Create montages
        Montage::factory()->create([
            'order_id' => $order->id,
            'duration' => 3.5,
        ]);
        
        Montage::factory()->create([
            'order_id' => $order->id,
            'duration' => 2.5,
        ]);
        
        $order = $order->fresh(['montages']);
        
        // Expected: 3.5 + 2.5 = 6.0
        $this->assertEquals(6.0, $order->montage_time_worked);
    }

    public function test_calculates_montage_time_left(): void
    {
        $order = Order::factory()->create();
        $article = Article::factory()->create();
        
        $order->articles()->attach($article, [
            'quantity' => 2,
            'montage_time' => 5.0,
            'price' => 100,
        ]);
        
        Montage::factory()->create([
            'order_id' => $order->id,
            'duration' => 3.0,
        ]);
        
        $order = $order->fresh(['articles', 'montages']);
        
        // Total montage time: 2 × 5.0 = 10.0
        // Worked: 3.0
        // Left: 10.0 - 3.0 = 7.0
        $this->assertEquals(7.0, $order->montage_time_left);
    }

    public function test_calculates_paid_from_customer(): void
    {
        // Create document category for revenue
        $revenueCategory = DocumentCategory::factory()->create([
            'type' => 'revenue',
        ]);
        
        $order = Order::factory()->create();
        
        // Create paid revenue documents
        Document::factory()->create([
            'order_id' => $order->id,
            'document_category_id' => $revenueCategory->id,
            'price' => 500,
            'is_paid' => true,
        ]);
        
        Document::factory()->create([
            'order_id' => $order->id,
            'document_category_id' => $revenueCategory->id,
            'price' => 300,
            'is_paid' => true,
        ]);
        
        // Create unpaid revenue document (should not be counted)
        Document::factory()->create([
            'order_id' => $order->id,
            'document_category_id' => $revenueCategory->id,
            'price' => 200,
            'is_paid' => false,
        ]);
        
        $order = $order->fresh(['documents']);
        
        // Expected: 500 + 300 = 800 (unpaid 200 not counted)
        $this->assertEquals(800, $order->paid_from_customer);
    }

    public function test_calculates_debt_from_customer(): void
    {
        $revenueCategory = DocumentCategory::factory()->create([
            'type' => 'revenue',
        ]);
        
        $order = Order::factory()->create([
            'price_to_customer' => 1000,
        ]);
        
        Document::factory()->create([
            'order_id' => $order->id,
            'document_category_id' => $revenueCategory->id,
            'price' => 300,
            'is_paid' => true,
        ]);
        
        $order = $order->fresh(['documents']);
        
        // Expected: 1000 - 300 = 700
        $this->assertEquals(700, $order->debt_from_customer);
    }

    public function test_calculates_debt_to_supplier(): void
    {
        $expenseCategory = DocumentCategory::factory()->create([
            'type' => 'expense',
        ]);
        
        $order = Order::factory()->create([
            'price_to_supplier' => 800,
        ]);
        
        Document::factory()->create([
            'order_id' => $order->id,
            'document_category_id' => $expenseCategory->id,
            'price' => 200,
            'is_paid' => true,
        ]);
        
        $order = $order->fresh(['documents']);
        
        // Expected: 800 - 200 = 600
        $this->assertEquals(600, $order->debt_to_supplier);
    }

    public function test_calculates_balance(): void
    {
        $revenueCategory = DocumentCategory::factory()->create([
            'type' => 'revenue',
        ]);
        
        $expenseCategory = DocumentCategory::factory()->create([
            'type' => 'expense',
        ]);
        
        $order = Order::factory()->create([
            'price_to_customer' => 1000,
            'price_to_supplier' => 600,
        ]);
        
        // Customer paid 400
        Document::factory()->create([
            'order_id' => $order->id,
            'document_category_id' => $revenueCategory->id,
            'price' => 400,
            'is_paid' => true,
        ]);
        
        // Paid to supplier 200
        Document::factory()->create([
            'order_id' => $order->id,
            'document_category_id' => $expenseCategory->id,
            'price' => 200,
            'is_paid' => true,
        ]);
        
        $order = $order->fresh(['documents']);
        
        // Debt from customer: 1000 - 400 = 600
        // Debt to supplier: 600 - 200 = 400
        // Balance: 600 - 400 = 200
        $this->assertEquals(200, $order->balance);
    }

    public function test_balance_handles_zero_prices(): void
    {
        $order = Order::factory()->create([
            'price_to_customer' => null,
            'price_to_supplier' => null,
        ]);
        
        $order = $order->fresh(['documents']);
        
        // Should handle null prices gracefully
        $this->assertEquals(0, $order->balance);
    }
}

