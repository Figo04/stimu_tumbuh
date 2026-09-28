{{-- Tab "Tes": Pre-test lalu berganti sendiri ke Post-test (PRD §4 alur orang tua). Gating tetap di KuesionerController;
     saat terkunci/selesai tab membuka halaman status (tanpa form). --}}
@php
    $u = Auth::user();
    $aktif = request()->routeIs('pretest', 'posttest', 'tes');

    [$href, $status, $adaTes] = match (true) {
        ! $u->sudahPretest() => [route('pretest'), 'Pre-test belum diisi', true],
        $u->sudahKuesioner('post') => [route('tes'), 'Post-test selesai', false],
        $u->semuaMateriSelesai() => [route('posttest'), 'Post-test terbuka', true],
        default => [route('tes'), 'Post-test terkunci', false],
    };
@endphp

<x-tab-ortu :href="$href" :active="$aktif" label="Tes" :title="$status">
    <rect width="8" height="4" x="8" y="2" rx="1" /><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" /><path d="m9 14 2 2 4-4" />
    <x-slot:extra>
        <span class="sr-only">{{ $status }}</span>
        @if ($adaTes && ! $aktif)
            {{-- Titik penanda: ada tes yang menunggu diisi. --}}
            <span class="absolute right-1/2 top-2 -mr-4 h-2 w-2 rounded-full bg-hangat" aria-hidden="true"></span>
        @endif
    </x-slot:extra>
</x-tab-ortu>
