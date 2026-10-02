<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Materi = video saja (keputusan klien, 2 Okt 2026): view konten teks sudah dihapus.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->dropColumn('konten_view');
        });
    }

    public function down(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->string('konten_view')->nullable()->after('urutan');
        });
    }
};
