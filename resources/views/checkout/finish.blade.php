<x-public-layout 
    title="Status Pembayaran" 
    description="Status pembayaran paket berlangganan SILADATA Anda."
>
    <div class="flex min-h-[70vh] items-center justify-center px-4 py-16">
        <div class="w-full max-w-lg">
            @if(!$transaction)
                {{-- ── Transaksi Tidak Ditemukan ── --}}
                <div class="rounded-3xl bg-white p-10 text-center shadow-sm border border-slate-200/70">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-slate-100">
                        <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h1 class="mt-5 text-xl font-black text-slate-900">Transaksi Tidak Ditemukan</h1>
                    <p class="mt-2 text-sm text-slate-500">Kami tidak dapat menemukan data transaksi Anda. Mungkin link sudah tidak valid.</p>
                    <a href="{{ route('home') }}" class="mt-8 inline-flex items-center gap-2 rounded-2xl bg-violet-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-violet-700 transition">
                        Kembali ke Beranda
                    </a>
                </div>

            @elseif($transaction->status === 'success')
                {{-- ── SUKSES ── --}}
                <div class="rounded-3xl bg-white p-10 text-center shadow-sm border border-slate-200/70">
                    {{-- Ikon Sukses Animasi --}}
                    <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100">
                        <svg class="h-12 w-12 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h1 class="mt-6 text-2xl font-black text-slate-900">Pembayaran Berhasil! 🎉</h1>
                    <p class="mt-2 text-slate-500 text-sm">
                        Terima kasih! Pembayaran untuk <strong class="text-slate-800">Paket {{ $transaction->package_name }}</strong> telah kami terima.
                    </p>

                    {{-- Ringkasan Transaksi --}}
                    <div class="mt-8 rounded-2xl bg-slate-50 border border-slate-100 p-5 text-left text-sm space-y-3">
                        <div class="flex justify-between">
                            <span class="text-slate-500">ID Pesanan</span>
                            <span class="font-mono font-semibold text-slate-900">{{ $transaction->order_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Paket</span>
                            <span class="font-bold text-slate-900">{{ $transaction->package_name }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-slate-200">
                            <span class="text-slate-500 font-semibold">Total Dibayar</span>
                            <span class="font-black text-emerald-700">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if(!Auth::check() && !$transaction->user_id)
                        {{-- Langkah Selanjutnya: Cek Email --}}
                        <div class="mt-6 rounded-2xl bg-blue-50 border border-blue-200 p-6 text-left">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-blue-900">Cek Kotak Masuk Email Anda</h3>
                                    <p class="mt-1 text-sm text-blue-700 leading-relaxed">
                                        Kami telah mengirimkan <strong>link pembuatan akun</strong> ke email:
                                    </p>
                                    <p class="mt-2 rounded-lg bg-blue-100 px-3 py-2 text-sm font-mono font-semibold text-blue-900">
                                        {{ $transaction->customer_email }}
                                    </p>
                                    <p class="mt-2 text-xs text-blue-600">
                                        Klik link tersebut untuk membuat akun Perguruan Tinggi Anda. Link pendaftaran ini akan terus berlaku sampai Anda berhasil membuat akun. Periksa folder Spam jika tidak menemukannya.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('register-perti.form', $transaction->registration_token) }}"
                           class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.01]">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Buat Akun Perguruan Tinggi Sekarang
                        </a>
                    @else
                        <div class="mt-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-5 text-left">
                            <h3 class="font-bold text-emerald-900">Paket Diperbarui</h3>
                            <p class="mt-1 text-sm text-emerald-700">Paket berlangganan Anda telah berhasil diperpanjang. Silakan lanjutkan aktivitas Anda.</p>
                        </div>
                        <a href="{{ route('dashboard') }}"
                           class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-900 px-6 py-4 text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.01] hover:bg-slate-800">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z"/></svg>
                            Kembali ke Dashboard
                        </a>
                    @endif
                </div>

            @elseif($transaction->status === 'pending')
                {{-- ── PENDING ── --}}
                <div class="rounded-3xl bg-white p-10 text-center shadow-sm border border-slate-200/70">
                    <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-amber-100">
                        <svg class="h-12 w-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h1 class="mt-6 text-2xl font-black text-slate-900">Menunggu Pembayaran</h1>
                    <p class="mt-2 text-slate-500 text-sm leading-relaxed">
                        Pembayaran Anda sedang diproses. Silakan selesaikan pembayaran menggunakan metode yang Anda pilih. Setelah terkonfirmasi, link aktivasi akun akan dikirim ke email Anda.
                    </p>
                    <div class="mt-6 rounded-2xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                        <strong>ID Pesanan:</strong> <span class="font-mono">{{ $transaction->order_id }}</span>
                    </div>
                    <a href="{{ route('checkout.payment', $transaction->order_id) }}"
                       class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-amber-500 px-6 py-4 text-sm font-bold text-white shadow-md transition-all hover:bg-amber-600 hover:scale-[1.01]">
                        Lanjutkan Pembayaran →
                    </a>
                </div>

            @else
                {{-- ── GAGAL ── --}}
                <div class="rounded-3xl bg-white p-10 text-center shadow-sm border border-slate-200/70">
                    <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h1 class="mt-6 text-2xl font-black text-slate-900">Pembayaran Gagal</h1>
                    <p class="mt-2 text-slate-500 text-sm">Pembayaran tidak dapat diselesaikan atau telah kedaluwarsa. Silakan coba kembali.</p>
                    <a href="{{ route('harga') }}"
                       class="mt-8 inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-slate-800">
                        Kembali ke Halaman Harga
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-public-layout>
