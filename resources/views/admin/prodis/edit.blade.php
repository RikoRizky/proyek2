<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Program Studi</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Edit Akun Program Studi</h1>
            <p class="mt-1 text-sm text-slate-600">Perbarui informasi akun {{ $prodi->email }}</p>
        </div>
    </x-slot>

    <div class="ui-card max-w-xl mx-auto p-6 sm:p-8">
        <form method="post" action="{{ route('admin.prodis.update', $prodi) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-slate-700">Nama Program Studi</label>
                <input type="text" name="name" value="{{ old('name', $prodi->name) }}" required class="ui-input mt-2">
                @error('name')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700">Email login</label>
                <input type="email" name="email" value="{{ old('email', $prodi->email) }}" required class="ui-input mt-2">
                @error('email')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700">Perguruan Tinggi Induk</label>
                <select name="perti_id" required class="ui-input mt-2">
                    @foreach ($pertis as $p)
                        <option value="{{ $p->pertiProfile->id }}" @selected(old('perti_id', $prodi->prodiProfile?->perti_id) == $p->pertiProfile->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('perti_id')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700">Kode Prodi <span class="font-normal text-slate-400">(opsional)</span></label>
                <input type="text" name="kode_prodi" value="{{ old('kode_prodi', $prodi->prodiProfile?->kode_prodi) }}" class="ui-input mt-2" placeholder="Contoh: IF001">
                @error('kode_prodi')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700">Kata sandi baru (opsional)</label>
                <input type="password" name="password" class="ui-input mt-2" autocomplete="new-password">
                @error('password')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700">Ulangi kata sandi</label>
                <input type="password" name="password_confirmation" class="ui-input mt-2" autocomplete="new-password">
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="ui-btn-primary">Simpan perubahan</button>
                <a href="{{ route('admin.users.index') }}" class="ui-btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
