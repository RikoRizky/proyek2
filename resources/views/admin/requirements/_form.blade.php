@php 
    $r = $requirement ?? null; 
    $initialType = old('input_type', $r?->input_type ?? 'document');
@endphp

<div x-data="{ inputType: '{{ $initialType }}' }" class="space-y-4">
    <div>
        <label class="block text-sm font-semibold text-slate-700">
            Tipe Pengisian Butir <span class="text-rose-500">*</span>
        </label>
        <p class="text-xs text-slate-500 mt-0.5 mb-2">Tentukan apakah butir ini berupa berkas dokumen LED atau tabel isian spreadsheet LKPS.</p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="flex items-start gap-3 rounded-xl border p-3.5 cursor-pointer transition select-none"
                :class="inputType === 'document' ? 'border-violet-500 bg-violet-50/60 text-violet-950 ring-2 ring-violet-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'">
                <input type="radio" name="input_type" value="document" x-model="inputType" class="mt-0.5 text-violet-600 focus:ring-violet-500">
                <div>
                    <span class="block text-xs font-bold text-slate-900">📄 Dokumen Evaluasi Diri (LED)</span>
                    <span class="block text-[11px] text-slate-500 mt-0.5">Unggah berkas dokumen (PDF/XLSX) atau sertakan link Google Drive untuk memenuhi tahapan PPEPP.</span>
                </div>
            </label>

            <label class="flex items-start gap-3 rounded-xl border p-3.5 cursor-pointer transition select-none"
                :class="inputType === 'table' || inputType === 'matrix' ? 'border-emerald-500 bg-emerald-50/60 text-emerald-950 ring-2 ring-emerald-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'">
                <input type="radio" name="input_type" value="table" x-model="inputType" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                <div>
                    <span class="block text-xs font-bold text-slate-900">📊 Tabel Kinerja LKPS (Spreadsheet)</span>
                    <span class="block text-[11px] text-slate-500 mt-0.5">Tabel kinerja terstruktur yang dapat diinput langsung di web, diubah judul kolomnya, atau diimport dari Excel.</span>
                </div>
            </label>
        </div>
        @error('input_type')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Field Kode Tabel LKPS --}}
    <div x-show="inputType === 'table' || inputType === 'matrix'" class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 transition">
        <label class="block text-xs font-bold uppercase tracking-wider text-emerald-950">
            Kode Tabel LKPS <span class="text-rose-500">*</span>
        </label>
        <p class="text-xs text-emerald-800 mt-0.5">Kode tabel resmi sesuai instrumen LAM INFOKOM (contoh: <code>1.A.1</code>, <code>2.B.1</code>, <code>3.A.1</code>, <code>7.1</code>, dsb.).</p>
        <input type="text" name="table_code" value="{{ old('table_code', $r?->table_code) }}"
            placeholder="Contoh: 1.A.1, 2.B.1, 7.1..."
            class="ui-input mt-2 border-emerald-300 bg-white text-xs font-bold text-emerald-950 placeholder-slate-400 focus:border-emerald-500 focus:ring-emerald-500">
        @error('table_code')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">
            Judul Butir / Nama Tabel <span class="text-rose-500">*</span>
        </label>
        <input type="text" name="title" value="{{ old('title', $r?->title) }}" required class="ui-input mt-1.5"
            placeholder="Contoh: Tabel 1.A.1: Pimpinan dan Tupoksi UPPS dan PS...">
        @error('title')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">
            Deskripsi / Petunjuk Pengisian <span class="font-normal text-slate-400">(opsional)</span>
        </label>
        <textarea name="description" rows="3" class="ui-input mt-1.5"
            placeholder="Tuliskan petunjuk atau kriteria yang perlu dilengkapi...">{{ old('description', $r?->description) }}</textarea>
        @error('description')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">
            Nomor Urut Tampilan <span class="font-normal text-slate-400">(opsional)</span>
        </label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $r?->sort_order ?? 0) }}" class="ui-input mt-1.5">
        @error('sort_order')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
