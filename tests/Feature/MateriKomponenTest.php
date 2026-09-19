<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class MateriKomponenTest extends TestCase
{
    public function test_komponen_materi_merender_isinya(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-tip-box>Ajak anak bicara.</x-tip-box>
            <x-highlight varian="penting" judul="Perhatian">Awasi anak.</x-highlight>
            <x-checklist :items="['Mengangkat kepala', 'Berguling']" />
            <x-langkah-stimulasi :langkah="['Baringkan anak', 'Tunjukkan mainan']" />
            BLADE);

        foreach (['Tips', 'Ajak anak bicara.', 'Perhatian', 'Awasi anak.', 'bg-amber-50',
            'Mengangkat kepala', 'Berguling', 'Langkah-langkah', 'Baringkan anak', 'Tunjukkan mainan'] as $teks) {
            $this->assertStringContainsString($teks, $html);
        }
    }

    public function test_isi_komponen_di_escape(): void
    {
        $html = Blade::render('<x-checklist :items="[\'<script>x</script>\']" />');

        $this->assertStringNotContainsString('<script>x</script>', $html);
    }
}
