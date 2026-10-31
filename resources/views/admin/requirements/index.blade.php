<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.modules.show', $module) }}" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition-all hover:bg-slate-50 hover:text-slate-900" title="Kembali ke Modul">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">{{ $module->name }}</p>
                    <h1 class="mt-0.5 text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">Persyaratan</h1>
                </div>
            </div>
            <a href="{{ route('admin.modules.requirements.create', $module) }}" class="ui-btn-primary shrink-0 text-sm">+ Tambah</a>
        </div>
    </x-slot>

    <div class="ui-card divide-y divide-slate-100 overflow-hidden">
        @forelse ($requirements as $req)
            <div class="flex flex-wrap items-start justify-between gap-4 px-6 py-5 transition hover:bg-violet-50/25">
                <div class="min-w-0">
                    <p class="font-semibold text-slate-900">{{ $req->title }}</p>
                    @if ($req->description)
                        <p class="mt-1 text-sm text-slate-600">{{ $req->description }}</p>
                    @endif
                </div>
                <div class="flex shrink-0 gap-3 text-sm font-semibold">
                    <a href="{{ route('admin.modules.requirements.show', [$module, $req]) }}" class="text-slate-600 hover:text-slate-900">Detail</a>
                    <a href="{{ route('admin.modules.requirements.edit', [$module, $req]) }}" class="text-violet-600 hover:text-violet-500">Edit</a>
                </div>
            </div>
        @empty
            <div class="ui-empty text-sm">Belum ada persyaratan.</div>
        @endforelse
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3">{{ $requirements->links() }}</div>
    </div>
</x-app-layout>
