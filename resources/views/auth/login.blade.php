<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Masuk</h1>
        <p class="mt-2 text-sm text-slate-600">Sistem Layanan Dokumen Akreditasi</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <a href="{{ route('google.login') }}" class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-violet-500/30 transition-all">
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Lanjutkan dengan Google
            </a>
        </div>

        <div class="relative flex items-center py-2">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="mx-4 text-xs font-medium text-slate-400">ATAU LOGIN MANUAL</span>
            <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <div>
                <x-input-label for="email" value="Email" class="text-sm font-medium text-slate-700" />
                <div class="relative mt-1.5">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-envelope text-sm"></i>
                    </div>
                    <x-text-input id="email" 
                        class="block w-full rounded-xl border-slate-200 bg-white/80 pl-10 pr-4 py-3 text-sm placeholder:text-slate-400 focus:border-violet-400 focus:ring-2 focus:ring-violet-400/30 transition-shadow duration-200" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required 
                        autofocus 
                        autocomplete="username" 
                        placeholder="nama@institusi.ac.id" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" value="Kata sandi" class="text-sm font-medium text-slate-700" />
                </div>
                <div class="relative mt-1.5">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-lock text-sm"></i>
                    </div>
                    <x-text-input id="password" 
                        class="block w-full rounded-xl border-slate-200 bg-white/80 pl-10 pr-4 py-3 text-sm placeholder:text-slate-400 focus:border-violet-400 focus:ring-2 focus:ring-violet-400/30 transition-shadow duration-200" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password" 
                        placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-violet-600 shadow-sm focus:ring-violet-500/30" name="remember">
                <span class="text-sm text-slate-600">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-violet-600 hover:text-violet-500" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:justify-between sm:items-center">
            <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors order-2 sm:order-1 text-center sm:text-left">
                &larr; Kembali ke Beranda
            </a>
            <div class="flex flex-col sm:flex-row gap-3 order-1 sm:order-2">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="ui-btn-secondary flex-1 justify-center sm:flex-initial">Daftar</a>
                @endif
                <x-primary-button class="flex-1 justify-center sm:flex-initial">
                    Masuk
                </x-primary-button>
            </div>
        </div>
        @unless (Route::has('register'))
            <p class="pt-4 text-center text-xs leading-relaxed text-slate-500">
                Belum memiliki akun program studi? Silakan <a href="{{ route('harga') }}" class="font-semibold text-violet-600 hover:text-violet-500 hover:underline">pilih paket langganan.</a>
            </p>
        @endunless
    </form>
</x-guest-layout>
