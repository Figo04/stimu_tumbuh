<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('materi_id')->constrained('materi')->cascadeOnDelete();
            $table->boolean('materi_selesai')->default(false);
            $table->timestamp('materi_selesai_at')->nullable();
            $table->boolean('video_ditonton')->default(false);
            $table->timestamp('video_ditonton_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'materi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_materi');
    }
};
