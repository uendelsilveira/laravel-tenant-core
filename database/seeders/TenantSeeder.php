<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create example tenant
        $tenant = Tenant::create([
            'name' => 'Example Tenant',
            'slug' => 'foo',
            'database_name' => 'tenant_foo',
            'is_active' => true,
        ]);

        // Create domain for the tenant
        Domain::create([
            'tenant_id' => $tenant->id,
            'domain' => 'foo.localhost',
            'is_primary' => true,
        ]);

        $this->command->info('✓ Created example tenant: foo.localhost');
        $this->command->info('✓ Database: tenant_foo');
    }
}
