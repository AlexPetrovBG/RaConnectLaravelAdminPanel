<?php

declare(strict_types=1);

use App\Http\Middleware\InitializeTenancyByHeader;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Subdomain-based tenant routes (for web browsers)
Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });
});

// Header-based tenant routes (for API access)
Route::middleware([
    'api',
    InitializeTenancyByHeader::class,
])->group(function () {
    Route::get('/api/tenant-info', function () {
        return response()->json([
            'tenant_id' => tenant('id'),
            'message' => 'This is your multi-tenant API. The id of the current tenant is ' . tenant('id')
        ]);
    });
});
