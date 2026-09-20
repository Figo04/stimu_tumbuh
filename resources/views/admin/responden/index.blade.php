<x-admin-layout judul="Responden">
    <section class="rounded-lg border border-gray-200 bg-white">
        <h2 class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800">
            Daftar Responden
            <span class="font-normal text-gray-500">({{ $responden->total() }})</span>
        </h2>

        @if ($responden->isEmpty())
            <p class="px-4 py-6 text-sm text-gray-500">Belum ada responden terdaftar.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-2 font-medium">Kode</th>
                            <th class="px-4 py-2 font-medium">Orang Tua</th>
                            <th class="px-4 py-2 font-medium">Hubungan</th>
                            <th class="px-4 py-2 font-medium">Kecamatan</th>
                            <th class="px-4 py-2 font-medium">Anak</th>
                            <th class="px-4 py-2 font-medium">JK</th>
                            <th class="px-4 py-2 font-medium">Tgl Lahir</th>
                            <th class="px-4 py-2 font-medium">Usia</th>
                            <th class="px-4 py-2 font-medium">Kelompok</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($responden as $r)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2 font-medium">{{ $r->kode_responden }}</td>
                                <td class="px-4 py-2">
                                    {{ $r->nama }}
                                    <span class="block text-xs text-gray-500">{{ $r->email }}{{ $r->no_hp ? ' · '.$r->no_hp : '' }}</span>
                                </td>
                                <td class="px-4 py-2">{{ ucfirst($r->hubungan_dengan_anak) }}</td>
                                <td class="px-4 py-2">{{ $r->kecamatan ?: '—' }}</td>
                                @if ($r->anak)
                                    <td class="px-4 py-2">{{ $r->anak->nama_inisial }}</td>
                                    <td class="px-4 py-2">{{ $r->anak->jenis_kelamin }}</td>
                                    <td class="whitespace-nowrap px-4 py-2">{{ $r->anak->tanggal_lahir->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        {{ \App\Services\UsiaAnakService::usiaBulan($r->anak->tanggal_lahir) }} bln
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2">{{ $r->anak->kelompok_usia }} bln</td>
                                @else
                                    <td class="px-4 py-2 text-gray-400" colspan="5">Data anak belum diisi</td>
                                @endif
                                <td class="whitespace-nowrap px-4 py-2 text-right">
                                    <a href="{{ route('admin.responden.show', $r) }}"
                                       class="font-medium text-emerald-700 hover:underline">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-200 px-4 py-3">{{ $responden->links() }}</div>
        @endif
    </section>
</x-admin-layout>
