<x-admin-layout judul="Dashboard">
    {{-- Isi dashboard (stat cards, chart, Aktivitas Terbaru) dikerjakan Sesi 28–30. --}}
    <div class="rounded-lg border border-gray-200 bg-white p-6">
        <p class="text-gray-800">Halo, {{ auth('admin')->user()->nama }}.</p>
        <p class="mt-2 text-sm text-gray-500">
            Statistik dan grafik ditambahkan pada tahap berikutnya. Menu bertanda
            &ldquo;segera&rdquo; di samping belum aktif.
        </p>
    </div>
</x-admin-layout>
