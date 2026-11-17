<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#A35139]">Langganan</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Perpanjang atau Upgrade Paket</h1>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-slate-900">Pilih Paket Layanan</h2>
            <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">
                Silakan pilih paket untuk memperpanjang masa aktif akun Anda, atau lakukan upgrade untuk mendapatkan lebih banyak fitur dan kapasitas penyimpanan.
            </p>
        </div>

        @if(session('error'))
            <div class="mb-8 p-4 bg-red-50 text-red-700 border border-red-200 rounded-xl text-center max-w-2xl mx-auto">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-8 md:grid-cols-3 items-stretch max-w-6xl mx-auto">
            @foreach($packages as $package)
                <div class="
                    flex flex-col justify-between rounded-3xl p-8 transition-all duration-300
                    hover:-translate-y-1 hover:shadow-2xl h-full
                    {{ isset($package['featured']) ? 'bg-gradient-to-b from-[#1B2632] via-[#243343] to-[#2C3B4D] text-white shadow-2xl shadow-[#1B2632]/30 scale-105 z-10 border border-[#C9C1B1]/30' : 'bg-white border border-[#C9C1B1]/60 shadow-sm' }}
                ">
                    <div>
                        @if(isset($package['featured']))
                            <div class="mb-4 inline-flex rounded-full bg-[#FFB162] px-3 py-1 text-xs font-black text-[#1B2632] uppercase tracking-wider">
                                ⭐ PILIHAN TERBAIK
                            </div>
                        @endif

                        <h3 class="text-2xl font-bold {{ isset($package['featured']) ? 'text-white' : 'text-slate-900' }}">
                            {{ $package['name'] }}
                        </h3>

                        <p class="mt-2 text-sm {{ isset($package['featured']) ? 'text-[#EEE9DF]' : 'text-slate-500' }}">
                            {{ $package['description'] }}
                        </p>

                        <div class="mt-6">
                            <span class="text-3xl font-extrabold {{ isset($package['featured']) ? 'text-white' : 'text-slate-900' }}">
                                {{ $package['price_label'] }}
                            </span>
                            <span class="text-sm {{ isset($package['featured']) ? 'text-[#C9C1B1]' : 'text-slate-500' }}">/tahun</span>
                        </div>

                        <div class="mt-6 border-t {{ isset($package['featured']) ? 'border-[#C9C1B1]/30' : 'border-slate-100' }} pt-6">
                            <p class="text-xs font-bold uppercase tracking-wider {{ isset($package['featured']) ? 'text-[#FFB162]' : 'text-slate-400' }}">Fitur Utama:</p>
                            <ul class="mt-4 space-y-3">
                                @foreach($package['features'] as $feature)
                                    <li class="flex items-start gap-2.5 text-sm">
                                        <span class="font-bold {{ isset($package['featured']) ? 'text-[#FFB162]' : 'text-emerald-500' }}">✓</span>
                                        <span class="{{ isset($package['featured']) ? 'text-[#EEE9DF]' : 'text-slate-600' }}">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="mt-8">
                        <form action="{{ route('upgrade.process') }}" method="POST">
                            @csrf
                            <input type="hidden" name="package" value="{{ $package['name'] }}">
                            
                            @php
                                $hierarchy = ['Starter' => 1, 'Pro' => 2, 'Enterprise' => 3];
                                $userPackage = auth()->user()->active_package;
                                $userLevel = $userPackage && isset($hierarchy[$userPackage]) ? $hierarchy[$userPackage] : 0;
                                $currentLevel = isset($hierarchy[$package['name']]) ? $hierarchy[$package['name']] : 0;
                                $isDowngrade = $currentLevel < $userLevel;
                                $isCurrent = $userPackage === $package['name'];
                            @endphp

                            @if(!$isDowngrade)
                            <div class="mb-4">
                                <label for="years_{{ $package['name'] }}" class="block text-sm font-medium {{ isset($package['featured']) ? 'text-[#EEE9DF]' : 'text-slate-700' }} mb-1">Durasi Langganan</label>
                                <select name="years" id="years_{{ $package['name'] }}" class="mt-1 block w-full rounded-lg border-[#C9C1B1]/60 py-2.5 pl-3 pr-10 text-base text-slate-900 focus:border-[#A35139] focus:outline-none focus:ring-[#A35139] sm:text-sm shadow-sm bg-white">
                                    <option value="1">1 Tahun</option>
                                    <option value="2">2 Tahun</option>
                                    <option value="3">3 Tahun</option>
                                    <option value="4">4 Tahun</option>
                                    <option value="5">5 Tahun</option>
                                </select>
                            </div>
                            @endif

                            <button type="{{ $isDowngrade ? 'button' : 'submit' }}" class="block w-full text-center rounded-xl py-3 font-semibold transition-colors duration-200
                                @if($isDowngrade)
                                    bg-slate-200 text-slate-400 cursor-not-allowed shadow-none
                                @elseif($isCurrent && isset($package['featured']))
                                    bg-[#A35139] text-white hover:bg-[#8A412C] shadow-lg shadow-[#A35139]/20
                                @elseif($isCurrent)
                                    bg-[#1B2632] text-white hover:bg-[#2C3B4D] shadow-lg shadow-black/10
                                @elseif(isset($package['featured']))
                                    bg-gradient-to-r from-[#FFB162] to-[#FF9E3B] text-[#1B2632] hover:opacity-90 shadow-lg shadow-[#FFB162]/30 font-black
                                @elseif($package['name'] === 'Enterprise')
                                    bg-[#1B2632] text-white hover:bg-[#2C3B4D] shadow-lg shadow-[#1B2632]/20
                                @else
                                    bg-[#A35139] text-white hover:bg-[#8A412C] shadow-lg shadow-[#A35139]/20
                                @endif
                                "
                                {{ $isDowngrade ? 'disabled' : '' }}>
                                {{ $isDowngrade ? 'Tidak Tersedia' : ($isCurrent ? 'Perpanjang Paket Ini' : 'Pilih Paket') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
