<x-app-layout>
    @php
        $uploadedCount = $module->requirements->filter(fn ($req) => $req->submissions->first() && $req->submissions->first()->status !== \App\Enums\SubmissionStatus::Pending)->count();
        $totalCount = $module->requirements->count();
        $progressPercent = $totalCount > 0 ? round(($uploadedCount / $totalCount) * 100) : 0;
    @endphp

    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Unit kerja</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900" data-module-title>{{ $module->name }}</h1>
                @if ($module->description)
                    <p class="mt-1 max-w-3xl text-sm text-slate-600">{{ $module->description }}</p>
                @endif
            </div>
            <div class="rounded-2xl border border-violet-200/80 bg-violet-50/80 px-4 py-3 text-right">
                <p class="text-xs font-bold uppercase tracking-wider text-violet-700">Progress modul</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-violet-900">{{ $uploadedCount }}/{{ $totalCount }}</p>
                <p class="text-xs text-violet-700">{{ $progressPercent }}% terunggah</p>
            </div>
        </div>
    </x-slot>

    @if (session('upload_partial_failure'))
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50/90 px-4 py-3 text-sm text-amber-950 ring-1 ring-amber-500/15">
            <p class="font-semibold">Beberapa berkas gagal diunggah</p>
            <p class="mt-1">Berkas yang valid sudah tersimpan. Periksa bagian yang ditandai merah di bawah, lalu unggah ulang hanya berkas yang bermasalah.</p>
        </div>
    @endif

    @if ($errors->has('files'))
        <div class="mb-6 rounded-2xl border border-red-100 bg-red-50/90 px-4 py-3 text-sm font-medium text-red-900 ring-1 ring-red-500/10">
            {{ $errors->first('files') }}
        </div>
    @endif

    <div class="ui-card overflow-hidden">
        @include('unit.submissions._module-form', ['module' => $module])
    </div>

    @include('unit.submissions._module-modals', ['module' => $module])

    <script>
        const uploadStatus = sessionStorage.getItem('upload_status');
        if (uploadStatus) {
            sessionStorage.removeItem('upload_status');
            const alert = document.createElement('div');
            alert.className = 'ui-alert-success mb-6';
            alert.setAttribute('role', 'status');
            alert.innerHTML = '<span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white text-xs font-bold">✓</span><span>' + uploadStatus + '</span>';
            document.querySelector('main')?.prepend(alert);
        }
    </script>
</x-app-layout>
