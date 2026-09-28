# StimuTumbuh — Design Reference

Sumber kebenaran **gaya visual saja**. Perilaku tetap mengikuti
`PRD_StimuTumbuh.md` (lihat precedence rule di `CLAUDE.md`).

Mockup: `design/mockup/orang-tua/*.png` (mobile) dan `design/mockup/admin/*.png` (desktop).

## Token

Semua token ada di `tailwind.config.js` → `theme.extend.colors`. Nilainya
diambil dari piksel mockup, jangan pakai warna Tailwind bawaan (gray/indigo)
untuk elemen baru.

| Token | Hex | Dipakai untuk |
|---|---|---|
| `brand` | #2E8C76 | tombol utama, kartu hero, menu aktif |
| `brand-soft` | #E4F2EA | tombol ikon bulat, kotak "Perlu bantuan?" |
| `brand-aktif` | #D7F0E8 | item sidebar admin aktif |
| `krem` | #FCF9F2 | latar halaman |
| `krem-tua` | #F6F2EB | thumbnail, badge netral, kepala tabel |
| `krem-garis` | #EDE5D8 | border input, garis pemisah |
| `ink` / `ink-muted` | #273730 / #64766E | teks utama / teks sekunder |
| `sukses` (+ `-bg`, `-badge`) | #48A874 | status selesai, "Naik x%" |
| `hangat` (+ `-bg`) | #EDA065 | status berjalan, kartu peringatan |
| `bahaya` | #D15C55 | hapus, ikon tempat sampah |
| `aspek-{kasar,halus,bicara,sosial}` (+ `-teks`) | — | chip aspek perkembangan |

Font: **Nunito** (400/600/700/800) via fonts.bunny.net.

## Pola komponen

- **Kartu:** `bg-white rounded-3xl`, bayangan sangat halus, padding lega.
- **Tombol utama:** `bg-brand text-white rounded-2xl font-bold`, tinggi ±56px di mobile.
- **Badge/chip:** `rounded-full px-3 py-1 text-sm font-bold`, latar pucat + teks warna senada.
- **Input:** `rounded-2xl border-krem-garis`, tinggi ±56px.
- **Orang tua (mobile-first):** header putih "Anak yang dipantau / Nama — N bulan"
  + tombol WhatsApp bulat di kanan; tab bar bawah tetap
  (Materi, Kalender, Perkembangan, Tes, Profil), aktif = `text-brand`.
- **Admin:** sidebar putih kiri (grup DATA / KONTEN, ikon + label, aktif =
  `bg-brand-aktif text-brand rounded-2xl`), nama & peran admin + Keluar di
  bawah sidebar; header "Area Peneliti" kecil di atas judul halaman; tabel
  dalam kartu putih dengan kepala `bg-krem-tua`.

## Elemen mockup yang BUKAN fitur

Jangan dibangun — itu kontrol prototipe untuk berpindah state mockup:

- Kotak "Tampilan prototipe" (Terbuka/Terkunci/Memuat/…)
- Tombol "Tampilkan skeleton"
- Kartu "Contoh aksi pada baris soal" di Kelola Soal
