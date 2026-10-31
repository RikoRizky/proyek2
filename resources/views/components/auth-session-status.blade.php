@props(['status'])

@if ($status)
    <div class="mb-4 rounded-xl border border-emerald-200/80 bg-emerald-50/90 px-4 py-3 text-sm font-medium text-emerald-900 shadow-sm">
        {{ $status }}
    </div>
@endif
