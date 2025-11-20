<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

trait RefreshTenantDatabase
{
    use RefreshDatabase;

    /**
     * Refresh the in-memory database.
     */
    protected function refreshInMemoryDatabase(): void
    {
        // Run main migrations (central + business tables)
        $this->artisan('migrate');
        
        // Also run tenant-specific migrations
        $this->artisan('migrate', [
            '--path' => 'database/migrations/tenant',
        ]);

        $this->app[Kernel::class]->setArtisan(null);
    }

    /**
     * Refresh the test database.
     */
    protected function refreshTestDatabase(): void
    {
        if (! RefreshDatabaseState::$migrated) {
            // Run fresh migrations for all tables
            $this->artisan('migrate:fresh', [
                '--drop-views' => $this->shouldDropViews(),
                '--drop-types' => $this->shouldDropTypes(),
            ]);
            
            // Also run tenant-specific migrations
            $this->artisan('migrate', [
                '--path' => 'database/migrations/tenant',
            ]);

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }
}


