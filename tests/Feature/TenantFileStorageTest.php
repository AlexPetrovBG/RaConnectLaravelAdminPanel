<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\TenantFileService;
use Stancl\Tenancy\Database\Models\Tenant;

class TenantFileStorageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test tenant
        $this->tenant = Tenant::create([
            'id' => 'test-tenant-1',
            'data' => [
                'name' => 'Test Tenant 1',
                'domain' => 'test1.yourapp.com',
            ],
        ]);
    }

    public function test_tenant_file_storage_creates_tenant_directory()
    {
        tenancy()->initialize($this->tenant);
        
        $tenantFileService = app(TenantFileService::class);
        $storagePath = $tenantFileService->getTenantStoragePath();
        
        $this->assertTrue(file_exists($storagePath));
        $this->assertStringContains('tenant-test-tenant-1', $storagePath);
    }

    public function test_tenant_file_upload_works()
    {
        tenancy()->initialize($this->tenant);
        
        $file = UploadedFile::fake()->create('test-document.pdf', 100);
        $tenantFileService = app(TenantFileService::class);
        
        $storedPath = $tenantFileService->storeFile($file, 'documents');
        
        $this->assertNotEmpty($storedPath);
        $this->assertTrue($tenantFileService->fileExists($storedPath));
    }

    public function test_tenant_file_isolation()
    {
        // Create second tenant
        $tenant2 = Tenant::create([
            'id' => 'test-tenant-2',
            'data' => [
                'name' => 'Test Tenant 2',
                'domain' => 'test2.yourapp.com',
            ],
        ]);

        // Upload file to tenant 1
        tenancy()->initialize($this->tenant);
        $file1 = UploadedFile::fake()->create('tenant1-document.pdf', 100);
        $tenantFileService = app(TenantFileService::class);
        $path1 = $tenantFileService->storeFile($file1, 'documents');

        // Switch to tenant 2
        tenancy()->initialize($tenant2);
        $tenantFileService = app(TenantFileService::class);
        
        // File from tenant 1 should not exist in tenant 2 storage
        $this->assertFalse($tenantFileService->fileExists($path1));
        
        // Upload file to tenant 2
        $file2 = UploadedFile::fake()->create('tenant2-document.pdf', 100);
        $path2 = $tenantFileService->storeFile($file2, 'documents');
        
        $this->assertTrue($tenantFileService->fileExists($path2));
    }

    public function test_tenant_file_service_requires_tenant_context()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tenant context required for file operations');
        
        $tenantFileService = app(TenantFileService::class);
        $tenantFileService->getTenantStoragePath();
    }

    public function test_tenant_file_service_methods()
    {
        tenancy()->initialize($this->tenant);
        
        $tenantFileService = app(TenantFileService::class);
        
        // Test getting tenant storage path
        $storagePath = $tenantFileService->getTenantStoragePath();
        $this->assertStringContains('tenant-test-tenant-1', $storagePath);
        
        // Test getting tenant public URL
        $publicUrl = $tenantFileService->getTenantPublicUrl('test-file.pdf');
        $this->assertStringContains('tenant-test-tenant-1', $publicUrl);
    }
}
