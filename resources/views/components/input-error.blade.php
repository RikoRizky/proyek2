@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'rounded-xl border border-red-100 bg-red-50/90 px-3 py-2 text-sm font-medium text-red-700']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
