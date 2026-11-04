<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(ProjectSeeder::class);

        // Admin login for /admin. Set ADMIN_EMAIL (and optionally ADMIN_PASSWORD) when seeding;
        // without a password a random one is made and printed once.
        // Later: `php artisan make:filament-user`.
        if ($email = env('ADMIN_EMAIL')) {
            if (User::where('email', $email)->exists()) {
                $this->command?->info("Admin {$email} already exists");
            } else {
                $password = env('ADMIN_PASSWORD') ?: Str::password(20, symbols: false);
                User::create(['name' => 'EMAX', 'email' => $email, 'password' => $password]);
                $this->command?->warn("Admin login: {$email} / {$password}");
            }
        }
    }
}
