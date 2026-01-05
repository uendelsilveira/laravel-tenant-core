<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding central database...');
        $this->command->newLine();

        // Seed SuperAdmin user
        $this->call(UserSeeder::class);

        // Seed example tenant
        $this->call(TenantSeeder::class);

        $this->command->newLine();
        $this->command->info('✅ Seeding completed!');
        $this->command->newLine();
        $this->command->warn('⚠️  Remember to create the tenant database:');
        $this->command->line('   CREATE DATABASE tenant_foo;');
        $this->command->newLine();
        $this->command->warn('⚠️  Then run tenant migrations:');
        $this->command->line('   php artisan migrate --database=tenant --path=database/migrations/tenant');
    }
}
