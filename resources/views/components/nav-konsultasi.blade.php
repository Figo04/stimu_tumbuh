{{-- Tombol konsultasi → WhatsApp tim peneliti (PRD §3.1). Nomor kosong = tidak tampil. --}}
@props(['responsive' => false])

@if ($nomor = config('services.wa_konsultasi'))
    @php
        $pesan = 'Halo tim peneliti StimuTumbuh, saya responden '.Auth::user()->kode_responden.' ingin berkonsultasi.';
        $kelas = $responsive
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-emerald-700 hover:bg-gray-50'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-emerald-700 hover:text-emerald-800';
    @endphp
    <a href="https://wa.me/{{ $nomor }}?text={{ rawurlencode($pesan) }}" target="_blank" rel="noopener" class="{{ $kelas }}">
        Konsultasi (WhatsApp)
    </a>
@endif
