<div
    class="fixed inset-0 z-[9999] w-screen h-[100dvh] flex items-center justify-center bg-surface-bright sm:bg-slate-100 overflow-hidden">

    {{-- PHONE CANVAS --}}
    <div
        class="relative w-full h-full sm:w-[375px] sm:h-[750px] sm:max-h-[90dvh]
               bg-surface-bright flex flex-col overflow-hidden
               sm:rounded-[2.5rem] sm:shadow-2xl sm:border-[8px] sm:border-slate-800">

        {{-- CONTENT --}}
        <div class="flex flex-col w-full h-full px-6 sm:px-8">

            {{-- ========================= --}}
            {{-- STEP 1: FORM --}}
            {{-- ========================= --}}
            @if ($step === 'form')
                <div class="flex-1 flex flex-col justify-center">

                    {{-- Header --}}
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined fill text-primary text-[32px]">lock_reset</span>
                        </div>
                        <h2 class="text-[22px] font-bold text-on-surface tracking-tight">Lupa Kata Sandi?</h2>
                        <p class="text-[13px] text-on-surface-variant mt-1 leading-relaxed max-w-[280px]">
                            Masukkan email dan nomor HP terdaftar. Kata sandi baru akan dikirim via WhatsApp.
                        </p>
                    </div>

                    {{-- Form --}}
                    <form wire:submit="sendNewPassword" class="flex flex-col gap-3.5 w-full">

                        {{-- Email --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[13px] font-medium text-on-surface-variant pl-1">Email</label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">mail</span>
                                <input wire:model="email" type="email"
                                    class="w-full h-[50px] pl-12 pr-4 rounded-2xl border
                                    {{ $errors->has('email') ? 'border-error bg-error/5' : 'border-outline-variant/50' }}
                                    bg-white text-on-surface text-[16px]
                                    focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-sm"
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
                                    class="w-full h-[50px] pl-12 pr-4 rounded-2xl border
                                    {{ $errors->has('phone') ? 'border-error bg-error/5' : 'border-outline-variant/50' }}
                                    bg-white text-on-surface text-[16px]
                                    focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-sm"
                                    placeholder="08xxxxxxxxxx" required />
                            </div>
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full h-[50px] mt-2 bg-primary text-on-primary rounded-2xl font-semibold shadow-lg">
                            <span wire:loading.remove wire:target="sendNewPassword">
                                Kirim Kata Sandi Baru
                            </span>
                            <span wire:loading wire:target="sendNewPassword">
                                Mengirim...
                            </span>
                        </button>

                    </form>

                </div>

                {{-- Footer --}}
                <div class="pb-6 pt-4 text-center">
                    <a href="{{ route('login') }}"
                        class="text-[13px] text-primary font-semibold flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                        Kembali ke Login
                    </a>
                </div>
            @endif

            {{-- ========================= --}}
            {{-- STEP 2: SUCCESS --}}
            {{-- ========================= --}}
            @if ($step === 'success')
                <div class="flex flex-col flex-1 justify-center items-center text-center gap-4 px-2">

                    <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center">
                        <span class="material-symbols-outlined fill text-green-700 text-[48px]">check_circle</span>
                    </div>

                    <h2 class="text-[22px] font-bold">Berhasil!</h2>

                    <p class="text-[13px] text-on-surface-variant max-w-[280px]">
                        Kata sandi baru sudah dikirim ke WhatsApp kamu.
                    </p>

                    <button wire:click="backToLogin"
                        class="w-full h-[50px] bg-primary text-on-primary rounded-2xl font-semibold mt-3">
                        Masuk Sekarang
                    </button>

                </div>
            @endif

        </div>
    </div>
</div>
