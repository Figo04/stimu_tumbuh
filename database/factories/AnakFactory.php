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
            'tanggal_lahir' => now()->subDays(fake()->numberBetween(0, 36 * 30))->toDateString(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
        ];
    }
}
