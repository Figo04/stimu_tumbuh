<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Revisi klien 19 Okt 2026: soal pre/post-test = dokumen "Kuesioner Tumbuh Kembang utk web"
// (Ya/Tidak per kelompok usia & kategori), menggantikan soal pengetahuan (B/S) + sikap (SS..STS).
// Soal & hasil lama hanya PLACEHOLDER, jadi ketiga tabel dibuat ulang (data lama dibuang) —
// juga menghindari ALTER ADD COLUMN NOT NULL yang ditolak SQLite.
return new class extends Migration
{
    private const KELOMPOK_USIA = ['12-18', '18-24', '24-36'];

    public function up(): void
    {
        $this->drop();

        Schema::create('kuesioner_soal', function (Blueprint $table) {
            $table->id();
            $table->enum('aspek', ['motorik', 'berpikir_belajar', 'komunikasi', 'sosial_emosional']);
            $table->enum('kelompok_usia', self::KELOMPOK_USIA);
            $table->text('pertanyaan');
            $table->unsignedTinyInteger('urutan');
            $table->timestamps();

            $table->index(['kelompok_usia', 'urutan']);
        });

        Schema::create('hasil_kuesioner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipe_sesi', ['pre', 'post']);
            // Kelompok soal yang dijawab; post-test memakai kelompok pre-test agar bisa dibandingkan.
            $table->enum('kelompok_usia', self::KELOMPOK_USIA);
            $table->decimal('skor_motorik', 5, 2)->nullable();
            $table->decimal('skor_berpikir_belajar', 5, 2)->nullable();
            $table->decimal('skor_komunikasi', 5, 2)->nullable();
            $table->decimal('skor_sosial_emosional', 5, 2)->nullable();
            $table->decimal('skor_total', 5, 2)->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();

            // Pre/post-test final: satu kali per responden, ditegakkan di level DB.
            $table->unique(['user_id', 'tipe_sesi']);
        });

        Schema::create('hasil_kuesioner_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_kuesioner_id')->constrained('hasil_kuesioner')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('kuesioner_soal')->cascadeOnDelete();
            $table->boolean('jawaban_responden'); // true = Ya
            $table->timestamps();

            $table->unique(['hasil_kuesioner_id', 'soal_id']);
        });
    }

    /** Bentuk lama (create 2026_09_19_000009–11 + skor nullable 000012); datanya tidak kembali. */
    public function down(): void
    {
        $this->drop();

        Schema::create('kuesioner_soal', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['pengetahuan', 'sikap']);
            $table->text('pertanyaan');
            $table->string('jawaban_benar')->nullable();
            $table->boolean('reverse_scored')->default(false);
            $table->unsignedTinyInteger('urutan');
            $table->timestamps();
        });

        Schema::create('hasil_kuesioner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipe_sesi', ['pre', 'post']);
            $table->decimal('skor_pengetahuan', 5, 2)->nullable();
            $table->enum('kategori_pengetahuan', ['Baik', 'Cukup', 'Kurang'])->nullable();
            $table->decimal('skor_sikap', 5, 2)->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->unique(['user_id', 'tipe_sesi']);
        });

        Schema::create('hasil_kuesioner_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_kuesioner_id')->constrained('hasil_kuesioner')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('kuesioner_soal')->cascadeOnDelete();
            $table->string('jawaban_responden');
            $table->timestamps();

            $table->unique(['hasil_kuesioner_id', 'soal_id']);
        });
    }

    private function drop(): void
    {
        Schema::dropIfExists('hasil_kuesioner_detail');
        Schema::dropIfExists('hasil_kuesioner');
        Schema::dropIfExists('kuesioner_soal');
    }
};
