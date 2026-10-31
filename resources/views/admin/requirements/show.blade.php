<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">{{ $module->name }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ $requirement->title }}</h1>
        </div>
    </x-slot>

    @if ($requirement->description)
        <p class="mb-6 text-slate-600">{{ $requirement->description }}</p>
    @endif

    <form method="post" action="{{ route('admin.modules.requirements.destroy', [$module, $requirement]) }}" onsubmit="return confirm('Hapus persyaratan ini?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-500">Hapus persyaratan</button>
    </form>
</x-app-layout>
