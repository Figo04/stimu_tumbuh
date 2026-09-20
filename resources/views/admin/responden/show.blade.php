@php
    $anak = $responden->anak;

    $orangTua = [
        'Kode responden' => $responden->kode_responden,
        'Nama' => $responden->nama,
        'Email' => $responden->email,
        'No. HP' => $responden->no_hp,
        'Hubungan dengan anak' => ucfirst($responden->hubungan_dengan_anak),
        'Pendidikan terakhir' => $responden->pendidikan_terakhir,
        'Pekerjaan' => $responden->pekerjaan,
        'Kecamatan' => $responden->kecamatan,
        'Alamat' => $responden->alamat,
        'Terdaftar' => $responden->created_at?->format('d/m/Y H:i'),
    ];

    // PRD §5 tabel `anak` — identitas + kondisi saat lahir (variabel pengelompokan analisis).
    $identitasAnak = $anak ? [
        'Inisial' => $anak->nama_inisial,
        'Jenis kelamin' => $anak->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
        'Tanggal lahir' => $anak->tanggal_lahir->format('d/m/Y'),
        'Usia saat ini' => \App\Services\UsiaAnakService::usiaBulan($anak->tanggal_lahir).' bulan',
        'Kelompok usia' => $anak->kelompok_usia.' bulan',
    ] : [];

    $kondisiLahir = $anak ? [
        'Usia gestasi' => $anak->usia_gestasi_minggu ? $anak->usia_gestasi_minggu.' minggu' : null,
        'Jenis persalinan' => \App\Models\Anak::JENIS_PERSALINAN[$anak->jenis_persalinan] ?? null,
        'Berat lahir' => $anak->bb_lahir_gram ? number_format($anak->bb_lahir_gram, 0, ',', '.').' gram' : null,
        'Panjang lahir' => $anak->pb_lahir_cm ? $anak->pb_lahir_cm.' cm' : null,
        'Lingkar kepala' => $anak->lingkar_kepala_cm ? $anak->lingkar_kepala_cm.' cm' : null,
        'Kondisi lahir' => \App\Models\Anak::KONDISI_LAHIR[$anak->kondisi_lahir] ?? null,
    ] : [];
@endphp

<x-admin-layout judul="Responden {{ $responden->kode_responden }}">
    <a href="{{ route('admin.responden.index') }}" class="text-sm font-medium text-emerald-700 hover:underline">
        &larr; Kembali ke daftar responden
    </a>

    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        @foreach ([
            ['Identitas Orang Tua', $orangTua],
            ['Identitas Anak', $identitasAnak],
            ['Kondisi Anak Saat Lahir', $kondisiLahir],
        ] as [$judul, $baris])
            <section class="rounded-lg border border-gray-200 bg-white">
                <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">{{ $judul }}</h2>

                @if (empty($baris))
                    <p class="px-4 py-6 text-sm text-gray-500">Data anak belum diisi.</p>
                @else
                    <dl class="divide-y divide-gray-100 text-sm">
                        @foreach ($baris as $label => $nilai)
                            <div class="flex gap-4 px-4 py-2">
                                <dt class="w-44 shrink-0 text-gray-500">{{ $label }}</dt>
                                <dd class="text-gray-800">{{ $nilai !== null && $nilai !== '' ? $nilai : '—' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </section>
        @endforeach
    </div>

    <p class="mt-4 text-sm text-gray-500">
        Hasil pre/post-test responden ini ada di
        <a href="{{ route('admin.hasil-test.show', $responden) }}" class="font-medium text-emerald-700 hover:underline">Menu Hasil Test</a>.
    </p>
</x-admin-layout>
