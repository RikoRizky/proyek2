<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Admin</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Riwayat Transaksi</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            
            <!-- Revenue -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col justify-between">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wide mt-1">TOTAL PENDAPATAN</h3>
                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                    <p class="text-sm text-slate-500 mt-2">Hanya transaksi sukses</p>
                </div>
            </div>

            <!-- Starter Users -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col justify-between">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wide mt-1">STARTER</h3>
                    <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-4xl font-black text-slate-800 tracking-tight">{{ $starterCount }}</h2>
                    <p class="text-sm text-slate-500 mt-2">Total institusi kecil</p>
                </div>
            </div>

            <!-- Pro Users -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col justify-between">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wide mt-1">PRO</h3>
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-4xl font-black text-slate-800 tracking-tight">{{ $proCount }}</h2>
                    <p class="text-sm text-slate-500 mt-2">Total institusi menengah</p>
                </div>
            </div>

            <!-- Enterprise Users -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col justify-between">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wide mt-1">ENTERPRISE</h3>
                    <div class="w-10 h-10 rounded-full bg-violet-50 flex items-center justify-center text-violet-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-4xl font-black text-slate-800 tracking-tight">{{ $enterpriseCount }}</h2>
                    <p class="text-sm text-slate-500 mt-2">Total institusi besar</p>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="ui-card overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                <h2 class="text-lg font-bold text-slate-800">Daftar Transaksi Masuk</h2>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ !request('status') ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
                    <a href="{{ route('admin.transactions.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Pending</a>
                    <a href="{{ route('admin.transactions.index', ['status' => 'success']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ request('status') === 'success' ? 'bg-emerald-500 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Sukses</a>
                    <a href="{{ route('admin.transactions.index', ['status' => 'failed']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ request('status') === 'failed' ? 'bg-rose-500 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Gagal</a>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-sm uppercase tracking-wider border-b border-slate-200">
                            <th class="p-4 font-medium">Tanggal</th>
                            <th class="p-4 font-medium">Order ID</th>
                            <th class="p-4 font-medium">Pelanggan</th>
                            <th class="p-4 font-medium">Paket & Durasi</th>
                            <th class="p-4 font-medium">Nominal</th>
                            <th class="p-4 font-medium text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 text-slate-500 text-sm whitespace-nowrap">
                                {{ $trx->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="p-4 text-sm font-medium text-slate-700">
                                {{ $trx->order_id }}
                            </td>
                            <td class="p-4">
                                <p class="text-sm font-bold text-slate-800">{{ $trx->customer_name }}</p>
                                <p class="text-xs text-slate-500 mb-1.5">{{ $trx->customer_email }}</p>
                                @if(is_null($trx->user_id))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-700 border border-blue-200">Baru Join</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-700 border border-purple-200">Perpanjang / Upgrade</span>
                                @endif
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100">
                                    {{ $trx->package_name }}
                                </span>
                                <span class="text-xs text-slate-500 ml-1">({{ $trx->duration_years }} Thn)</span>
                            </td>
                            <td class="p-4 font-bold text-slate-700 whitespace-nowrap">
                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                @if($trx->status === 'success')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Sukses
                                    </span>
                                @elseif($trx->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Gagal
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                Belum ada riwayat transaksi langganan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
                <div class="p-4 border-t border-slate-100 bg-white">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
