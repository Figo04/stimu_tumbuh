<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuesioner_soal', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['pengetahuan', 'sikap']);
            $table->text('pertanyaan');
            $table->string('jawaban_benar')->nullable(); // hanya pengetahuan (B/S)
            $table->boolean('reverse_scored')->default(false); // hanya sikap
            $table->unsignedTinyInteger('urutan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuesioner_soal');
    }
};
