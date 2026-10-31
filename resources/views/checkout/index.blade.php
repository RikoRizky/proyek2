<x-public-layout 
    title="Checkout Paket" 
    description="Isi data untuk melanjutkan pembayaran paket berlangganan SILADATA."
>
    <div class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-5">

            {{-- ── Kiri: Ringkasan Paket ─────────────────────── --}}
            <div class="lg:col-span-2">
                {{-- Progress Steps --}}
                <ol class="flex flex-col gap-3 mb-8">
                    @foreach(['Isi Data', 'Pembayaran', 'Akun Aktif'] as $i => $step)
                        <li class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold
                                {{ $i === 0 ? 'bg-violet-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                                {{ $i + 1 }}
                            </span>
                            <span class="text-sm font-medium {{ $i === 0 ? 'text-slate-900' : 'text-slate-400' }}">{{ $step }}</span>
                        </li>
                    @endforeach
                </ol>

                {{-- Ringkasan --}}
                <div class="rounded-3xl bg-gradient-to-br from-violet-600 to-indigo-700 p-7 text-white shadow-xl shadow-violet-500/25">
                    <p class="text-xs font-bold uppercase tracking-widest text-violet-200 mb-4">Paket yang Dipilih</p>
                    <h2 class="text-3xl font-black">{{ $package }}</h2>
                    <p class="mt-1 text-violet-200 text-sm">Berlangganan per tahun</p>

                    <div class="mt-6 pt-6 border-t border-violet-500/40">
                        <p class="text-xs font-semibold uppercase tracking-wider text-violet-200 mb-3">Yang akan Anda dapatkan:</p>
                        <ul class="space-y-2 text-sm text-violet-100">
                            @php
                            $perks = [
                                'Starter' => ['Hingga 3 Akun Program Studi', 'Upload via Google Drive Link', 'Dashboard Monitoring Progress'],
                                'Pro'      => ['Hingga 10 Akun Program Studi', 'Upload File Langsung (10GB)', 'Cetak Laporan PDF', 'Dukungan Prioritas WhatsApp'],
                                'Enterprise' => ['Akun Prodi Tidak Terbatas', 'Upload File Langsung (50GB)', 'Laporan & Analitik Lanjutan', 'Manajer Akun Khusus'],
                            ];
                            $list = $perks[$package] ?? [];
                            @endphp
                            @foreach($list as $perk)
                                <li class="flex items-start gap-2">
                                    <svg class="h-4 w-4 shrink-0 mt-0.5 text-violet-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    {{ $perk }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-6 pt-6 border-t border-violet-500/40 flex items-center gap-2 text-xs text-violet-200">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Pembayaran aman & terenkripsi via Midtrans
                    </div>
                </div>
            </div>

            {{-- ── Kanan: Form ───────────────────────────────── --}}
            <div class="lg:col-span-3">
                <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200/70">
                    <h1 class="text-2xl font-black text-slate-900">Informasi Pemesanan</h1>
                    <p class="mt-1 text-sm text-slate-500">Isi data di bawah ini untuk melanjutkan ke tahap pembayaran.</p>

                    @if(session('error'))
                        <div class="mt-6 flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('checkout.process') }}" method="POST" class="mt-8 space-y-6">
                        @csrf
                        <input type="hidden" name="package" value="{{ $package }}">

                        {{-- Nama --}}
                        <div>
                            <label for="customer_name" class="block text-sm font-semibold text-slate-700 mb-2">
                                Nama Institusi / Perguruan Tinggi <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="customer_name"
                                name="customer_name"
                                type="text"
                                placeholder="cth. Universitas Logistik & Bisnis Internasional"
                                required
                                value="{{ old('customer_name') }}"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition"
                            >
                            @error('customer_name')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="customer_email" class="block text-sm font-semibold text-slate-700 mb-2">
                                Email Utama <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="customer_email"
                                name="customer_email"
                                type="email"
                                autocomplete="email"
                                placeholder="cth. admin@universitasanda.ac.id"
                                required
                                value="{{ old('customer_email') }}"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition"
                            >
                            <p class="mt-1.5 text-xs text-slate-400">
                                Link pembuatan akun akan dikirimkan ke email ini setelah pembayaran dikonfirmasi.
                            </p>
                            @error('customer_email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Info Email --}}
                        <div class="flex items-start gap-3 rounded-xl bg-amber-50 border border-amber-200/70 p-4">
                            <svg class="h-5 w-5 shrink-0 text-amber-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                Setelah pembayaran berhasil, <strong>link aktivasi akun</strong> akan dikirim ke alamat email di atas. Pastikan email yang dimasukkan aktif dan dapat diakses.
                            </p>
                        </div>

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 px-6 py-4 text-sm font-bold text-white shadow-lg shadow-violet-500/25 transition-all hover:scale-[1.01] hover:shadow-violet-500/35 focus:outline-none focus:ring-4 focus:ring-violet-500/20"
                        >
                            Lanjutkan ke Pembayaran
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>

                        <p class="text-center text-xs text-slate-400">
                            Dengan melanjutkan, Anda menyetujui <a href="#" class="text-violet-600 hover:underline">Syarat & Ketentuan</a> layanan SILADATA.
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-public-layout>
