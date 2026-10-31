<x-public-layout 
    title="Jadwalkan Konsultasi & Diskusi Akreditasi" 
    description="Jadwalkan konsultasi gratis mengenai penerapan Sistem Layanan Dokumen Akreditasi (SILADATA) berdasarkan LAM Infokom untuk perguruan tinggi Anda."
>
    <div x-data="{
        step: 1,
        nama: '',
        email: '',
        whatsapp: '',
        pt: '',
        pt_lainnya: '',
        jabatan: '',
        jabatan_lainnya: '',
        kebutuhan: [],
        kebutuhan_lainnya: '',
        sistem_saat_ini: '',
        investasi: '',
        robot_checked: false,
        errors: {
            nama: '',
            email: '',
            whatsapp: '',
            pt: '',
            pt_lainnya: '',
            jabatan: '',
            jabatan_lainnya: '',
            kebutuhan: '',
            kebutuhan_lainnya: '',
            sistem_saat_ini: '',
            investasi: ''
        },
        validateStep1() {
            this.errors.nama = !this.nama ? 'Nama Lengkap wajib diisi' : '';
            this.errors.email = !this.email ? 'Email wajib diisi' : (!this.email.includes('@') ? 'Format email tidak valid' : '');
            this.errors.whatsapp = !this.whatsapp ? 'Nomor WhatsApp wajib diisi' : '';
            this.errors.pt = !this.pt ? 'Silakan pilih Perguruan Tinggi' : '';
            this.errors.pt_lainnya = (this.pt === 'Lainnya' && !this.pt_lainnya.trim()) ? 'Silakan masukkan nama perguruan tinggi Anda' : '';
            this.errors.jabatan = !this.jabatan ? 'Silakan pilih Jabatan' : '';
            this.errors.jabatan_lainnya = (this.jabatan === 'Lainnya' && !this.jabatan_lainnya.trim()) ? 'Silakan masukkan jabatan Anda' : '';

            if (!this.errors.nama && !this.errors.email && !this.errors.whatsapp && !this.errors.pt && !this.errors.pt_lainnya && !this.errors.jabatan && !this.errors.jabatan_lainnya) {
                this.step = 2;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        validateStep2() {
            this.errors.kebutuhan = this.kebutuhan.length === 0 ? 'Pilih minimal 1 kebutuhan utama' : '';
            this.errors.kebutuhan_lainnya = (this.kebutuhan.includes('Lainnya') && !this.kebutuhan_lainnya.trim()) ? 'Silakan jelaskan kebutuhan Anda' : '';
            this.errors.sistem_saat_ini = !this.sistem_saat_ini ? 'Silakan pilih sistem informasi akademik saat ini' : '';
            this.errors.investasi = !this.investasi ? 'Silakan pilih kesiapan investasi digital' : '';

            if (!this.errors.kebutuhan && !this.errors.kebutuhan_lainnya && !this.errors.sistem_saat_ini && !this.errors.investasi) {
                this.step = 3;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    }" class="relative flex-grow pb-16">
        <!-- Header Banner -->
        <div class="bg-violet-700 pt-16 pb-32 text-center text-white px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl">
                <h1 class="text-3xl font-extrabold sm:text-4xl">
                    Diskusi dengan tim kami dan dapatkan solusi terbaik
                </h1>
                <p class="mt-4 text-violet-100 text-sm sm:text-base">
                    Silakan isi formulir, sehingga tim kami dapat dengan senang hati menghubungi Anda untuk memberikan solusi terbaik
                </p>

                @if(!session('success'))
                <!-- Step Progress Indicator -->
                <div class="mt-8 flex items-center justify-center gap-4 text-xs font-semibold text-white sm:text-sm">
                    <!-- Step 1 -->
                    <div class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs transition-colors duration-200"
                              :class="step > 1 ? 'bg-emerald-500 text-white' : (step === 1 ? 'bg-white text-violet-700 font-bold' : 'bg-white/30 text-white/70')">
                            <template x-if="step > 1">
                                <span>✓</span>
                            </template>
                            <template x-if="step === 1">
                                <span>1</span>
                            </template>
                        </span>
                        <span :class="step >= 1 ? 'text-white' : 'text-white/60'">Personal Info</span>
                    </div>
                    
                    <div class="h-[1px] w-8 sm:w-16 bg-white/30"></div>

                    <!-- Step 2 -->
                    <div class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs transition-colors duration-200"
                              :class="step > 2 ? 'bg-emerald-500 text-white' : (step === 2 ? 'bg-white text-violet-700 font-bold' : 'bg-white/30 text-white/70')">
                            <template x-if="step > 2">
                                <span>✓</span>
                            </template>
                            <template x-if="step <= 2">
                                <span>2</span>
                            </template>
                        </span>
                        <span :class="step >= 2 ? 'text-white' : 'text-white/60'">Kebutuhan</span>
                    </div>

                    <div class="h-[1px] w-8 sm:w-16 bg-white/30"></div>

                    <!-- Step 3 -->
                    <div class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs transition-colors duration-200"
                              :class="step === 3 ? 'bg-white text-violet-700 font-bold' : 'bg-white/30 text-white/70'">
                            <span>3</span>
                        </span>
                        <span :class="step === 3 ? 'text-white' : 'text-white/60'">Terkirim</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 mt-[-80px]">
            <div class="bg-white rounded-[24px] shadow-xl p-8 border border-slate-100">
                @if(session('success'))
                    <!-- Success State -->
                    <div class="text-center py-12 px-4">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-3xl mb-6">
                            ✓
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 mb-2">Formulir Terkirim!</h2>
                        <p class="text-slate-600 max-w-md mx-auto mb-8">
                            {{ session('success') }}
                        </p>
                        <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 hover:bg-violet-700 px-6 py-3.5 font-bold text-white transition hover:scale-[1.02] active:scale-[0.98]">
                            Kembali ke Beranda
                        </a>
                    </div>
                @else
                    <!-- Form Submission -->
                    <form action="{{ route('discussion.store') }}" method="POST" @submit="validateStep3($event)">
                        @csrf

                        <!-- STEP 1: Personal Info -->
                        <div x-show="step === 1" x-transition>
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Lengkap*</label>
                                <input type="text" x-model="nama" name="nama" placeholder="Nama Lengkap" 
                                       class="w-full rounded-xl border-slate-200 bg-white text-slate-900 shadow-sm focus:border-violet-500 focus:ring-violet-500/30">
                                <p class="text-red-500 text-xs mt-1 font-medium" x-text="errors.nama"></p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Email*</label>
                                    <input type="email" x-model="email" name="email" placeholder="Email"
                                           class="w-full rounded-xl border-slate-200 bg-white text-slate-900 shadow-sm focus:border-violet-500 focus:ring-violet-500/30">
                                    <p class="text-red-500 text-xs mt-1 font-medium" x-text="errors.email"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Nomor WhatsApp*</label>
                                    <input type="tel" x-model="whatsapp" name="whatsapp" placeholder="Contoh: 81234567890"
                                           class="w-full rounded-xl border-slate-200 bg-white text-slate-900 shadow-sm focus:border-violet-500 focus:ring-violet-500/30">
                                    <p class="text-red-500 text-xs mt-1 font-medium" x-text="errors.whatsapp"></p>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Perguruan Tinggi*</label>
                                <select x-model="pt" name="perusahaan" 
                                        class="w-full rounded-xl border-slate-200 bg-white text-slate-900 shadow-sm focus:border-violet-500 focus:ring-violet-500/30">
                                    <option value="" disabled selected>Pilih PT Anda</option>
                                    <option value="Universitas Indonesia">Universitas Indonesia</option>
                                    <option value="Institut Teknologi Bandung">Institut Teknologi Bandung</option>
                                    <option value="Universitas Gadjah Mada">Universitas Gadjah Mada</option>
                                    <option value="Universitas Airlangga">Universitas Airlangga</option>
                                    <option value="Universitas Diponegoro">Universitas Diponegoro</option>
                                    <option value="Universitas Brawijaya">Universitas Brawijaya</option>
                                    <option value="Universitas Padjadjaran">Universitas Padjadjaran</option>
                                    <option value="Universitas Sebelas Maret">Universitas Sebelas Maret</option>
                                    <option value="Universitas Hasanuddin">Universitas Hasanuddin</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <p class="text-red-500 text-xs mt-1 font-medium" x-text="errors.pt"></p>

                                {{-- Input manual saat pilih Lainnya --}}
                                <div x-show="pt === 'Lainnya'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3">
                                    <label class="block text-xs font-semibold text-violet-700 mb-1">✏️ Masukkan nama Perguruan Tinggi Anda*</label>
                                    <input type="text" x-model="pt_lainnya" name="perusahaan_lainnya"
                                           placeholder="Contoh: Universitas Nusantara Mandiri"
                                           class="w-full rounded-xl border-violet-300 bg-violet-50/40 text-slate-900 shadow-sm focus:border-violet-500 focus:ring-violet-500/30 placeholder:text-slate-400">
                                    <p class="text-red-500 text-xs mt-1 font-medium" x-text="errors.pt_lainnya"></p>
                                </div>
                            </div>

                            <div class="mb-8">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Jabatan di Perguruan Tinggi*</label>
                                <select x-model="jabatan" name="jabatan" 
                                        class="w-full rounded-xl border-slate-200 bg-white text-slate-900 shadow-sm focus:border-violet-500 focus:ring-violet-500/30">
                                    <option value="" disabled selected>Pilih Jabatan</option>
                                    <option value="Rektor / Wakil Rektor">Rektor / Wakil Rektor</option>
                                    <option value="Dekan / Wakil Dekan">Dekan / Wakil Dekan</option>
                                    <option value="Ketua Program Studi">Ketua Program Studi</option>
                                    <option value="Kepala Penjaminan Mutu (LPM)">Kepala Penjaminan Mutu (LPM)</option>
                                    <option value="Dosen">Dosen</option>
                                    <option value="Staff Administrasi">Staff Administrasi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <p class="text-red-500 text-xs mt-1 font-medium" x-text="errors.jabatan"></p>

                                {{-- Input manual saat pilih Lainnya --}}
                                <div x-show="jabatan === 'Lainnya'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3">
                                    <label class="block text-xs font-semibold text-violet-700 mb-1">✏️ Masukkan jabatan Anda*</label>
                                    <input type="text" x-model="jabatan_lainnya" name="jabatan_lainnya"
                                           placeholder="Contoh: Kepala Biro Akademik"
                                           class="w-full rounded-xl border-violet-300 bg-violet-50/40 text-slate-900 shadow-sm focus:border-violet-500 focus:ring-violet-500/30 placeholder:text-slate-400">
                                    <p class="text-red-500 text-xs mt-1 font-medium" x-text="errors.jabatan_lainnya"></p>
                                </div>
                            </div>

                            <button type="button" @click="validateStep1()" 
                                    class="w-full bg-violet-600 hover:bg-violet-700 text-white font-bold py-3.5 px-6 rounded-xl transition duration-150 hover:scale-[1.01] active:scale-[0.99] shadow-md shadow-violet-500/20">
                                Lanjut
                            </button>
                        </div>

                        <!-- STEP 2: Kebutuhan -->
                        <div x-show="step === 2" x-transition>
                            <div class="mb-6">
                                <h3 class="text-xl font-bold text-slate-900">Kebutuhan Kampus</h3>
                                <p class="text-sm text-slate-500 mb-6">Pilih kebutuhan yang sesuai dengan institusi Anda</p>
                                
                                <label class="block text-sm font-bold text-slate-700 mb-3">Apa kebutuhan utama institusi Anda? (pilih maksimal 3)*</label>
                                
                                <div class="space-y-2.5">
                                    <template x-for="opt in [
                                        'Pemenuhan dan pengelolaan pelaporan regulasi kampus (OBE, KPL, PDDIKTI, Akreditasi, SPMI)',
                                        'Peningkatan sistem akademik agar lebih rapi, stabil, dan mudah digunakan',
                                        'Penggunaan satu sistem terpadu untuk akademik, pembelajaran, dan keuangan',
                                        'Dukungan pembelajaran daring dan hybrid melalui LMS',
                                        'Pengelolaan keuangan kampus yang lebih tertata dan terintegrasi',
                                        'Pendampingan dan dukungan sistem yang berkelanjutan dari vendor',
                                        'Pengelolaan data dan akses pengguna yang lebih aman dan tidak bergantung pada satu orang',
                                        'Kesiapan digital kampus untuk pengembangan dan peningkatan daya saing',
                                        'Lainnya'
                                    ]">
                                        <label class="flex items-start gap-3 rounded-xl border p-4 transition cursor-pointer hover:border-violet-400 hover:bg-violet-50/5"
                                               :class="kebutuhan.includes(opt) ? 'border-violet-500 bg-violet-50/10' : 'border-slate-200 bg-white'">
                                            <input type="checkbox" name="kebutuhan[]" :value="opt" x-model="kebutuhan" 
                                                   :disabled="kebutuhan.length >= 3 && !kebutuhan.includes(opt)"
                                                   class="mt-1 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                            <span class="text-sm text-slate-700 font-medium" x-text="opt"></span>
                                        </label>
                                    </template>
                                </div>
                                <p class="text-red-500 text-xs mt-1 font-medium" x-text="errors.kebutuhan"></p>

                                {{-- Textarea manual jika memilih Lainnya pada kebutuhan --}}
                                <div x-show="kebutuhan.includes('Lainnya')" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3">
                                    <label class="block text-xs font-semibold text-violet-700 mb-1">✏️ Jelaskan kebutuhan lainnya Anda*</label>
                                    <textarea x-model="kebutuhan_lainnya" name="kebutuhan_lainnya" rows="3"
                                              placeholder="Tuliskan kebutuhan spesifik institusi Anda di sini..."
                                              class="w-full rounded-xl border-violet-300 bg-violet-50/40 text-slate-900 shadow-sm focus:border-violet-500 focus:ring-violet-500/30 placeholder:text-slate-400 resize-none"></textarea>
                                    <p class="text-red-500 text-xs mt-1 font-medium" x-text="errors.kebutuhan_lainnya"></p>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-bold text-slate-700 mb-3">Sistem informasi akademik yang digunakan saat ini*</label>
                                
                                <div class="space-y-2.5">
                                    <template x-for="opt in [
                                        {val: 'internal', label: 'Menggunakan sistem yang dikelola dan dikembangkan secara internal'},
                                        {val: 'vendor', label: 'Menggunakan sistem yang disediakan oleh mitra/vendor'},
                                        {val: 'community', label: 'Menggunakan sistem pendukung atau komunitas'},
                                        {val: 'none', label: 'Saat ini belum menggunakan sistem akademik'}
                                    ]">
                                        <label class="flex items-center gap-3 rounded-xl border p-4 transition cursor-pointer hover:border-violet-400 hover:bg-violet-50/5"
                                               :class="sistem_saat_ini === opt.val ? 'border-violet-500 bg-violet-50/10' : 'border-slate-200 bg-white'">
                                            <input type="radio" name="sistem_saat_ini" :value="opt.val" x-model="sistem_saat_ini" 
                                                   class="border-slate-300 text-violet-600 focus:ring-violet-500">
                                            <span class="text-sm text-slate-700 font-medium" x-text="opt.label"></span>
                                        </label>
                                    </template>
                                </div>
                                <p class="text-red-500 text-xs mt-1 font-medium" x-text="errors.sistem_saat_ini"></p>
                            </div>

                            <div class="mb-8">
                                <label class="block text-sm font-bold text-slate-700 mb-3">Kesiapan investasi digital institusi*</label>
                                
                                <div class="space-y-2.5">
                                    <template x-for="opt in [
                                        {val: 'near', label: 'Siap direalisasikan dalam waktu dekat'},
                                        {val: 'budgeted', label: 'Sudah dianggarkan, menunggu penyesuaian internal'},
                                        {val: 'planning', label: 'Sedang dalam tahap perencanaan anggaran'},
                                        {val: 'next', label: 'Dianggarkan untuk periode selanjutnya'}
                                    ]">
                                        <label class="flex items-center gap-3 rounded-xl border p-4 transition cursor-pointer hover:border-violet-400 hover:bg-violet-50/5"
                                               :class="investasi === opt.val ? 'border-violet-500 bg-violet-50/10' : 'border-slate-200 bg-white'">
                                            <input type="radio" name="investasi" :value="opt.val" x-model="investasi" 
                                                   class="border-slate-300 text-violet-600 focus:ring-violet-500">
                                            <span class="text-sm text-slate-700 font-medium" x-text="opt.label"></span>
                                        </label>
                                    </template>
                                </div>
                                <p class="text-red-500 text-xs mt-1 font-medium" x-text="errors.investasi"></p>
                            </div>

                            <div class="flex gap-4">
                                <button type="button" @click="step = 1; window.scrollTo({ top: 0, behavior: 'smooth' });"
                                        class="w-1/3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 px-6 rounded-xl transition duration-150">
                                    Kembali
                                </button>
                                <button type="button" @click="validateStep2()" 
                                        class="w-2/3 bg-violet-600 hover:bg-violet-700 text-white font-bold py-3.5 px-6 rounded-xl transition duration-150 hover:scale-[1.01] active:scale-[0.99] shadow-md shadow-violet-500/20">
                                    Lanjutkan
                                </button>
                            </div>
                        </div>

                        <!-- STEP 3: Terkirim -->
                        <div x-show="step === 3" x-transition>
                            <div class="mb-8 border border-slate-200 rounded-xl p-5 bg-slate-50 flex items-center justify-between max-w-sm mx-auto shadow-sm">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" id="recaptcha" x-model="robot_checked"
                                           class="rounded border-slate-300 text-violet-600 focus:ring-violet-500 h-6 w-6 cursor-pointer">
                                    <label for="recaptcha" class="text-sm font-semibold text-slate-700 select-none cursor-pointer">I'm not a robot</label>
                                </div>
                                <div class="flex flex-col items-center select-none text-[10px] text-slate-400">
                                    <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="reCAPTCHA" class="h-6 w-6 mb-1">
                                    <span>reCAPTCHA</span>
                                    <span class="text-[8px] text-slate-300">Privacy - Terms</span>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <button type="button" @click="step = 2; window.scrollTo({ top: 0, behavior: 'smooth' });"
                                        class="w-1/3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 px-6 rounded-xl transition duration-150">
                                    Kembali
                                </button>
                                <button type="submit"
                                        class="w-2/3 bg-violet-600 hover:bg-violet-700 text-white font-bold py-3.5 px-6 rounded-xl transition duration-150 hover:scale-[1.01] active:scale-[0.99] shadow-md shadow-violet-500/20">
                                    Kirim Formulir Diskusi
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('formHandler', () => ({
                // Setup for inline x-data context mapping
            }));
        });
    </script>
</x-public-layout>
