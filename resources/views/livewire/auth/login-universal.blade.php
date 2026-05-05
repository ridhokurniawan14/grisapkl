{{-- MANTRA SAPU JAGAT: w-screen dan z-[9999] akan mengabaikan padding dari layout utama! --}}
<div x-data="{ splash: true }" x-init="setTimeout(() => splash = false, 1600)"
    class="fixed inset-0 z-[9999] w-screen h-[100dvh] flex items-center justify-center bg-surface-bright sm:bg-slate-100 overflow-hidden m-0 p-0">

    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes logoPop {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-logo-pop {
            animation: logoPop 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>

    {{-- ========================================================== --}}
    {{-- THE PHONE CANVAS: Di HP akan full layar, di Desktop jadi Mockup HP (Lebar 375px) --}}
    {{-- ========================================================== --}}
    <div
        class="relative w-full h-full sm:w-[375px] sm:h-[750px] sm:max-h-[90dvh] bg-surface-bright flex flex-col overflow-hidden sm:rounded-[2.5rem] sm:shadow-2xl sm:border-[8px] sm:border-slate-800">

        {{-- ============================== --}}
        {{-- 1. SPLASH SCREEN (True Center) --}}
        {{-- ============================== --}}
        <div x-show="splash" x-transition:leave="transition ease-in duration-400" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-[100] flex items-center justify-center bg-surface-bright w-full h-full">

            <div class="animate-logo-pop flex items-center justify-center">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo"
                        class="w-28 h-28 object-contain rounded-[1.5rem] drop-shadow-xl">
                @else
                    <div class="w-28 h-28 bg-primary/10 flex items-center justify-center rounded-[1.5rem]">
                        <span class="material-symbols-outlined fill text-primary text-[56px]">work</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ======================================= --}}
        {{-- 2. LOGIN FORM (Mobile First & No Scroll) --}}
        {{-- ======================================= --}}
        <div x-show="!splash" x-cloak x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0"
            class="flex flex-col w-full h-full relative z-10 px-6 sm:px-8">

            {{-- AREA TENGAH: Form dipaksa ke Absolute Center secara Vertikal --}}
            <div class="flex-1 flex flex-col justify-center w-full">

                {{-- Header --}}
                <div class="flex flex-col items-center text-center mb-6 w-full">
                    <div class="w-[68px] h-[68px] mb-3 flex items-center justify-center">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo"
                                class="w-full h-full object-contain rounded-[1rem] drop-shadow-sm">
                        @else
                            <div class="w-full h-full bg-primary/10 flex items-center justify-center rounded-[1rem]">
                                <span class="material-symbols-outlined fill text-primary text-[32px]">work</span>
                            </div>
                        @endif
                    </div>
                    <h2 class="text-[22px] font-bold text-on-surface tracking-tight">Selamat Datang</h2>
                    <p class="text-[13px] text-on-surface-variant mt-0.5">Masuk untuk memulai aktivitas PKL</p>
                </div>

                {{-- Form --}}
                <form wire:submit="authenticate" class="flex flex-col gap-3 w-full">

                    {{-- Email Input --}}
                    <div class="flex flex-col gap-1 w-full">
                        <label class="text-[13px] font-medium text-on-surface-variant pl-1">Email</label>
                        <div class="relative w-full">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline pointer-events-none select-none">person</span>

                            <input wire:model="identifier" type="email" autocomplete="email"
                                class="w-full h-[50px] pl-12 pr-4 rounded-2xl border
                                       {{ $errors->has('identifier') ? 'border-error bg-error/5' : 'border-outline-variant/50' }}
                                       bg-white text-on-surface text-[16px] placeholder-outline/50
                                       focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-sm"
                                placeholder="Masukkan email..." required />
                        </div>
                        @error('identifier')
                            <span class="text-error text-[11px] font-medium pl-1 flex items-center gap-1 mt-0.5">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Password Input --}}
                    <div class="flex flex-col gap-1 w-full" x-data="{ show: false }">
                        <label class="text-[13px] font-medium text-on-surface-variant pl-1">Kata Sandi</label>
                        <div class="relative w-full">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline pointer-events-none select-none">lock</span>

                            <input wire:model="password" :type="show ? 'text' : 'password'"
                                autocomplete="current-password"
                                class="w-full h-[50px] pl-12 pr-12 rounded-2xl border border-outline-variant/50
                                       bg-white text-on-surface text-[16px] placeholder-outline/50
                                       focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-sm"
                                placeholder="Masukkan sandi..." required />

                            <button type="button" @click="show = !show"
                                class="absolute right-1.5 top-1/2 -translate-y-1/2 w-10 h-10
                                       flex items-center justify-center text-outline hover:text-primary
                                       transition-colors rounded-full focus:outline-none">
                                <span class="material-symbols-outlined text-[20px]"
                                    x-text="show ? 'visibility' : 'visibility_off'">visibility_off</span>
                            </button>
                        </div>
                    </div>

                    {{-- Lupa Sandi Link --}}
                    <div class="flex justify-end mt-[-2px] w-full">
                        <a href="{{ route('auth.forgot-password') }}"
                            class="text-[13px] text-primary hover:text-primary-fixed-variant hover:underline font-semibold transition-colors">
                            Lupa Kata Sandi?
                        </a>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full h-[50px] mt-1 bg-primary hover:bg-primary-fixed-variant text-on-primary
                               text-[15px] font-semibold rounded-2xl flex items-center justify-center gap-2
                               transition-all active:scale-[0.98] shadow-lg hover:shadow-primary/30">
                        <span wire:loading.remove wire:target="authenticate">Masuk Sekarang</span>
                        <span wire:loading wire:target="authenticate" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                            Memeriksa...
                        </span>
                    </button>
                </form>

            </div> {{-- Akhir Flex-1 Pembungkus Form Tengah --}}

            {{-- AREA BAWAH: Footer (Didorong otomatis oleh flex-1 ke bawah layar) --}}
            <div class="w-full pb-6 pt-4 text-center">
                <p class="text-[13px] text-on-surface-variant">
                    Belum punya akun?
                    <a href="#" class="text-primary font-semibold hover:underline ml-0.5">Hubungi Admin
                        Sekolah</a>
                </p>
            </div>

        </div>
    </div>
</div>
