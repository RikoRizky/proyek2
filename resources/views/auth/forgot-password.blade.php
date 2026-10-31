<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Lupa Kata Sandi?</h1>
        <p class="mt-2 text-sm text-slate-600">Tidak masalah. Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset kata sandi.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email Terdaftar" class="text-sm font-medium text-slate-700" />
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
                    placeholder="nama@institusi.ac.id" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:justify-between sm:items-center">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors order-2 sm:order-1 text-center sm:text-left">
                &larr; Kembali ke Login
            </a>
            
            <x-primary-button class="order-1 flex-1 justify-center sm:order-2 sm:flex-initial">
                Kirim Tautan Reset
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>