@props([
    'label',
    'value',
    'accent' => 'violet',
])

@php
    $accents = [
        'violet' => 'from-violet-500 to-indigo-600 shadow-violet-500/25',
        'sky' => 'from-sky-500 to-blue-600 shadow-sky-500/25',
        'emerald' => 'from-emerald-500 to-teal-600 shadow-emerald-500/25',
        'amber' => 'from-amber-500 to-orange-600 shadow-amber-500/25',
        'rose' => 'from-rose-500 to-pink-600 shadow-rose-500/25',
    ];
    $bar = $accents[$accent] ?? $accents['violet'];
@endphp

<div {{ $attributes->merge(['class' => 'group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-soft ring-1 ring-slate-100/80 transition hover:-translate-y-0.5 hover:shadow-lg']) }}>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $bar }} opacity-90 shadow-lg"></div>
    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</p>
    <p class="mt-3 text-3xl font-bold tabular-nums tracking-tight text-slate-900">{{ $value }}</p>
    <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-gradient-to-br {{ $bar }} opacity-[0.07] blur-2xl transition group-hover:opacity-[0.12]"></div>
</div>
