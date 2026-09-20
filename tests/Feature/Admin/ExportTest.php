<?php

namespace Tests\Feature\Admin;

use App\Exports\DataPenelitianExport;
use App\Models\Admin;
use App\Models\AktivitasStimulasi;
use App\Models\Anak;
use App\Models\HasilKuesioner;
use App\Models\Materi;
use App\Models\PenilaianPerkembangan;
use App\Models\ProgressMateri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'password' => 'rahasia123']);
    }

    /** Satu responden lengkap: anak, materi (1 dibuka+selesai dari 2), aktivitas, penilaian, pre-test. */
    private function responden(): User
    {
        $user = User::factory()->create(['kode_responden' => 'RSP-001', 'nama' => 'Ibu Uji']);
        $anak = Anak::factory()->create([
            'user_id' => $user->id, 'nama_inisial' => 'AZ', 'tanggal_lahir' => now()->subMonths(8),
            'jenis_persalinan' => 'sc', 'kondisi_lahir' => 'bblr', 'bb_lahir_gram' => 2200,
        ]);

        $materi = Materi::factory()->create(['judul' => 'Materi Uji', 'kelompok_usia' => '6-9']);
        Materi::factory()->create(['kelompok_usia' => '6-9']);
        ProgressMateri::create([
            'user_id' => $user->id, 'materi_id' => $materi->id,
            'materi_selesai' => true, 'materi_selesai_at' => now(),
            'video_ditonton' => true, 'video_ditonton_at' => now(),
        ]);

        AktivitasStimulasi::create([
            'user_id' => $user->id, 'tanggal' => now()->subMonths(2), 'aspek' => 'motorik_kasar',
            'jenis_stimulasi' => 'Tengkurap', 'durasi_menit' => 20, 'pelaku' => 'ibu', 'respons_anak' => 'Senang',
        ]);
        // Entri tab Praktik: tanpa jenis_stimulasi & durasi.
        AktivitasStimulasi::create([
            'user_id' => $user->id, 'materi_id' => $materi->id, 'tanggal' => now(),
            'aspek' => 'bicara_bahasa', 'pelaku' => 'ayah',
        ]);

        PenilaianPerkembangan::create([
            'user_id' => $user->id, 'anak_id' => $anak->id, 'tanggal_penilaian' => '2026-03-05',
            'usia_bulan' => 6, 'kelompok_usia' => '6-9',
            'skor_motorik_kasar' => 100, 'skor_motorik_halus' => 66.67,
            'skor_bicara_bahasa' => 33.33, 'skor_sosial_emosional' => 0, 'skor_total' => 50,
        ]);

        HasilKuesioner::create([
            'user_id' => $user->id, 'tipe_sesi' => 'pre', 'skor_pengetahuan' => 80,
            'kategori_pengetahuan' => 'Baik', 'skor_sikap' => 32, 'submitted_at' => '2026-02-01 09:00:00',
        ]);

        return $user;
    }

    public function test_excel_berisi_lima_sheet_sesuai_prd(): void
    {
        $this->responden();

        $sheet = (new DataPenelitianExport)->sheets();

        $this->assertSame([
            'Tabel 1 - Identitas Responden',
            'Tabel 2 - Kondisi Saat Lahir',
            'Tabel 3 - Aktivitas Stimulasi',
            'Tabel 4 - Perkembangan',
            'Tabel 5 - Penggunaan Aplikasi',
        ], array_map(fn ($s) => $s->title(), $sheet));

        // Tabel 1: satu baris per responden, usia dihitung lewat UsiaAnakService.
        $this->assertSame(['RSP-001', 'Ibu Uji'], array_slice($sheet[0]->array()[0], 0, 2));
        $this->assertSame([8, '6-9'], array_slice($sheet[0]->array()[0], 13, 2));

        // Tabel 2: label enum, bukan kode mentah.
        $this->assertSame(['RSP-001', 'AZ', null, 'Caesar (SC)', 2200], array_slice($sheet[1]->array()[0], 0, 5));
        $this->assertSame('BBLR (berat lahir rendah)', $sheet[1]->array()[0][7]);

        // Tabel 3: satu baris per entri; usia = usia pada TANGGAL ENTRI (8 − 2 = 6 bln), bukan hari ini.
        $aktivitas = $sheet[2]->array();
        $this->assertCount(2, $aktivitas);
        $this->assertSame(6, $aktivitas[0][2]);
        $this->assertSame(['Motorik Kasar', 'Tengkurap', 20, 'Ibu', 'Senang'], array_slice($aktivitas[0], 3, 5));
        // Entri tab Praktik: jenis diisi judul materi, durasi kosong.
        $this->assertSame(['Bicara & Bahasa', 'Praktik: Materi Uji', null, 'Ayah', null], array_slice($aktivitas[1], 3, 5));

        // Tabel 4: skor sebagai angka (bisa dihitung), bukan string "66,67%".
        $this->assertSame(['RSP-001', '2026-03-05', 6, '6-9', 100.0, 66.67, 33.33, 0.0, 50.0], $sheet[3]->array()[0]);

        // Tabel 5: 1 dari 2 materi kelompok usia anak saat ini + status/skor kedua test.
        $this->assertSame(['RSP-001', 1, 1, 1, 2, '1 dari 2'], array_slice($sheet[4]->array()[0], 0, 6));
        $this->assertSame(['2026-02-01', 80.0, 'Baik', 32.0, null, null, null, null], array_slice($sheet[4]->array()[0], 6));
    }

    public function test_responden_tanpa_data_anak_tidak_menggagalkan_export(): void
    {
        User::factory()->create(['kode_responden' => 'RSP-002']);

        $sheet = (new DataPenelitianExport)->sheets();

        $this->assertCount(1, $sheet[0]->array());
        // Tanpa anak: tidak muncul di Tabel 2, progres materi kosong (bukan "0 dari 0").
        $this->assertSame([], $sheet[1]->array());
        $this->assertSame([null, null], array_slice($sheet[4]->array()[0], 4, 2));
    }

    public function test_tombol_export_excel_mengunduh_file(): void
    {
        $this->freezeTime();
        Excel::fake();
        $this->responden();

        $this->actingAs($this->admin(), 'admin')->get(route('admin.export.excel'))->assertOk();

        Excel::assertDownloaded(DataPenelitianExport::namaFile('xlsx'));
    }

    public function test_csv_ringkas_berisi_identitas_dan_hasil_test(): void
    {
        $this->responden();

        $isi = $this->actingAs($this->admin(), 'admin')->get(route('admin.export.csv'))
            ->assertOk()
            ->assertDownload(DataPenelitianExport::namaFile('csv'))
            ->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $isi);
        // fputcsv mengapit field bersepasi; yang penting pemisahnya ';'.
        $this->assertStringContainsString('"Kode Responden";"Nama Orang Tua";Email;', $isi);
        // Skor berkoma desimal agar langsung terbaca Excel locale Indonesia.
        $this->assertStringContainsString('RSP-001;"Ibu Uji";', $isi);
        $this->assertStringContainsString(';80,00;Baik;32,00;', $isi);
        // Ringkas: tanpa kolom aktivitas/perkembangan.
        $this->assertStringNotContainsString('Tengkurap', $isi);
    }

    public function test_command_menulis_file_excel_ke_storage(): void
    {
        $this->freezeTime();
        Excel::fake();
        $this->responden();

        $this->artisan('export:data-penelitian --disk=local')->assertSuccessful();

        Excel::assertStored(DataPenelitianExport::namaFile('xlsx'), 'local');
    }

    public function test_export_tertutup_untuk_tamu_dan_orang_tua(): void
    {
        foreach (['admin.export.excel', 'admin.export.csv'] as $rute) {
            $this->get(route($rute))->assertRedirect('/admin');
            $this->actingAs(User::factory()->create())->get(route($rute))->assertRedirect('/admin');
        }
    }
}
