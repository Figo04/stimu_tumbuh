@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-2xl border-krem-garis px-4 py-3.5 text-base text-ink placeholder:text-ink-muted/60 focus:border-brand focus:ring-brand disabled:bg-krem-tua']) }}>
