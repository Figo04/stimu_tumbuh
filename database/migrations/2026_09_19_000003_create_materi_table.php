<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->enum('aspek', ['motorik_kasar', 'motorik_halus', 'bicara_bahasa', 'sosial_emosional']);
            $table->enum('kelompok_usia', ['0-3', '3-6', '6-9', '9-12', '12-18', '18-24', '24-36']);
            $table->unsignedTinyInteger('urutan');
            $table->string('konten_view'); // dot-path Blade view, bukan raw HTML
            $table->string('video_youtube_id')->nullable();
            $table->timestamps();

            $table->index(['kelompok_usia', 'aspek', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
