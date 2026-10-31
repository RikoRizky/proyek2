<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Reset Kata Sandi</h1>
        <p class="mt-2 text-sm text-slate-600">Silakan masukkan kata sandi baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" class="text-sm font-medium text-slate-700" />
            <div class="relative mt-1.5">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="fas fa-envelope text-sm"></i>
                </div>
                <x-text-input id="email" 
                    class="block w-full rounded-xl border-slate-200 bg-slate-50 pl-10 pr-4 py-3 text-sm text-slate-500 focus:border-violet-400 focus:ring-2 focus:ring-violet-400/30 transition-shadow duration-200" 
                    type="email" 
                    name="email" 
                    :value="old('email', $request->email)" 
                    required 
                    readonly />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi Baru" class="text-sm font-medium text-slate-700" />
            <div class="relative mt-1.5">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="fas fa-lock text-sm"></i>
                </div>
                <x-text-input id="password" 
                    class="block w-full rounded-xl border-slate-200 bg-white/80 pl-10 pr-4 py-3 text-sm placeholder:text-slate-400 focus:border-violet-400 focus:ring-2 focus:ring-violet-400/30 transition-shadow duration-200" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="new-password" 
                    placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" class="text-sm font-medium text-slate-700" />
            <div class="relative mt-1.5">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="fas fa-lock text-sm"></i>
                </div>
                <x-text-input id="password_confirmation" 
                    class="block w-full rounded-xl border-slate-200 bg-white/80 pl-10 pr-4 py-3 text-sm placeholder:text-slate-400 focus:border-violet-400 focus:ring-2 focus:ring-violet-400/30 transition-shadow duration-200" 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password" 
                    placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center">
                Simpan Kata Sandi Baru
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
