<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Aktivitas</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Kirim Notifikasi</h1>
                <p class="mt-1 text-sm text-slate-600">Kirim email pengumuman atau pemberitahuan massal kepada pengguna terdaftar.</p>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
        <div class="p-6 sm:p-8">
            <form action="{{ route('admin.broadcast.send') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <!-- Target Penerima -->
                    <div>
                        <label for="recipient_type" class="block text-sm font-medium text-slate-700 mb-2">Target Penerima <span class="text-red-500">*</span></label>
                        <select id="recipient_type" name="recipient_type" required class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm">
                            <option value="all">Semua Pengguna (Perti & Prodi)</option>
                            <option value="perti">Hanya Akun Perguruan Tinggi (Perti)</option>
                            <option value="prodi">Hanya Akun Program Studi (Prodi)</option>
                        </select>
                        @error('recipient_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subjek Email -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-slate-700 mb-2">Subjek Email <span class="text-red-500">*</span></label>
                        <input type="text" id="subject" name="subject" required value="{{ old('subject') }}" placeholder="Contoh: Pemberitahuan Maintenance Sistem" class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Isi Pesan -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-slate-700 mb-2">Isi Pesan <span class="text-red-500">*</span></label>
                        <textarea id="message" name="message" rows="8" required placeholder="Tuliskan isi pesan atau pengumuman di sini..." class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm">{{ old('message') }}</textarea>
                        <p class="mt-2 text-xs text-slate-500">Pesan ini akan dikirimkan persis seperti yang Anda ketikkan (mendukung baris baru/enter).</p>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="reset" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">Reset Form</button>
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengirim email massal ini sekarang? Proses ini akan masuk ke antrean server.')" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-violet-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-600 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        Kirim Notifikasi Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
