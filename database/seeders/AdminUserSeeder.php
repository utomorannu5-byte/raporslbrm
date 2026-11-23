<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default administrator and teacher accounts.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@erapor-slb.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $admin->syncRoles('admin');

        $guru = User::updateOrCreate(
            ['email' => 'guru@erapor-slb.test'],
            [
                'name' => 'Guru Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $guru->syncRoles('guru');
    }
}
