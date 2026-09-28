<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-2xl bg-brand px-6 py-3.5 text-base font-bold text-white shadow-lg shadow-brand/20 hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 disabled:opacity-40 transition']) }}>
    {{ $slot }}
</button>
