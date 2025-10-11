<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class TenantFileUpload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure we're in a tenant context
        if (!tenant()) {
            return response()->json(['message' => 'Tenant context required for file operations'], 403);
        }

        // Override the default filesystem disk for this request
        $this->configureTenantStorage();

        return $next($request);
    }

    /**
     * Configure tenant-specific storage for this request
     */
    private function configureTenantStorage(): void
    {
        $tenantId = tenant('id');
        
        if (!$tenantId) {
            throw new \Exception('No tenant context available for file storage');
        }

        // Create tenant-specific storage directory if it doesn't exist
        $tenantStoragePath = storage_path('app/tenant-' . $tenantId);
        if (!file_exists($tenantStoragePath)) {
            mkdir($tenantStoragePath, 0755, true);
        }

        // Set the tenant disk as default for this request
        config(['filesystems.default' => 'tenant']);
        
        // Ensure the tenant disk is properly configured
        config([
            'filesystems.disks.tenant.root' => $tenantStoragePath
        ]);
    }
}



