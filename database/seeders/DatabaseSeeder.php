<?php

namespace Database\Seeders;

use App\Models\Anak;
use App\Models\User;
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
        $this->call([AdminSeeder::class, KuesionerSoalSeeder::class, MateriSeeder::class, ItemPerkembanganSeeder::class]);

        User::factory()->has(Anak::factory(), 'anak')->create([
            'nama' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
