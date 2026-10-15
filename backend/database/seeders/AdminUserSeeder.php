<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('portfolio.admin_email');
        $password = config('portfolio.admin_password');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD not set; skipping admin user.');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Administrator', 'password' => $password],
        );
    }
}
