<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-2xl border border-krem-garis bg-white px-6 py-3.5 text-base font-bold text-ink hover:bg-krem focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 disabled:opacity-40 transition']) }}>
    {{ $slot }}
</button>
