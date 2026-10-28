<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Data primer tambahan dari klien (29 Okt 2026): umur & asuransi orang tua,
 * kondisi lahir boleh lebih dari satu, riwayat kehamilan & kebiasaan ibu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('umur')->nullable()->after('hubungan_dengan_anak');
            $table->string('asuransi')->nullable()->after('pekerjaan');
        });

        // enum → teks berisi JSON array; nilai lama dibungkus jadi ["sehat"].
        Schema::table('anak', function (Blueprint $table) {
            $table->text('kondisi_lahir')->nullable()->change();
            $table->boolean('anemia_hamil')->nullable();
            $table->boolean('penyakit_hamil')->nullable();
            $table->string('penyakit_hamil_ket')->nullable();
            $table->boolean('penyakit_keturunan')->nullable();
            $table->string('penyakit_keturunan_ket')->nullable();
            $table->text('kebiasaan_ibu')->nullable();
            $table->string('kebiasaan_ibu_lain')->nullable();
        });

        DB::table('anak')->whereNotNull('kondisi_lahir')->eachById(fn ($a) => DB::table('anak')
            ->where('id', $a->id)->update(['kondisi_lahir' => json_encode([$a->kondisi_lahir])]));
    }

    public function down(): void
    {
        // Kembali ke satu nilai: ambil pilihan pertama.
        DB::table('anak')->whereNotNull('kondisi_lahir')->eachById(fn ($a) => DB::table('anak')
            ->where('id', $a->id)->update(['kondisi_lahir' => json_decode($a->kondisi_lahir)[0] ?? null]));

        Schema::table('anak', function (Blueprint $table) {
            $table->enum('kondisi_lahir', ['sehat', 'bblr', 'prematur', 'asfiksia', 'lain'])->nullable()->change();
            $table->dropColumn([
                'anemia_hamil', 'penyakit_hamil', 'penyakit_hamil_ket',
                'penyakit_keturunan', 'penyakit_keturunan_ket', 'kebiasaan_ibu', 'kebiasaan_ibu_lain',
            ]);
        });

        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['umur', 'asuransi']));
    }
};
