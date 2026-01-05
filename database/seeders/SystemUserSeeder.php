<?php

namespace Database\Seeders;

use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder creates an admin user for the tenant database.
     */
    public function run(): void
    {
        // Create admin user for tenant
        SystemUser::create([
            'name' => 'Tenant Admin',
            'email' => 'admin@tenant.local',
            'password' => Hash::make('password'),
        ]);

        $this->command->info('✓ Created tenant admin: admin@tenant.local');
        $this->command->info('  Password: password');
    }
}
