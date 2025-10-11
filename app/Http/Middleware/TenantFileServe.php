<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class TenantFileServe
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
            return response()->json(['message' => 'Tenant context required for file access'], 403);
        }

        // Validate that the requested file belongs to the current tenant
        $this->validateTenantFileAccess($request);

        return $next($request);
    }

    /**
     * Validate that the requested file belongs to the current tenant
     */
    private function validateTenantFileAccess(Request $request): void
    {
        $tenantId = tenant('id');
        
        if (!$tenantId) {
            throw new \Exception('No tenant context available for file access');
        }

        // Get the file path from the request
        $filePath = $request->path();
        
        // Check if the file path contains tenant-specific directory
        $tenantStoragePath = 'tenant-' . $tenantId;
        
        if (strpos($filePath, $tenantStoragePath) === false) {
            // If the file path doesn't contain the tenant directory, redirect to tenant-specific path
            $this->redirectToTenantFile($request, $tenantStoragePath);
        }
    }

    /**
     * Redirect to tenant-specific file path
     */
    private function redirectToTenantFile(Request $request, string $tenantStoragePath): void
    {
        $originalPath = $request->path();
        $tenantSpecificPath = $tenantStoragePath . '/' . $originalPath;
        
        // Check if the tenant-specific file exists
        $tenantStoragePath = storage_path('app/' . $tenantStoragePath);
        $fullPath = $tenantStoragePath . '/' . $originalPath;
        
        if (!file_exists($fullPath)) {
            abort(404, 'File not found in tenant storage');
        }
    }
}



