<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Berkelanjutan, multi-entri: sengaja tanpa unique (user_id, tanggal).
        Schema::create('aktivitas_stimulasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('materi_id')->nullable()->constrained('materi')->nullOnDelete();
            $table->date('tanggal');
            $table->enum('aspek', ['motorik_kasar', 'motorik_halus', 'bicara_bahasa', 'sosial_emosional']);
            $table->string('jenis_stimulasi')->nullable();
            $table->unsignedSmallInteger('durasi_menit')->nullable();
            $table->enum('pelaku', ['ibu', 'ayah', 'pengasuh', 'lain'])->default('ibu');
            $table->text('respons_anak')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aktivitas_stimulasi');
    }
};
