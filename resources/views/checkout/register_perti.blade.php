<x-public-layout 
    title="Registrasi Akun Perguruan Tinggi" 
    description="Buat akun admin untuk Perguruan Tinggi Anda."
>
    <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-100">
            <h1 class="text-2xl font-bold text-slate-900 mb-2">Buat Akun Perguruan Tinggi</h1>
            <p class="text-slate-500 mb-8">Pendaftaran khusus untuk pembeli <b>Paket {{ $transaction->package_name }}</b>.</p>

            <div class="mb-6">
                <a href="{{ route('google.login', ['registration_token' => $transaction->registration_token]) }}" class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-violet-500/30 transition-all">
                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Daftar dengan Google
                </a>
            </div>

            <div class="relative flex items-center mb-6">
                <div class="flex-grow border-t border-slate-200"></div>
                <span class="mx-4 text-xs font-medium text-slate-400">ATAU DAFTAR MANUAL</span>
                <div class="flex-grow border-t border-slate-200"></div>
            </div>

            <form action="{{ route('register-perti.process', $transaction->registration_token) }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nama Perguruan Tinggi</label>
                        <div class="mt-2">
                            <input id="name" name="name" type="text" value="{{ old('name', $transaction->customer_name) }}" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-violet-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="kode_pt" class="block text-sm font-medium leading-6 text-gray-900">Kode PT (Opsional)</label>
                        <div class="mt-2">
                            <input id="kode_pt" name="kode_pt" type="text" value="{{ old('kode_pt') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-violet-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('kode_pt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email Login</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" value="{{ old('email', $transaction->customer_email) }}" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-violet-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Kata Sandi</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-violet-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">Konfirmasi Kata Sandi</label>
                        <div class="mt-2">
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-violet-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="flex w-full justify-center rounded-md bg-emerald-600 px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">Daftar Akun Sekarang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>
