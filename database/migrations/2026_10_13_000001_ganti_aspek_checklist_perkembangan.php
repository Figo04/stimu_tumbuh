<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Checklist perkembangan memakai 4 kategori dokumen klien "Kuesioner Tumbuh Kembang utk web"
// (13 Okt 2026): Motorik / Berpikir & Belajar / Komunikasi / Sosial & Emosional.
// Materi & aktivitas stimulasi tetap memakai 4 aspek lama (Materi::ASPEK).
return new class extends Migration
{
    private const LAMA = ['motorik_kasar', 'motorik_halus', 'bicara_bahasa', 'sosial_emosional'];

    private const BARU = ['motorik', 'berpikir_belajar', 'komunikasi', 'sosial_emosional'];

    public function up(): void
    {
        // Item beraspek lama = PLACEHOLDER (Sesi 22); cascade ikut menghapus jawaban per item-nya.
        DB::table('item_perkembangan')->whereIn('aspek', ['motorik_kasar', 'motorik_halus', 'bicara_bahasa'])->delete();
        DB::table('item_perkembangan')->where('pertanyaan', 'like', '[PLACEHOLDER]%')->delete();

        Schema::table('item_perkembangan', function (Blueprint $table) {
            $table->enum('aspek', self::BARU)->change();
        });

        // Penilaian lama (dari item placeholder) tetap ada; skor aspek barunya kosong.
        Schema::table('penilaian_perkembangan', function (Blueprint $table) {
            $table->dropColumn(['skor_motorik_kasar', 'skor_motorik_halus', 'skor_bicara_bahasa']);
        });
        Schema::table('penilaian_perkembangan', function (Blueprint $table) {
            $table->decimal('skor_motorik', 5, 2)->nullable()->after('kelompok_usia');
            $table->decimal('skor_berpikir_belajar', 5, 2)->nullable()->after('skor_motorik');
            $table->decimal('skor_komunikasi', 5, 2)->nullable()->after('skor_berpikir_belajar');
        });
    }

    public function down(): void
    {
        // Seluruh item dokumen klien dibuang (termasuk sosial_emosional) — sebelum migration ini hanya ada placeholder.
        DB::table('item_perkembangan')->whereNot('pertanyaan', 'like', '[PLACEHOLDER]%')->delete();

        Schema::table('item_perkembangan', function (Blueprint $table) {
            $table->enum('aspek', self::LAMA)->change();
        });

        Schema::table('penilaian_perkembangan', function (Blueprint $table) {
            $table->dropColumn(['skor_motorik', 'skor_berpikir_belajar', 'skor_komunikasi']);
        });
        Schema::table('penilaian_perkembangan', function (Blueprint $table) {
            $table->decimal('skor_motorik_kasar', 5, 2)->nullable()->after('kelompok_usia');
            $table->decimal('skor_motorik_halus', 5, 2)->nullable()->after('skor_motorik_kasar');
            $table->decimal('skor_bicara_bahasa', 5, 2)->nullable()->after('skor_motorik_halus');
        });
    }
};
