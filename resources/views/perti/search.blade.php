<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Perguruan Tinggi · Monitoring &amp; Validasi</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Pencarian Dokumen, LKPS &amp; Arsip</h1>
                <p class="mt-1 text-sm text-slate-600">Cari butir kriteria, berkas LED PPEPP, tabel LKPS, serta dokumen tambahan (data mahasiswa, nilai, SK) lalu cek dan unduh langsung.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 transition">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </x-slot>

    <x-unified-search-panel
        :is-perti="true"
        :items="$items"
        :module-options="$moduleOptions"
        :archive-categories="$archiveCategories"
        :initial-query="$initialQuery"
        :prodis="$prodis"
        :selected-prodi-id="$selectedProdiId"
    />
</x-app-layout>
