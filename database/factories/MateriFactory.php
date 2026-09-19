<?php

namespace Database\Factories;

use App\Models\Materi;
use App\Services\UsiaAnakService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Materi>
 */
class MateriFactory extends Factory
{
    public function definition(): array
    {
        $judul = fake()->unique()->sentence(3);

        return [
            'judul' => $judul,
            'slug' => Str::slug($judul),
            'aspek' => fake()->randomElement(array_keys(Materi::ASPEK)),
            'kelompok_usia' => fake()->randomElement(UsiaAnakService::KELOMPOK_USIA),
            'urutan' => 1,
            'konten_view' => 'materi.placeholder',
        ];
    }
}
