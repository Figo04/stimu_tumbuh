<?php

namespace Database\Seeders;

use App\Models\ItemPerkembangan;
use Illuminate\Database\Seeder;

class ItemPerkembanganSeeder extends Seeder
{
    // Dari dokumen klien "Kuesioner Tumbuh Kembang utk web" (13 Okt 2026), teks disalin apa adanya.
    // Usia 12–17 bln → 12-18, 18–23 bln → 18-24, 24 bln → 24-36. Urutan = nomor di dokumen.
    private const ITEM = [
        '12-18' => [
            ['motorik', 'Apakah anak dapat berjalan menuju Ibu saat dipanggil dari jarak dekat?'],
            ['motorik', 'Apakah anak dapat mendorong mainan sambil berjalan tanpa mudah jatuh?'],
            ['motorik', 'Apakah anak dapat mengambil mainan di lantai lalu berdiri kembali?'],
            ['motorik', 'Apakah anak dapat menggulirkan atau menendang bola secara sederhana?'],
            ['motorik', 'Apakah anak dapat memasukkan benda besar ke dalam wadah?'],
            ['motorik', 'Apakah anak dapat mencoret kertas dengan krayon besar?'],
            ['motorik', 'Apakah anak mencoba makan sendiri menggunakan sendok?'],
            ['berpikir_belajar', 'Apakah anak dapat menyusun atau menumpuk balok besar?'],
            ['berpikir_belajar', 'Apakah anak dapat membalik halaman buku karton?'],
            ['berpikir_belajar', 'Apakah anak meniru gerakan sederhana yang Ibu contohkan (misalnya bertepuk tangan)?'],
            ['komunikasi', 'Apakah anak merespons saat diajak bicara (menoleh, bersuara, atau tersenyum)?'],
            ['komunikasi', 'Apakah anak menyampaikan keinginannya dengan suara, kata, atau gerakan (menunjuk, menggapai)?'],
            ['komunikasi', 'Apakah anak memperhatikan gambar saat Ibu membacakan buku bergambar?'],
            ['sosial_emosional', 'Apakah anak tampak senang dan tertawa saat bermain cilukba?'],
            ['sosial_emosional', 'Apakah anak tampak tenang dan merasa aman setelah dipeluk Ibu?'],
            ['sosial_emosional', 'Apakah anak dapat memilih salah satu dari dua benda yang ditawarkan?'],
            ['sosial_emosional', 'Apakah anak mau bermain bersama Ibu atau anggota keluarga?'],
        ],
        '18-24' => [
            ['motorik', 'Apakah anak dapat berjalan menuju orang tua atau mainan tanpa banyak terjatuh?'],
            ['motorik', 'Apakah anak dapat menendang bola yang diletakkan di depannya?'],
            ['motorik', 'Apakah anak dapat berjalan sambil membawa mainan ringan?'],
            ['motorik', 'Apakah anak dapat membungkuk mengambil mainan di lantai tanpa jatuh?'],
            ['motorik', 'Apakah anak dapat melangkahi benda yang sangat rendah (dengan bantuan)?'],
            ['motorik', 'Apakah anak dapat mencoret kertas dengan krayon?'],
            ['motorik', 'Apakah anak dapat memegang dan menggunakan sendok saat makan?'],
            ['berpikir_belajar', 'Apakah anak dapat menyusun balok bersama Ibu?'],
            ['berpikir_belajar', 'Apakah anak dapat memasukkan benda ke dalam wadah dengan tepat?'],
            ['berpikir_belajar', 'Apakah anak meniru kegiatan sehari-hari saat bermain peran (misalnya menyuapi boneka)?'],
            ['komunikasi', 'Apakah anak menunjuk atau menyebut nama benda saat Ibu membacakan buku bergambar?'],
            ['komunikasi', 'Apakah anak mengerti kalimat sederhana yang Ibu ucapkan saat bermain?'],
            ['komunikasi', 'Apakah anak mengikuti lagu anak dengan suara atau gerakan?'],
            ['komunikasi', 'Apakah anak dapat menyampaikan pilihannya saat ditawari dua pilihan sederhana?'],
            ['sosial_emosional', 'Apakah anak menikmati bermain bersama orang tua?'],
            ['sosial_emosional', 'Apakah anak menunjukkan perasaannya (senang, sedih, marah) dengan jelas, sehingga Ibu dapat menamai emosinya?'],
            ['sosial_emosional', 'Apakah anak mau bermain bergiliran dalam permainan sederhana (dengan bantuan)?'],
        ],
        '24-36' => [
            ['motorik', 'Apakah anak dapat berjalan dengan stabil saat bermain di pagi hari?'],
            ['motorik', 'Apakah anak dapat bermain bola (menggulirkan atau menendang)?'],
            ['motorik', 'Apakah anak dapat makan sendiri menggunakan sendok?'],
            ['motorik', 'Apakah anak dapat menyusun balok atau benda-benda kecil?'],
            ['berpikir_belajar', 'Apakah anak dapat menyusun balok atau benda dengan tujuan tertentu (misalnya menyusun menjadi menara)?'],
            ['berpikir_belajar', 'Apakah anak tertarik dan memperhatikan saat Ibu membacakan buku bergambar?'],
            ['berpikir_belajar', 'Apakah anak dapat memilih sendiri mainan atau benda yang diinginkan?'],
            ['komunikasi', 'Apakah anak menyebut nama benda atau gambar yang sering dilihat sehari-hari?'],
            ['komunikasi', 'Apakah anak mengerti dan merespons ajakan atau perintah sederhana dari Ibu?'],
            ['komunikasi', 'Apakah anak ikut bernyanyi atau menyimak saat Ibu bercerita dan bernyanyi sebelum tidur?'],
            ['sosial_emosional', 'Apakah anak aktif bermain bersama Ibu atau anggota keluarga?'],
            ['sosial_emosional', 'Apakah anak tampak tenang dan dekat dengan Ibu saat bercerita atau bernyanyi sebelum tidur?'],
            ['sosial_emosional', 'Apakah anak dapat menunjukkan perasaannya dan menjadi tenang setelah ditenangkan Ibu?'],
            ['sosial_emosional', 'Apakah anak mau mencoba melakukan sesuatu sendiri (misalnya makan sendiri)?'],
        ],
    ];

    public function run(): void
    {
        foreach (self::ITEM as $kelompok => $daftar) {
            foreach ($daftar as $i => [$aspek, $pertanyaan]) {
                // Per item (bukan hapus-isi ulang): FK penilaian_detail.item_id akan ikut menghapus
                // riwayat penilaian responden. Item yang sudah ada dilewati, yang hilang diisi.
                ItemPerkembangan::firstOrCreate(
                    ['kelompok_usia' => $kelompok, 'aspek' => $aspek, 'urutan' => $i + 1],
                    ['pertanyaan' => $pertanyaan],
                );
            }
        }
    }
}
