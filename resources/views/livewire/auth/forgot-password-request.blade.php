<div
    class="fixed inset-0 z-[9999] w-screen h-[100dvh] flex items-center justify-center bg-surface-bright sm:bg-slate-100 overflow-hidden">

    {{-- PHONE CANVAS --}}
    <div
        class="relative w-full h-full sm:w-[375px] sm:h-[750px] sm:max-h-[90dvh] bg-surface-bright flex flex-col overflow-hidden sm:rounded-[2.5rem] sm:shadow-2xl sm:border-[8px] sm:border-slate-800">

        {{-- TOMBOL KEMBALI POJOK KIRI ATAS (Native App Feel) --}}
        @if ($step !== 'success')
            <div class="absolute top-6 left-6 z-50">
                <button wire:click="goBack"
                    class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 active:scale-90 transition-all shadow-sm border border-slate-200">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </button>
            </div>
        @endif

        {{-- CONTENT --}}
        <div class="flex flex-col w-full h-full px-6 sm:px-8">

            {{-- ========================= --}}
            {{-- STEP 1: REQUEST EMAIL & HP --}}
            {{-- ========================= --}}
            @if ($step === 'request')
                <div class="flex-1 flex flex-col justify-center mt-10">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined fill text-primary text-[32px]">lock_reset</span>
                        </div>
                        <h2 class="text-[22px] font-bold text-on-surface tracking-tight">Lupa Kata Sandi?</h2>
                        <p class="text-[13px] text-on-surface-variant mt-1 leading-relaxed max-w-[280px]">
                            Masukkan email dan nomor HP terdaftar untuk menerima 6 digit kode OTP via WhatsApp.
                        </p>
                    </div>

                    <form wire:submit="requestOtp" class="flex flex-col gap-3.5 w-full">
                        {{-- Email --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[13px] font-medium text-on-surface-variant pl-1">Email</label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">mail</span>
                                <input wire:model="email" type="email"
                                    class="w-full h-[50px] pl-12 pr-4 rounded-2xl border {{ $errors->has('email') ? 'border-error bg-error/5' : 'border-outline-variant/50' }} bg-white text-on-surface text-[14px] focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-sm"
                                    placeholder="contoh@email.com" required />
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[13px] font-medium text-on-surface-variant pl-1">Nomor HP</label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">smartphone</span>
                                <input wire:model="phone" type="tel"
                                    class="w-full h-[50px] pl-12 pr-4 rounded-2xl border {{ $errors->has('phone') ? 'border-error bg-error/5' : 'border-outline-variant/50' }} bg-white text-on-surface text-[14px] focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-sm"
                                    placeholder="08xxxxxxxxxx" required />
                            </div>
                            @error('phone')
                                <span class="text-[11px] text-error pl-1 font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full h-[50px] mt-2 bg-primary text-on-primary rounded-2xl font-bold shadow-lg active:scale-95 transition-all">
                            <span wire:loading.remove wire:target="requestOtp">Kirim Kode OTP</span>
                            <span wire:loading wire:target="requestOtp">Mengirim OTP...</span>
                        </button>
                    </form>
                </div>
            @endif

            {{-- ========================= --}}
            {{-- STEP 2: VERIFIKASI OTP --}}
            {{-- ========================= --}}
            @if ($step === 'otp')
                <div class="flex-1 flex flex-col justify-center mt-10">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined fill text-primary text-[32px]">chat</span>
                        </div>
                        <h2 class="text-[22px] font-bold text-on-surface tracking-tight">Verifikasi OTP</h2>
                        <p class="text-[13px] text-on-surface-variant mt-1 leading-relaxed max-w-[280px]">
                            Masukkan 6 digit kode yang baru saja kami kirim ke WhatsApp Anda.
                        </p>
                    </div>

                    <form wire:submit="verifyOtp" class="flex flex-col gap-4 w-full">
                        <div class="flex flex-col gap-1.5 items-center">
                            <input wire:model="otp" type="text" maxlength="6" inputmode="numeric"
                                class="w-full max-w-[240px] h-[60px] text-center rounded-2xl border {{ $errors->has('otp') ? 'border-error bg-error/5' : 'border-outline-variant/50' }} bg-white text-on-surface text-[24px] font-extrabold tracking-[0.5em] pl-4 focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-sm uppercase"
                                placeholder="------" required autofocus />
                            @error('otp')
                                <span class="text-[11px] text-error font-medium mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full h-[50px] mt-2 bg-primary text-on-primary rounded-2xl font-bold shadow-lg active:scale-95 transition-all">
                            <span wire:loading.remove wire:target="verifyOtp">Verifikasi Kode</span>
                            <span wire:loading wire:target="verifyOtp">Memeriksa...</span>
                        </button>
                    </form>
                </div>
            @endif

            {{-- ========================= --}}
            {{-- STEP 3: RESET PASSWORD --}}
            {{-- ========================= --}}
            @if ($step === 'reset')
                <div class="flex-1 flex flex-col justify-center mt-10">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined fill text-green-600 text-[32px]">key</span>
                        </div>
                        <h2 class="text-[22px] font-bold text-on-surface tracking-tight">Kata Sandi Baru</h2>
                        <p class="text-[13px] text-on-surface-variant mt-1 leading-relaxed max-w-[280px]">
                            Silakan buat kata sandi baru Anda. Pastikan mudah diingat dan aman.
                        </p>
                    </div>

                    <form wire:submit="resetPassword" class="flex flex-col gap-3.5 w-full">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[13px] font-medium text-on-surface-variant pl-1">Kata Sandi Baru</label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock</span>
                                <input wire:model="password" type="password"
                                    class="w-full h-[50px] pl-12 pr-4 rounded-2xl border {{ $errors->has('password') ? 'border-error bg-error/5' : 'border-outline-variant/50' }} bg-white text-on-surface text-[14px] focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-sm"
                                    placeholder="Minimal 8 karakter" required />
                            </div>
                            @error('password')
                                <span class="text-[11px] text-error pl-1 font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[13px] font-medium text-on-surface-variant pl-1">Ulangi Kata
                                Sandi</label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock_check</span>
                                <input wire:model="password_confirmation" type="password"
                                    class="w-full h-[50px] pl-12 pr-4 rounded-2xl border border-outline-variant/50 bg-white text-on-surface text-[14px] focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-sm"
                                    placeholder="Ketik ulang kata sandi" required />
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full h-[50px] mt-2 bg-primary text-on-primary rounded-2xl font-bold shadow-lg active:scale-95 transition-all">
                            <span wire:loading.remove wire:target="resetPassword">Simpan Kata Sandi</span>
                            <span wire:loading wire:target="resetPassword">Menyimpan...</span>
                        </button>
                    </form>
                </div>
            @endif

            {{-- ========================= --}}
            {{-- STEP 4: SUCCESS --}}
            {{-- ========================= --}}
            @if ($step === 'success')
                <div class="flex flex-col flex-1 justify-center items-center text-center gap-4 px-2">
                    <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center">
                        <span class="material-symbols-outlined fill text-green-600 text-[48px]">check_circle</span>
                    </div>
                    <h2 class="text-[22px] font-bold">Pembaruan Berhasil!</h2>
                    <p class="text-[13px] text-on-surface-variant max-w-[280px]">
                        Kata sandi Anda telah berhasil diubah. Silakan masuk menggunakan kata sandi baru.
                    </p>
                    <button wire:click="backToLogin"
                        class="w-full h-[50px] bg-primary text-on-primary rounded-2xl font-bold mt-4 shadow-lg active:scale-95 transition-all">
                        Masuk Sekarang
                    </button>
                </div>
            @endif

        </div>
    </div>
</div>
