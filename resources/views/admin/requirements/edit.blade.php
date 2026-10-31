<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">{{ $module->name }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Edit persyaratan</h1>
        </div>
    </x-slot>

    <div class="ui-card max-w-2xl mx-auto p-6 sm:p-8">
        <form method="post" action="{{ route('admin.modules.requirements.update', [$module, $requirement]) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.requirements._form', ['requirement' => $requirement])
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="ui-btn-primary">Perbarui</button>
                <a href="{{ route('admin.modules.requirements.index', $module) }}" class="ui-btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
