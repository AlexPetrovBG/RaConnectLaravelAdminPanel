<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class TenantFileService
{
    /**
     * Store a file in tenant-specific storage
     */
    public function storeFile(UploadedFile $file, string $path = ''): string
    {
        $this->ensureTenantContext();
        
        $tenantId = tenant('id');
        $tenantStoragePath = 'tenant-' . $tenantId;
        
        // Generate unique filename to prevent conflicts
        $filename = time() . '_' . $file->getClientOriginalName();
        $fullPath = $path ? $path . '/' . $filename : $filename;
        
        // Store the file in tenant-specific directory
        $storedPath = Storage::disk('tenant')->putFileAs($path, $file, $filename);
        
        return $storedPath;
    }

    /**
     * Get the URL for a tenant file
     */
    public function getFileUrl(string $path): string
    {
        $this->ensureTenantContext();
        
        return Storage::disk('tenant')->url($path);
    }

    /**
     * Delete a file from tenant storage
     */
    public function deleteFile(string $path): bool
    {
        $this->ensureTenantContext();
        
        return Storage::disk('tenant')->delete($path);
    }

    /**
     * Check if a file exists in tenant storage
     */
    public function fileExists(string $path): bool
    {
        $this->ensureTenantContext();
        
        return Storage::disk('tenant')->exists($path);
    }

    /**
     * Get file contents from tenant storage
     */
    public function getFileContents(string $path): string
    {
        $this->ensureTenantContext();
        
        return Storage::disk('tenant')->get($path);
    }

    /**
     * List files in tenant storage directory
     */
    public function listFiles(string $directory = ''): array
    {
        $this->ensureTenantContext();
        
        return Storage::disk('tenant')->files($directory);
    }

    /**
     * Create a directory in tenant storage
     */
    public function createDirectory(string $path): bool
    {
        $this->ensureTenantContext();
        
        return Storage::disk('tenant')->makeDirectory($path);
    }

    /**
     * Get tenant storage disk
     */
    public function getStorageDisk()
    {
        $this->ensureTenantContext();
        
        return Storage::disk('tenant');
    }

    /**
     * Ensure we're in a tenant context
     */
    private function ensureTenantContext(): void
    {
        if (!tenant()) {
            throw new \Exception('Tenant context required for file operations');
        }
    }

    /**
     * Get tenant-specific storage path
     */
    public function getTenantStoragePath(): string
    {
        $this->ensureTenantContext();
        
        $tenantId = tenant('id');
        return storage_path('app/tenant-' . $tenantId);
    }

    /**
     * Get tenant-specific public URL
     */
    public function getTenantPublicUrl(string $path): string
    {
        $this->ensureTenantContext();
        
        $tenantId = tenant('id');
        return url('storage/tenant-' . $tenantId . '/' . $path);
    }
}



