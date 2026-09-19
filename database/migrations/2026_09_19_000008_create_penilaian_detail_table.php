<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaian_perkembangan')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('item_perkembangan')->cascadeOnDelete();
            $table->boolean('jawaban'); // true = Ya
            $table->timestamps();

            $table->unique(['penilaian_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_detail');
    }
};
