<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Profile Photo Section --}}
        <div class="flex items-start gap-4">
            <img src="{{ $user->profile_photo_url }}" alt="Profile Photo" class="h-16 w-16 rounded-full object-cover ring-2 ring-violet-500/20 bg-slate-100 shrink-0">
            <div class="min-w-0 flex-1">
                <x-input-label for="profile_photo" :value="__('Foto Profil')" />
                <p class="text-xs text-slate-400 mb-1.5">Format: JPEG, PNG, JPG, GIF · Maks. 2 MB</p>
                <input id="profile_photo" name="profile_photo" type="file"
                    class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100"
                    accept="image/jpeg,image/png,image/jpg,image/gif"
                    onchange="validateProfilePhoto(this)" />

                {{-- Server-side validation errors --}}
                @if ($errors->has('profile_photo'))
                    <div class="mt-2 flex items-start gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5">
                        <svg class="h-4 w-4 shrink-0 text-rose-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        <div>
                            <p class="text-xs font-bold text-rose-700">Gagal mengunggah foto profil</p>
                            <p class="text-xs text-rose-600 mt-0.5">{{ $errors->first('profile_photo') }}</p>
                        </div>
                    </div>
                @endif

                {{-- Client-side error (shown immediately on file select) --}}
                <div id="photo-client-error" class="mt-2 hidden flex items-start gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5">
                    <svg class="h-4 w-4 shrink-0 text-rose-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                    <div>
                        <p class="text-xs font-bold text-rose-700">Gagal memilih foto</p>
                        <p id="photo-client-error-msg" class="text-xs text-rose-600 mt-0.5"></p>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function validateProfilePhoto(input) {
            const errorBox = document.getElementById('photo-client-error');
            const errorMsg = document.getElementById('photo-client-error-msg');
            errorBox.classList.add('hidden');
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            const maxBytes = 2 * 1024 * 1024; // 2 MB
            const allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!allowed.includes(file.type)) {
                errorMsg.textContent = 'Format foto tidak didukung. Gunakan JPEG, PNG, JPG, atau GIF.';
                errorBox.classList.remove('hidden');
                input.value = '';
                return;
            }
            if (file.size > maxBytes) {
                const sizeMb = (file.size / 1024 / 1024).toFixed(1);
                errorMsg.textContent = `Foto terlalu besar (${sizeMb} MB). Batas maksimal adalah 2 MB. Silakan pilih gambar yang lebih kecil.`;
                errorBox.classList.remove('hidden');
                input.value = '';
                return;
            }
        }
        </script>


        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-slate-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="text-sm font-semibold text-violet-600 hover:text-violet-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-semibold text-emerald-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-emerald-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
