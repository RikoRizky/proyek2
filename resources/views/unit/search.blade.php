<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Unit Kerja · Program Studi</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Pencarian Dokumen, LKPS &amp; Arsip</h1>
            <p class="mt-1 text-sm text-slate-600">Cari butir kriteria, berkas LED (PPEPP), tabel LKPS, serta arsip dokumen tambahan untuk dicek atau diunduh instan.</p>
        </div>
    </x-slot>

    <x-unified-search-panel
        :is-perti="false"
        :items="$items"
        :module-options="$moduleOptions"
        :archive-categories="$archiveCategories"
        :initial-query="$initialQuery"
    />
</x-app-layout>
