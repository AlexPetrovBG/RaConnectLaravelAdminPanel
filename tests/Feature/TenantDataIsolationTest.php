<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Client;
use App\Models\Document;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Database\Models\Tenant;
use Tests\TestCase;

class TenantDataIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant1;
    protected Tenant $tenant2;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create two test tenants
        $this->tenant1 = Tenant::create([
            'id' => 'test-tenant-1',
        ]);
        
        $this->tenant2 = Tenant::create([
            'id' => 'test-tenant-2',
        ]);
        
        // Create databases for both tenants
        $this->tenant1->createDatabase();
        $this->tenant2->createDatabase();
        
        // Run tenant migrations
        $this->tenant1->run(function () {
            $this->artisan('migrate', [
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);
        });
        
        $this->tenant2->run(function () {
            $this->artisan('migrate', [
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);
        });
    }

    public function test_orders_are_isolated_between_tenants(): void
    {
        // Create order in tenant 1
        tenancy()->initialize($this->tenant1);
        $user1 = User::factory()->create();
        $order1 = Order::factory()->create([
            'number' => 'ORDER-001',
            'user_id' => $user1->id,
        ]);
        $orderId1 = $order1->id;
        
        // Switch to tenant 2
        tenancy()->initialize($this->tenant2);
        $user2 = User::factory()->create();
        
        // Tenant 2 should not see tenant 1's orders
        $this->assertEquals(0, Order::count());
        $this->assertNull(Order::find($orderId1));
        
        // Create order in tenant 2
        $order2 = Order::factory()->create([
            'number' => 'ORDER-002',
            'user_id' => $user2->id,
        ]);
        
        $this->assertEquals(1, Order::count());
        $this->assertEquals('ORDER-002', $order2->number);
        
        // Switch back to tenant 1
        tenancy()->initialize($this->tenant1);
        
        // Tenant 1 should still have only its own order
        $this->assertEquals(1, Order::count());
        $this->assertEquals('ORDER-001', Order::first()->number);
    }

    public function test_clients_are_isolated_between_tenants(): void
    {
        tenancy()->initialize($this->tenant1);
        $client1 = Client::factory()->create([
            'name' => 'Client Tenant 1',
        ]);
        $clientId1 = $client1->id;
        
        tenancy()->initialize($this->tenant2);
        
        // Should not see tenant 1's client
        $this->assertEquals(0, Client::count());
        $this->assertNull(Client::find($clientId1));
        
        $client2 = Client::factory()->create([
            'name' => 'Client Tenant 2',
        ]);
        
        $this->assertEquals(1, Client::count());
        $this->assertEquals('Client Tenant 2', $client2->name);
    }

    public function test_documents_are_isolated_between_tenants(): void
    {
        tenancy()->initialize($this->tenant1);
        $order1 = Order::factory()->create();
        $document1 = Document::factory()->create([
            'order_id' => $order1->id,
            'file_name' => 'tenant1-doc.pdf',
        ]);
        $documentId1 = $document1->id;
        
        tenancy()->initialize($this->tenant2);
        
        // Should not see tenant 1's document
        $this->assertEquals(0, Document::count());
        $this->assertNull(Document::find($documentId1));
        
        $order2 = Order::factory()->create();
        $document2 = Document::factory()->create([
            'order_id' => $order2->id,
            'file_name' => 'tenant2-doc.pdf',
        ]);
        
        $this->assertEquals(1, Document::count());
        $this->assertEquals('tenant2-doc.pdf', $document2->file_name);
    }

    public function test_articles_are_isolated_between_tenants(): void
    {
        tenancy()->initialize($this->tenant1);
        $article1 = Article::factory()->create([
            'designation' => 'Tenant 1 Article',
        ]);
        $articleId1 = $article1->id;
        
        tenancy()->initialize($this->tenant2);
        
        // Should not see tenant 1's article
        $this->assertEquals(0, Article::count());
        $this->assertNull(Article::find($articleId1));
        
        $article2 = Article::factory()->create([
            'designation' => 'Tenant 2 Article',
        ]);
        
        $this->assertEquals(1, Article::count());
        $this->assertEquals('Tenant 2 Article', $article2->designation);
    }

    public function test_users_are_isolated_between_tenants(): void
    {
        tenancy()->initialize($this->tenant1);
        $user1 = User::factory()->create([
            'email' => 'user1@tenant1.com',
        ]);
        $userId1 = $user1->id;
        
        tenancy()->initialize($this->tenant2);
        
        // Should not see tenant 1's user
        $this->assertEquals(0, User::count());
        $this->assertNull(User::find($userId1));
        
        $user2 = User::factory()->create([
            'email' => 'user2@tenant2.com',
        ]);
        
        $this->assertEquals(1, User::count());
        $this->assertEquals('user2@tenant2.com', $user2->email);
    }

    public function test_order_relationships_respect_tenant_isolation(): void
    {
        // Create order with client in tenant 1
        tenancy()->initialize($this->tenant1);
        $client1 = Client::factory()->create();
        $order1 = Order::factory()->create([
            'client_id' => $client1->id,
        ]);
        
        // Verify relationship works in tenant 1
        $this->assertEquals($client1->id, $order1->client->id);
        
        // Switch to tenant 2
        tenancy()->initialize($this->tenant2);
        $client2 = Client::factory()->create();
        $order2 = Order::factory()->create([
            'client_id' => $client2->id,
        ]);
        
        // Verify relationship works in tenant 2
        $this->assertEquals($client2->id, $order2->client->id);
        
        // Verify tenant 2 cannot access tenant 1 relationships
        $this->assertEquals(1, Order::count());
        $this->assertEquals(1, Client::count());
    }

    public function test_cross_tenant_foreign_key_references_do_not_work(): void
    {
        tenancy()->initialize($this->tenant1);
        $client1 = Client::factory()->create();
        $clientId1 = $client1->id;
        
        tenancy()->initialize($this->tenant2);
        
        // Attempting to create an order with tenant 1's client ID should fail
        // because that client doesn't exist in tenant 2's database
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Order::factory()->create([
            'client_id' => $clientId1, // This ID doesn't exist in tenant 2
        ]);
    }

    public function test_tenant_switching_correctly_changes_database_context(): void
    {
        // Create different data in each tenant
        tenancy()->initialize($this->tenant1);
        Order::factory()->count(3)->create();
        
        tenancy()->initialize($this->tenant2);
        Order::factory()->count(5)->create();
        
        // Verify counts are correct for each tenant
        tenancy()->initialize($this->tenant1);
        $this->assertEquals(3, Order::count());
        
        tenancy()->initialize($this->tenant2);
        $this->assertEquals(5, Order::count());
        
        tenancy()->initialize($this->tenant1);
        $this->assertEquals(3, Order::count());
    }

    protected function tearDown(): void
    {
        // Clean up tenant databases
        $this->tenant1->deleteDatabase();
        $this->tenant2->deleteDatabase();
        
        parent::tearDown();
    }
}








