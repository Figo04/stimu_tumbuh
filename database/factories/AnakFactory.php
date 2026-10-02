<?php

namespace Database\Factories;

use App\Models\Anak;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Anak>
 */
class AnakFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama_inisial' => strtoupper(fake()->lexify('??')),
            // 370–730 hari = selalu 12–24 bulan penuh (rentang registrasi).
            'tanggal_lahir' => now()->subDays(fake()->numberBetween(370, 730))->toDateString(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
        ];
    }
}
