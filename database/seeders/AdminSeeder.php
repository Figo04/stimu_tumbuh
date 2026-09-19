<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (blank($email) || blank($password)) {
            throw new RuntimeException('ADMIN_EMAIL dan ADMIN_PASSWORD wajib diisi di .env sebelum seeding.');
        }

        // firstOrCreate: seeding ulang tidak menimpa password yang sudah diganti admin.
        Admin::firstOrCreate(
            ['email' => $email],
            ['nama' => env('ADMIN_NAMA', 'Admin Peneliti'), 'password' => $password],
        );
    }
}
