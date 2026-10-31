<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Perguruan Tinggi</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Edit Akun Perguruan Tinggi</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $perti->email }}</p>
        </div>
    </x-slot>

    <div class="ui-card max-w-xl mx-auto p-6 sm:p-8">
        <form method="post" action="{{ route('admin.pertis.update', $perti) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-slate-700">Nama Institusi</label>
                <input type="text" name="name" value="{{ old('name', $perti->name) }}" required class="ui-input mt-2">
                @error('name')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $perti->email) }}" required class="ui-input mt-2">
                @error('email')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Kode PT <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input type="text" name="kode_pt" value="{{ old('kode_pt', $perti->pertiProfile?->kode_pt) }}" class="ui-input mt-2" placeholder="Contoh: 001009">
                    @error('kode_pt')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Alamat <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input type="text" name="alamat" value="{{ old('alamat', $perti->pertiProfile?->alamat) }}" class="ui-input mt-2">
                    @error('alamat')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700">Kata sandi baru <span class="font-normal text-slate-400">(opsional)</span></label>
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
