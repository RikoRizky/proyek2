<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Laporan</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Ekspor Laporan PDF</h1>
            <p class="mt-1 text-sm text-slate-600">Pilih Perguruan Tinggi (Perti) untuk mengekspor laporan ringkasan akreditasi.</p>
        </div>
    </x-slot>

    <div class="ui-card max-w-xl mx-auto p-6 sm:p-8">
        <form method="get" action="{{ route('admin.reports.pdf') }}" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-slate-700">Perguruan Tinggi</label>
                <select name="perti_id" id="pertiSelect" class="ui-input mt-2" required>
                    <option value="" disabled selected>-- Pilih Perguruan Tinggi --</option>
                    @foreach ($pertis as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('perti_id')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="ui-btn-primary">Unduh PDF</button>
                <a href="{{ route('dashboard') }}" class="ui-btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
