@php $r = $requirement ?? null; @endphp

<div>
    <label class="block text-sm font-semibold text-slate-700">Judul</label>
    <input type="text" name="title" value="{{ old('title', $r?->title) }}" required class="ui-input mt-2">
    @error('title')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-semibold text-slate-700">Deskripsi</label>
    <textarea name="description" rows="3" class="ui-input mt-2">{{ old('description', $r?->description) }}</textarea>
</div>

<div>
    <label class="block text-sm font-semibold text-slate-700">Urutan</label>
    <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $r?->sort_order ?? 0) }}" class="ui-input mt-2">
</div>
