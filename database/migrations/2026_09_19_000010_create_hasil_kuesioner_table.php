<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_kuesioner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipe_sesi', ['pre', 'post']);
            $table->decimal('skor_pengetahuan', 5, 2);
            $table->enum('kategori_pengetahuan', ['Baik', 'Cukup', 'Kurang']);
            $table->decimal('skor_sikap', 5, 2);
            $table->timestamp('submitted_at');
            $table->timestamps();

            // Pre/post-test final: satu kali per responden, ditegakkan di level DB.
            $table->unique(['user_id', 'tipe_sesi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_kuesioner');
    }
};
