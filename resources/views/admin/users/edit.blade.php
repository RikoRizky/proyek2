<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Administrator</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Edit Akun Administrator</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $user->email }}</p>
        </div>
    </x-slot>

    <div class="ui-card max-w-xl mx-auto p-6 sm:p-8">
        <form method="post" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-slate-700">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="ui-input mt-2">
                @error('name')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="ui-input mt-2"
                    @if($user->id === auth()->id()) readonly @endif>
                @error('email')<p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
                @if($user->id === auth()->id())
                    <input type="hidden" name="email" value="{{ $user->email }}">
                @endif
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
