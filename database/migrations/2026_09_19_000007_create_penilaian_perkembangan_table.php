<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_perkembangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('anak_id')->constrained('anak')->cascadeOnDelete();
            $table->date('tanggal_penilaian');
            $table->unsignedTinyInteger('usia_bulan');
            $table->enum('kelompok_usia', ['0-3', '3-6', '6-9', '9-12', '12-18', '18-24', '24-36']); // snapshot saat penilaian
            $table->decimal('skor_motorik_kasar', 5, 2);
            $table->decimal('skor_motorik_halus', 5, 2);
            $table->decimal('skor_bicara_bahasa', 5, 2);
            $table->decimal('skor_sosial_emosional', 5, 2);
            $table->decimal('skor_total', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_perkembangan');
    }
};
