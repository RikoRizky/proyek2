<button {{ $attributes->merge(['type' => 'button', 'class' => 'ui-btn-secondary inline-flex items-center px-5 py-2.5 text-sm font-semibold disabled:cursor-not-allowed disabled:opacity-50']) }}>
    {{ $slot }}
</button>
