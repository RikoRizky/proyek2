<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Admin</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Panel admin</h1>
            <p class="mt-1 text-sm text-slate-600">Akun dan dokumen seluruh sistem</p>
        </div>
    </x-slot>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Perguruan Tinggi -->
        <div class="ui-card relative overflow-hidden p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Perguruan Tinggi</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">{{ $pertiCount }}</p>
            <p class="mt-4 text-xs font-medium text-slate-500">Total Perguruan Tinggi terdaftar</p>
        </div>

        <!-- Program Studi -->
        <div class="ui-card relative overflow-hidden p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Program Studi</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">{{ $unitCount }}</p>
            <p class="mt-4 text-xs font-medium text-slate-500">Total prodi di seluruh perti</p>
        </div>

        <!-- Persyaratan Aktif -->
        <div class="ui-card relative overflow-hidden p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Persyaratan Aktif</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-3.123-.199m-3.75 0c-.81 0-1.579.176-2.268.494m-6.736.216A48.43 48.43 0 0 0 3.75 6.108V17.25c0 1.243 1.007 2.25 2.25 2.25h12" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">{{ $requirementsCount }}</p>
            <p class="mt-4 text-xs font-medium text-slate-500">Total indikator akreditasi</p>
        </div>

        <!-- Total Berkas Terkumpul -->
        <div class="ui-card relative overflow-hidden p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Berkas Terkumpul</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">{{ $submissionsCount }}</p>
            <p class="mt-4 text-xs font-medium text-slate-500">Dokumen & link terunggah</p>
        </div>
    </div>

    <div class="mb-8 grid gap-6 lg:grid-cols-2">
        <div class="ui-card overflow-hidden">
            <div class="ui-section-header">
                <h2 class="text-lg font-bold text-slate-900">Pengguna terbaru</h2>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-violet-600 hover:text-violet-500">Kelola</a>
            </div>
            <ul class="divide-y divide-slate-100 text-sm">
                @foreach ($recentUsers as $u)
                    <li class="flex justify-between gap-2 px-4 py-3">
                        <span class="truncate font-medium text-slate-900">{{ $u->name }}</span>
                        <span class="shrink-0 text-xs text-slate-500">{{ $u->role->label() }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="ui-card overflow-hidden">
            <div class="ui-section-header">
                <h2 class="text-lg font-bold text-slate-900">Unggahan terbaru</h2>
                <a href="{{ route('admin.submissions.index') }}" class="text-xs font-semibold text-violet-600 hover:text-violet-500">Semua</a>
            </div>
            <ul class="divide-y divide-slate-100 text-sm">
                @foreach ($recentSubmissions as $s)
                    <li class="px-4 py-3">
                        <div class="truncate font-medium text-slate-900">{{ $s->user->name }}</div>
                        <div class="truncate text-xs text-slate-500">{{ $s->requirement->title }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="ui-card overflow-hidden">
        <div class="ui-section-header">
            <h2 class="text-lg font-bold text-slate-900">Modul akreditasi</h2>
            <a href="{{ route('admin.modules.index') }}" class="text-sm font-semibold text-violet-600 hover:text-violet-500">Kelola →</a>
        </div>
        <ul class="divide-y divide-slate-100">
            @foreach ($modules as $m)
                <li class="flex items-center justify-between gap-4 px-6 py-3.5 text-sm transition hover:bg-violet-50/30">
                    <span class="font-semibold text-slate-900">{{ $m->name }}</span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200/80">{{ $m->requirements_count }} persyaratan</span>
                </li>
            @endforeach
        </ul>
    </div>
</x-app-layout>
