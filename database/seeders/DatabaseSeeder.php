<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Services\SchoolDemoDataSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@nexora.local'],
            [
                'name' => 'Nexora Demo',
                'password' => bcrypt('nexora123'),
                'email_verified_at' => now(),
            ]
        );

        $demoOrganization = Organization::firstOrCreate(
            ['slug' => 'nexora-demo'],
            ['name' => 'Nexora Demo Schule', 'status' => 'active']
        );

        if (! $demoUser->organizations()->whereKey($demoOrganization->id)->exists()) {
            $demoOrganization->users()->attach($demoUser, ['role' => 'owner']);
        }

        SchoolDemoDataSeeder::seed($demoOrganization);
    }
}
