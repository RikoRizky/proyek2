@php
    $m = $module ?? null;
@endphp

<div class="space-y-4">
    <div>
        <label class="block text-sm font-semibold text-slate-700">
            Nama Kriteria / Modul <span class="text-rose-500">*</span>
        </label>
        <p class="text-xs text-slate-500 mt-0.5">Nama standar atau kriteria akreditasi LAM INFOKOM.</p>
        <input type="text" name="name" value="{{ old('name', $m?->name) }}" required
            placeholder="Contoh: Kriteria 7: Suplemen Program Studi (S1 Terapan)..."
            class="ui-input mt-1.5">
        @error('name')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">
            Deskripsi Kriteria <span class="font-normal text-slate-400">(opsional)</span>
        </label>
        <p class="text-xs text-slate-500 mt-0.5">Penjelasan umum mengenai lingkup kriteria ini.</p>
        <textarea name="description" rows="3"
            placeholder="Tuliskan gambaran umum atau panduan kriteria..."
            class="ui-input mt-1.5">{{ old('description', $m?->description) }}</textarea>
        @error('description')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">
            Urutan Tampilan (Sort Order) <span class="font-normal text-slate-400">(opsional)</span>
        </label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $m?->sort_order ?? 0) }}"
            placeholder="Contoh: 1, 2, 3..."
            class="ui-input mt-1.5">
        @error('sort_order')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
