<x-public-layout 
    title="Selesaikan Pembayaran" 
    description="Selesaikan pembayaran paket berlangganan SILADATA."
>
    {{-- Midtrans Snap Script --}}
    @if(env('MIDTRANS_IS_PRODUCTION', false))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    @endif

    <div class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-5">

            {{-- ── Kiri: Ringkasan ───────────────────────────── --}}
            <div class="lg:col-span-2">
                {{-- Progress Steps --}}
                <ol class="flex flex-col gap-3 mb-8">
                    @foreach(['Isi Data' => true, 'Pembayaran' => true, 'Akun Aktif' => false] as $step => $done)
                        <li class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold
                                {{ $done ? 'bg-violet-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                                @if($done && $step !== 'Pembayaran')
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    {{ array_search($step, array_keys(iterator_to_array(new ArrayIterator(['Isi Data' => true, 'Pembayaran' => true, 'Akun Aktif' => false])))) + 1 }}
                                @endif
                            </span>
                            <span class="text-sm font-medium {{ $done ? 'text-slate-900' : 'text-slate-400' }}">{{ $step }}</span>
                        </li>
                    @endforeach
                </ol>

                {{-- Info Ringkasan --}}
                <div class="rounded-3xl bg-gradient-to-br from-violet-600 to-indigo-700 p-7 text-white shadow-xl shadow-violet-500/25">
                    <p class="text-xs font-bold uppercase tracking-widest text-violet-200 mb-4">Ringkasan Pesanan</p>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-violet-200">ID Pesanan</span>
                            <span class="font-mono font-medium text-white">{{ $transaction->order_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-violet-200">Paket</span>
                            <span class="font-bold text-white">{{ $transaction->package_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-violet-200">Nama</span>
                            <span class="font-medium text-white">{{ $transaction->customer_name }}</span>
                        </div>
                        <div class="pt-4 border-t border-violet-500/40 flex justify-between items-end">
                            <span class="text-violet-200 font-semibold">Total</span>
                            <span class="text-2xl font-black text-white">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 border-t border-violet-500/40">
                        <p class="text-xs text-violet-200 leading-relaxed">
                            <svg class="inline h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Link aktivasi akun akan dikirim ke email <strong class="text-white">{{ $transaction->customer_email }}</strong> setelah pembayaran berhasil.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Kanan: Bayar ──────────────────────────────── --}}
            <div class="lg:col-span-3">
                <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200/70">
                    <h1 class="text-2xl font-black text-slate-900">Selesaikan Pembayaran</h1>
                    <p class="mt-1 text-sm text-slate-500">Klik tombol di bawah untuk memilih metode pembayaran yang tersedia.</p>

                    {{-- Metode Pembayaran yang tersedia --}}
                    <div class="mt-8 grid grid-cols-3 gap-3">
                        @foreach(['Transfer Bank', 'Virtual Account', 'Kartu Kredit'] as $method)
                            <div class="flex flex-col items-center justify-center gap-2 rounded-2xl border border-slate-100 bg-slate-50 p-4 text-center">
                                @if($method === 'Transfer Bank')
                                    <svg class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l9-4 9 4v2H3V6zM3 10h18M5 10v6m4-6v6m4-6v6m4-6v6M3 18h18"/></svg>
                                @elseif($method === 'Virtual Account')
                                    <svg class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                @else
                                    <svg class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                @endif
                                <span class="text-xs font-medium text-slate-600">{{ $method }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-2 text-center text-xs text-slate-400">dan banyak metode pembayaran lainnya via Midtrans</p>

                    {{-- Tombol Bayar --}}
                    <button id="pay-button"
                        class="mt-8 flex w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-5 text-base font-bold text-white shadow-xl shadow-slate-900/20 transition-all hover:scale-[1.01] hover:shadow-slate-900/30 focus:outline-none focus:ring-4 focus:ring-slate-500/20">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Pilih Metode & Bayar Sekarang
                    </button>

                    <div class="mt-6 flex items-center justify-center gap-2 text-xs text-slate-400">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Pembayaran diproses dengan enkripsi SSL 256-bit oleh Midtrans
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script type="text/javascript">
      document.getElementById('pay-button').onclick = function(){
        snap.pay('{{ $snapToken }}', {
          onSuccess: function(result){
            window.location.href = "{{ route('checkout.finish') }}?order_id={{ $transaction->order_id }}";
          },
          onPending: function(result){
            window.location.href = "{{ route('checkout.finish') }}?order_id={{ $transaction->order_id }}";
          },
          onError: function(result){
            window.location.href = "{{ route('checkout.finish') }}?order_id={{ $transaction->order_id }}";
          }
        });
      };
    </script>
</x-public-layout>
