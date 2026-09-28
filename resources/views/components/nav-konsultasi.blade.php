{{-- Tombol konsultasi → WhatsApp tim peneliti (PRD §3.1). Nomor kosong = tidak tampil. --}}
@if ($nomor = config('services.wa_konsultasi'))
    @php($pesan = 'Halo tim peneliti StimuTumbuh, saya responden '.Auth::user()->kode_responden.' ingin berkonsultasi.')
    <a href="https://wa.me/{{ $nomor }}?text={{ rawurlencode($pesan) }}" target="_blank" rel="noopener"
       aria-label="Konsultasi dengan tim peneliti lewat WhatsApp" title="Konsultasi (WhatsApp)"
       class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand hover:bg-brand-aktif">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />
        </svg>
    </a>
@endif
