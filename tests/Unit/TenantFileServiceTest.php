<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TenantFileService;
use Stancl\Tenancy\Database\Models\Tenant;

class TenantFileServiceTest extends TestCase
{
    public function test_tenant_file_service_requires_tenant_context()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tenant context required for file operations');
        
        $tenantFileService = app(TenantFileService::class);
        $tenantFileService->getTenantStoragePath();
    }

    public function test_tenant_file_service_with_tenant_context()
    {
        // Create a mock tenant
        $tenant = new Tenant();
        $tenant->id = 'test-tenant-1';
        
        // Mock the tenant context
        $this->app->instance('tenant', $tenant);
        
        $tenantFileService = app(TenantFileService::class);
        
        // Test getting tenant storage path
        $storagePath = $tenantFileService->getTenantStoragePath();
        $this->assertStringContains('tenant-test-tenant-1', $storagePath);
        
        // Test getting tenant public URL
        $publicUrl = $tenantFileService->getTenantPublicUrl('test-file.pdf');
        $this->assertStringContains('tenant-test-tenant-1', $publicUrl);
    }
}



