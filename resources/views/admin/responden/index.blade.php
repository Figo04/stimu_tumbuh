<x-admin-layout judul="Responden">
    <section class="overflow-hidden rounded-3xl bg-white shadow-sm">
        <h2 class="px-6 py-5 text-lg font-extrabold">
            Daftar Responden
            <span class="font-bold text-ink-muted">({{ $responden->total() }})</span>
        </h2>

        @if ($responden->isEmpty())
            <p class="px-6 pb-6 text-ink-muted">Belum ada responden terdaftar.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-krem-tua text-left text-sm font-bold text-ink-muted">
                        <tr>
                            <th class="px-6 py-3">Kode</th>
                            <th class="px-6 py-3">Orang Tua</th>
                            <th class="px-6 py-3">Hubungan</th>
                            <th class="px-6 py-3">Kecamatan</th>
                            <th class="px-6 py-3">Anak</th>
                            <th class="px-6 py-3">JK</th>
                            <th class="px-6 py-3">Tgl Lahir</th>
                            <th class="px-6 py-3">Usia</th>
                            <th class="px-6 py-3">Kelompok</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-krem-garis">
                        @foreach ($responden as $r)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 font-bold">{{ $r->kode_responden }}</td>
                                <td class="px-6 py-4">
                                    {{ $r->nama }}
                                    <span class="block text-xs text-ink-muted">{{ $r->email }}{{ $r->no_hp ? ' · '.$r->no_hp : '' }}</span>
                                </td>
                                <td class="px-6 py-4">{{ ucfirst($r->hubungan_dengan_anak) }}</td>
                                <td class="px-6 py-4">{{ $r->kecamatan ?: '—' }}</td>
                                @if ($r->anak)
                                    <td class="px-6 py-4">{{ $r->anak->nama_inisial }}</td>
                                    <td class="px-6 py-4">{{ $r->anak->jenis_kelamin }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $r->anak->tanggal_lahir->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        {{ \App\Services\UsiaAnakService::usiaBulan($r->anak->tanggal_lahir) }} bln
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $r->anak->kelompok_usia }} bln</td>
                                @else
                                    <td class="px-6 py-4 text-ink-muted/60" colspan="5">Data anak belum diisi</td>
                                @endif
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a href="{{ route('admin.responden.show', $r) }}"
                                       class="font-bold text-ink hover:text-brand">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-krem-garis px-6 py-4">{{ $responden->links() }}</div>
        @endif
    </section>
</x-admin-layout>
