<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Modul</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Kriteria &amp; persyaratan</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola struktur akreditasi</p>
        </div>
    </x-slot>

    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.modules.create') }}" class="ui-btn-primary">+ Modul baru</a>
    </div>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Persyaratan</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($modules as $module)
                    <tr>
                        <td class="font-semibold text-slate-900">{{ $module->name }}</td>
                        <td><span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">{{ $module->requirements_count }}</span></td>
                        <td class="text-right">
                            <a href="{{ route('admin.modules.show', $module) }}" class="font-semibold text-violet-600 hover:text-violet-500">Detail</a>
                            <span class="mx-2 text-slate-300">|</span>
                            <a href="{{ route('admin.modules.edit', $module) }}" class="font-semibold text-slate-600 hover:text-slate-900">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3">{{ $modules->links() }}</div>
    </div>
</x-app-layout>
