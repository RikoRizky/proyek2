<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Akun</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ __('Profile') }}</h1>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        
        @if(!auth()->user()->isAdmin())
        {{-- Subscription Info Card --}}
        <div class="ui-card p-6 sm:p-8 border-l-4 {{ (auth()->user()->effective_package_valid_until && auth()->user()->effective_package_valid_until->diffInDays(now()) <= 30) ? 'border-amber-500 bg-amber-50/30' : 'border-violet-600' }}">
            <div class="max-w-xl">
                <header>
                    <h2 class="text-lg font-medium text-slate-900">
                        Informasi Paket Langganan
                    </h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Detail paket berlangganan SILADATA Anda saat ini.
                    </p>
                </header>

                <div class="mt-6 space-y-4">
                    <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 shadow-sm">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Paket Aktif</p>
                            <p class="text-xl font-bold text-violet-700">{{ auth()->user()->effective_package ?? 'Belum Ada Paket' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-slate-500 mb-1">Berlaku Hingga</p>
                            <p class="text-lg font-semibold text-slate-800">
                                {{ auth()->user()->effective_package_valid_until ? auth()->user()->effective_package_valid_until->translatedFormat('d F Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    @php
                        $validUntil = auth()->user()->effective_package_valid_until;
                        $isExpiringSoon = $validUntil && $validUntil->isFuture() && now()->addDays(30)->gte($validUntil);
                        $isExpired = $validUntil && $validUntil->isPast();
                    @endphp

                    @if($isExpiringSoon || $isExpired)
                        <div class="flex gap-3 p-4 {{ $isExpired ? 'bg-red-100/50 text-red-800 border-red-200' : 'bg-amber-100/50 text-amber-800 border-amber-200' }} border rounded-xl items-start">
                            <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <h4 class="font-bold">{{ $isExpired ? 'Peringatan: Paket Telah Berakhir' : 'Peringatan: Paket Akan Berakhir' }}</h4>
                                <p class="text-sm mt-1">
                                    @if($isExpired)
                                        Masa aktif paket Anda telah habis. Segera perpanjang untuk terus menggunakan semua fitur.
                                    @else
                                        Paket langganan Anda akan berakhir dalam <b>{{ round(now()->floatDiffInDays($validUntil)) }} hari</b>. Segera perpanjang agar Anda tetap dapat mengakses semua fitur.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    @if(!auth()->user()->isProdi())
                    <div class="pt-4 flex items-center gap-4">
                        <a href="{{ route('upgrade.packages') }}" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-500 focus:bg-violet-500 active:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ auth()->user()->effective_package ? 'Perpanjang / Upgrade Paket' : 'Pilih Paket' }}
                        </a>
                    </div>
                    @else
                    <div class="pt-4">
                        <p class="text-xs text-slate-500 text-center">Pengaturan dan perpanjangan paket dikelola oleh administrator Perguruan Tinggi Anda.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="ui-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="ui-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="ui-card p-6 sm:p-8 border-rose-100 bg-rose-50/10">
            <div class="max-w-xl">
                <header>
                    <h2 class="text-lg font-medium text-slate-900">
                        Keluar dari Aplikasi
                    </h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Akhiri sesi Anda saat ini dan kembali ke halaman login.
                    </p>
                </header>
                <div class="mt-6">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl border border-rose-200 bg-white px-4 py-2.5 text-sm font-semibold text-rose-600 shadow-sm transition hover:border-rose-300 hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-300 focus:ring-offset-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Keluar (Logout)
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="ui-card border-slate-100 p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.account-deletion-policy')
            </div>
        </div>
    </div>
</x-app-layout>
