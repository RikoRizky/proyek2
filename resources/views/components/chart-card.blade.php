@props([
    'title',
    'subtitle' => null,
    'canvasId',
    'height' => '320px',
])

<div {{ $attributes->merge(['class' => 'ui-card overflow-hidden']) }}>
    <div class="ui-section-header">
        <div>
            <h2 class="text-lg font-bold text-slate-900">{{ $title }}</h2>
            @if ($subtitle)
                <p class="mt-0.5 text-sm text-slate-600">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    <div class="p-4 sm:p-6">
        <div style="height: {{ $height }}">
            <canvas id="{{ $canvasId }}"></canvas>
        </div>
    </div>
</div>
