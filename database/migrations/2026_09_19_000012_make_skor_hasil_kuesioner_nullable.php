<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Skor dihitung di Sesi 10; sampai itu pre-test tersimpan tanpa skor.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hasil_kuesioner', function (Blueprint $table) {
            $table->decimal('skor_pengetahuan', 5, 2)->nullable()->change();
            $table->enum('kategori_pengetahuan', ['Baik', 'Cukup', 'Kurang'])->nullable()->change();
            $table->decimal('skor_sikap', 5, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('hasil_kuesioner', function (Blueprint $table) {
            $table->decimal('skor_pengetahuan', 5, 2)->nullable(false)->change();
            $table->enum('kategori_pengetahuan', ['Baik', 'Cukup', 'Kurang'])->nullable(false)->change();
            $table->decimal('skor_sikap', 5, 2)->nullable(false)->change();
        });
    }
};
