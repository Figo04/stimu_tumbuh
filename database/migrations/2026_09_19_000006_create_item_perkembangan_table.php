<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_perkembangan', function (Blueprint $table) {
            $table->id();
            $table->enum('aspek', ['motorik_kasar', 'motorik_halus', 'bicara_bahasa', 'sosial_emosional']);
            $table->enum('kelompok_usia', ['0-3', '3-6', '6-9', '9-12', '12-18', '18-24', '24-36']);
            $table->text('pertanyaan');
            $table->unsignedTinyInteger('urutan');
            $table->timestamps();

            $table->index(['kelompok_usia', 'aspek', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_perkembangan');
    }
};
