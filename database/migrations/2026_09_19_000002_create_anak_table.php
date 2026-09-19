<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete(); // satu anak per akun
            $table->string('nama_inisial');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->integer('bb_lahir_gram')->nullable();
            $table->decimal('pb_lahir_cm', 4, 1)->nullable();
            $table->decimal('lingkar_kepala_cm', 4, 1)->nullable();
            $table->unsignedTinyInteger('usia_gestasi_minggu')->nullable();
            $table->enum('jenis_persalinan', ['normal', 'sc', 'vakum', 'forceps'])->nullable();
            $table->enum('kondisi_lahir', ['sehat', 'bblr', 'prematur', 'asfiksia', 'lain'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anak');
    }
};
