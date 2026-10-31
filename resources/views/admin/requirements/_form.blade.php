@php $r = $requirement ?? null; @endphp

<div>
    <label class="block text-sm font-semibold text-slate-700">Judul Persyaratan</label>
    <input type="text" name="title" value="{{ old('title', $r?->title) }}" required class="ui-input mt-2">
    @error('title')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-slate-700">Tipe Input Pengisian</label>
        <select name="input_type" class="ui-input mt-2" required>
            <option value="document" {{ old('input_type', $r?->input_type ?? 'document') === 'document' ? 'selected' : '' }}>
                📄 Dokumen LED (Unggah Berkas PDF/Google Drive)
            </option>
            <option value="table" {{ old('input_type', $r?->input_type) === 'table' ? 'selected' : '' }}>
                📊 Tabel Kinerja LKPS (Input Data / Import Excel)
            </option>
            <option value="matrix" {{ old('input_type', $r?->input_type) === 'matrix' ? 'selected' : '' }}>
                🧩 Matriks Cross-Tabulation (Matriks CPL / Profil Lulusan)
            </option>
        </select>
        @error('input_type')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">Kode Tabel LKPS (Opsional)</label>
        <input type="text" name="table_code" value="{{ old('table_code', $r?->table_code) }}" placeholder="Contoh: 2.B.1, 2.B.2, 1.A.4..." class="ui-input mt-2">
        @error('table_code')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-slate-700">Deskripsi / Petunjuk Pengisian</label>
    <textarea name="description" rows="3" class="ui-input mt-2">{{ old('description', $r?->description) }}</textarea>
</div>

<div>
    <label class="block text-sm font-semibold text-slate-700">Urutan Tampil (Sort Order)</label>
    <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $r?->sort_order ?? 0) }}" class="ui-input mt-2">
</div>
