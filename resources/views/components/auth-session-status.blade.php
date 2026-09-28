@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-2xl bg-sukses-bg px-4 py-3 text-sm font-semibold text-sukses']) }}>
        {{ $status }}
    </div>
@endif
