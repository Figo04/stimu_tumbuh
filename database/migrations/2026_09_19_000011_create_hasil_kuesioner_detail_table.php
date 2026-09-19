<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_kuesioner_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_kuesioner_id')->constrained('hasil_kuesioner')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('kuesioner_soal')->cascadeOnDelete();
            $table->string('jawaban_responden');
            $table->timestamps();

            $table->unique(['hasil_kuesioner_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_kuesioner_detail');
    }
};
