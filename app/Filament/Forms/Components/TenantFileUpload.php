<?php

namespace App\Filament\Forms\Components;

use App\Services\TenantFileService;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;

class TenantFileUpload extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->disk('tenant')
            ->directory('uploads')
            ->visibility('private')
            ->storeFileNamesIn('original_filename');
    }

    /**
     * Get the tenant file service
     */
    protected function getTenantFileService(): TenantFileService
    {
        return app(TenantFileService::class);
    }

    /**
     * Get the tenant storage disk
     */
    protected function getTenantDisk()
    {
        return Storage::disk('tenant');
    }

    /**
     * Get the tenant storage path
     */
    protected function getTenantStoragePath(): string
    {
        return $this->getTenantFileService()->getTenantStoragePath();
    }

    /**
     * Get the public URL for a tenant file
     */
    public function getTenantFileUrl(string $path): string
    {
        return $this->getTenantFileService()->getTenantPublicUrl($path);
    }

    /**
     * Check if a file exists in tenant storage
     */
    public function tenantFileExists(string $path): bool
    {
        return $this->getTenantFileService()->fileExists($path);
    }

    /**
     * Delete a file from tenant storage
     */
    public function deleteTenantFile(string $path): bool
    {
        return $this->getTenantFileService()->deleteFile($path);
    }
}



