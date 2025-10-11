<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;

class InitializeTenancyByHeader extends InitializeTenancyByRequestData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for X-Tenant-ID header
        $tenantId = $request->header('X-Tenant-ID');
        
        if ($tenantId) {
            $tenant = $this->resolver->resolve($tenantId);
            
            if ($tenant) {
                tenancy()->initialize($tenant);
                return $next($request);
            }
        }
        
        // If no tenant found via header, continue without tenancy
        return $next($request);
    }
}
