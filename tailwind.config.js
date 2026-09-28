import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            // Diambil langsung dari piksel mockup (design/mockup). Lihat DESIGN.md.
            colors: {
                brand: {
                    DEFAULT: '#2E8C76', // tombol utama, kartu hero, menu aktif
                    muda: '#62A998',    // track progress di atas kartu hijau
                    soft: '#E4F2EA',    // latar tombol ikon (WhatsApp), kotak info
                    aktif: '#D7F0E8',   // latar item sidebar admin yang aktif
                },
                krem: {
                    DEFAULT: '#FCF9F2', // latar halaman
                    tua: '#F6F2EB',     // thumbnail, badge netral, kepala tabel
                    garis: '#EDE5D8',   // border input & pemisah baris
                },
                ink: {
                    DEFAULT: '#273730', // teks utama & judul
                    muted: '#64766E',   // teks deskripsi & label
                },
                sukses: {
                    DEFAULT: '#48A874', // teks "Selesai", "Naik x%"
                    bg: '#E2F2EB',      // kartu materi yang selesai
                    badge: '#CBE7D9',   // badge "Selesai"
                },
                hangat: {
                    DEFAULT: '#EDA065', // oranye chart "berjalan"
                    bg: '#F7EDD8',      // badge "Sedang dibaca", kartu peringatan
                },
                bahaya: '#D15C55',
                aspek: {
                    kasar: { DEFAULT: '#E3EBE2', teks: '#2E8C76' },
                    halus: { DEFAULT: '#F7EDD8', teks: '#273730' },
                    bicara: { DEFAULT: '#E5EBEC', teks: '#418DC2' },
                    sosial: { DEFAULT: '#F7E7E2', teks: '#D66E74' },
                },
            },
        },
    },

    plugins: [forms],
};
