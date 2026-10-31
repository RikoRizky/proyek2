@php
    use App\Enums\UserRole;
    use App\Models\Module;
    $role = auth()->user()->role;

    $navLink = function (bool $active) {
        return $active
            ? 'bg-white/10 text-white shadow-inner ring-1 ring-white/10'
            : 'text-slate-300 hover:bg-white/5 hover:text-white';
    };

    $subNavLink = function (bool $active) {
        return $active
            ? 'bg-white/10 text-white ring-1 ring-white/10'
            : 'text-slate-400 hover:bg-white/5 hover:text-slate-100';
    };

    $uploadModules = $role === UserRole::Prodi
        ? Module::query()->orderBy('sort_order')->get()
        : collect();

    $uploadMenuActive = request()->routeIs('unit.submissions.module');

    $pertiProdis = $role === UserRole::Perti
        ? \App\Models\Prodi::query()
            ->where('perti_id', auth()->user()->pertiProfile?->id)
            ->with('user')
            ->orderBy('id')
            ->get()
        : collect();

    $pertiModules = $role === UserRole::Perti
        ? Module::query()->orderBy('sort_order')->get()
        : collect();

    $pertiProgressActive = request()->routeIs('perti.prodis.progress') || request()->routeIs('perti.prodis.modul');
    $activeProdiId = request()->route('prodi');
@endphp

{{-- Mobile top bar (in document flow) --}}
<header class="sticky top-0 z-30 flex h-14 shrink-0 items-center justify-between gap-3 border-b border-slate-200/80 bg-white/90 px-4 shadow-sm backdrop-blur-md lg:hidden">
    <button type="button" @click="sidebarOpen = true" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm" aria-label="Buka menu">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
    </button>
    <span class="truncate text-sm font-semibold text-slate-800">{{ config('app.name', 'SILADATA') }}</span>
    <a href="{{ route('profile.edit') }}">
        <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" class="h-9 w-9 shrink-0 rounded-xl shadow-md ring-2 ring-white object-cover bg-slate-200">
    </a>
</header>

{{-- Mobile backdrop --}}
<div
    x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden"
    style="display: none;"
    @click="sidebarOpen = false"
></div>

{{-- Sidebar: fixed on all breakpoints; main area uses lg:pl-64 --}}
<aside
    class="fixed inset-y-0 left-0 z-50 flex w-[min(18rem,88vw)] flex-col border-r border-white/10 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100 shadow-2xl transition-transform duration-300 ease-out lg:z-40 lg:w-64 lg:max-w-none lg:translate-x-0 lg:shadow-xl"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>
    <div class="flex h-16 items-center gap-3 border-b border-white/10 px-5">
        <img
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl shadow-lg shadow-violet-500/30 object-contain bg-white"
            src="{{ asset('images/logoname.png') }}"
            alt="SILADATA"
        />
        <a href="{{ route('dashboard') }}" class="min-w-0 leading-tight" @click="sidebarOpen = false">
            <span class="block truncate text-sm font-semibold tracking-tight text-white">SILADATA</span>
            <span class="block truncate text-[11px] font-medium text-slate-400">Sistem Layanan Dokumen Akreditasi</span>
        </a>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-6 text-sm">
        
        {{-- SECTION: UTAMA --}}
        <div class="space-y-1">
            <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Utama</p>
            <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ $navLink(request()->routeIs('dashboard')) }}">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                Ringkasan
            </a>

            @if ($role === UserRole::Admin)
                <a href="{{ route('admin.home') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ $navLink(request()->routeIs('admin.home')) }}">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                    Panel admin
                </a>
            @endif
        </div>

        @if ($role === UserRole::Admin)
            {{-- SECTION: MANAJEMEN --}}
            <div class="space-y-1">
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Manajemen</p>
                <a href="{{ route('admin.users.index') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ $navLink(request()->routeIs('admin.users.*') || request()->routeIs('admin.pertis.*') || request()->routeIs('admin.prodis.*')) }}">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    Kelola Akun
                </a>
                <a href="{{ route('admin.modules.index') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ $navLink(request()->routeIs('admin.modules.*') || request()->routeIs('admin.modules.requirements.*')) }}">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.051.2-2.966.55M12 6.042A8.967 8.967 0 0118 3.75c1.052 0 2.051.2 2.966.55M12 6.042v8.458m0 0a8.967 8.967 0 01-6 3.292m6-3.292a8.967 8.967 0 006 3.292"/></svg>
                    Modul &amp; syarat
                </a>
            </div>

            {{-- SECTION: AKTIVITAS --}}
            <div class="space-y-1">
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Aktivitas</p>
                <a href="{{ route('admin.submissions.index') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ $navLink(request()->routeIs('admin.submissions.*')) }}">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    Semua dokumen
                </a>
                <a href="{{ route('admin.transactions.index') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ $navLink(request()->routeIs('admin.transactions.*')) }}">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                    </svg>
                    Riwayat Transaksi
                </a>
                <a href="{{ route('admin.discussions.index') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ $navLink(request()->routeIs('admin.discussions.*')) }}">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>
                    </svg>
                    Diskusi masuk
                </a>
                <a href="{{ route('admin.broadcast.index') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ $navLink(request()->routeIs('admin.broadcast.*')) }}">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                    Kirim Notifikasi
                </a>
            </div>

            {{-- SECTION: ANALITIK & LAPORAN --}}
            <div class="space-y-1">
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Analitik &amp; Laporan</p>
                <a href="{{ route('admin.analytics') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ $navLink(request()->routeIs('admin.analytics')) }}">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    Grafik progress
                </a>
                <a href="{{ route('admin.reports.pdf') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    Export PDF
                </a>
            </div>
        @endif

        @if ($role === UserRole::Perti)
            {{-- SECTION: MANAJEMEN --}}
            <div class="space-y-1 mt-6">
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Manajemen</p>
                <a href="{{ route('perti.prodis.index') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ $navLink(request()->routeIs('perti.prodis.index') || request()->routeIs('perti.prodis.create') || request()->routeIs('perti.prodis.edit')) }}">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a6 6 0 0 0-3.44-5.22m0 0A5.5 5.5 0 0 0 10 4.5a5.5 5.5 0 0 0-4.56 9m10.12 0a5.9 5.9 0 0 1-.77-1.74M15 11.25a3.3 3.3 0 1 1-6.59 0 3.3 3.3 0 0 1 6.59 0ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Kelola program studi
                </a>
            </div>

            {{-- SECTION: DAFTAR PRODI --}}
            <div class="space-y-1 mt-6">
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Program Studi</p>
                @foreach ($pertiProdis as $pertiProdi)
                    @php
                        $isThisProdi = ($activeProdiId !== null && (string)$activeProdiId === (string)$pertiProdi->id);
                    @endphp
                    <div x-data="{ prodiOpen: {{ $isThisProdi ? 'true' : 'false' }} }">
                        <button type="button"
                                @click="prodiOpen = !prodiOpen"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition
                                    {{ $isThisProdi ? 'bg-white/10 text-white shadow-inner ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                            </svg>
                            <span class="flex-1 truncate text-left text-sm">{{ $pertiProdi->name }}</span>
                            <svg class="h-4 w-4 shrink-0 transition-transform" :class="prodiOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </button>

                        <div x-show="prodiOpen" x-cloak class="mt-1 space-y-0.5 pl-3">
                            {{-- Link overview prodi --}}
                            <a href="{{ route('perti.prodis.progress', $pertiProdi->id) }}" @click="sidebarOpen = false"
                               class="block rounded-lg px-3 py-2 text-[13px] font-semibold transition {{ $subNavLink(request()->routeIs('perti.prodis.progress') && $isThisProdi) }}"
                               title="Semua Kriteria">
                                Overview semua kriteria
                            </a>
                            {{-- Sub-menu per modul --}}
                            @foreach ($pertiModules as $pMod)
                                @php
                                    $modActive = $isThisProdi && request()->routeIs('perti.prodis.modul') && (string)request()->route('module') === (string)$pMod->id;
                                @endphp
                                <a href="{{ route('perti.prodis.modul', [$pertiProdi->id, $pMod->id]) }}" @click="sidebarOpen = false"
                                   class="block rounded-lg px-3 py-2 text-[13px] font-medium transition {{ $subNavLink($modActive) }}"
                                   title="{{ $pMod->name }}">
                                    <span class="block truncate">{{ $pMod->shortLabel() }}</span>
                                    <span class="mt-0.5 block truncate text-[11px] opacity-70">{{ \Illuminate\Support\Str::after($pMod->name, ': ') ?: $pMod->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- SECTION: LAPORAN --}}
            @if (auth()->user()->effective_package !== 'Starter')
                <div class="space-y-1 mt-6">
                    <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Laporan</p>
                    <a href="{{ route('perti.reports.pdf') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">
                        <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        Export PDF
                    </a>
                </div>
            @endif
        @endif

        @if ($role === UserRole::Prodi)
            <p class="px-3 pt-5 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Unit kerja</p>

            <div x-data="{ uploadOpen: {{ $uploadMenuActive ? 'true' : 'false' }} }">
                <button type="button"
                        @click="uploadOpen = !uploadOpen"
                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ $uploadMenuActive ? 'bg-white/10 text-white shadow-inner ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75v-2.25M7.5 7.5h9M7.5 10.5h5.25M7.5 4.5v9"/></svg>
                    <span class="flex-1 text-left">Unggah dokumen</span>
                    <svg class="h-4 w-4 shrink-0 transition-transform" :class="uploadOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </button>

                <div x-show="uploadOpen" x-cloak class="mt-1 space-y-0.5 pl-3">
                    @foreach ($uploadModules as $uploadModule)
                        @php
                            $moduleActive = request()->routeIs('unit.submissions.module') && request()->route('module')?->is($uploadModule);
                        @endphp
                        <a href="{{ route('unit.submissions.module', $uploadModule) }}" @click="sidebarOpen = false"
                           class="block rounded-lg px-3 py-2 text-[13px] font-medium transition {{ $subNavLink($moduleActive) }}"
                           title="{{ $uploadModule->name }}">
                            <span class="block truncate">{{ $uploadModule->shortLabel() }}</span>
                            <span class="mt-0.5 block truncate text-[11px] opacity-70">{{ \Illuminate\Support\Str::after($uploadModule->name, ': ') ?: $uploadModule->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            @if (auth()->user()->effective_package !== 'Starter')
                <p class="px-3 pt-5 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Laporan</p>
                <a href="{{ route('unit.reports.pdf') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    Export PDF
                </a>
            @endif
        @endif
    </nav>

    <div class="mt-auto border-t border-white/10 p-4">
        <div class="rounded-xl bg-white/5 p-3 ring-1 ring-white/10 flex items-center justify-between gap-2">
            <div class="flex items-center gap-3 min-w-0">
                <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" class="h-9 w-9 rounded-full object-cover shrink-0 bg-slate-800 ring-2 ring-white/10">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="mt-0.5 truncate text-xs text-violet-300">{{ $role->label() }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-white/10 hover:text-white" title="Keluar">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

