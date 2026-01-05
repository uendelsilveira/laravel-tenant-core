<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create SuperAdmin user for central database
        User::create([
            'name' => 'Super Admin',
            'email' => 'mail@example.com',
            'password' => Hash::make('123456'),
        ]);

        $this->command->info('✓ Created SuperAdmin: mail@example.com');
        $this->command->info('  Password: 123456');
    }
}
