<div
    class="w-full max-w-[420px] bg-white/90 backdrop-blur-lg rounded-2xl shadow-[0_20px_60px_-15px_rgba(79,70,229,0.15)] relative z-10 overflow-hidden border border-white/50">
    <!-- Top Accent Line -->
    <div class="h-1.5 w-full bg-gradient-to-r from-primary via-primary/80 to-secondary"></div>

    <div class="p-[32px] flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-col items-center text-center gap-2 mb-4">
            <div
                class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary/10 to-white rounded-2xl mb-3 shadow-inner border border-primary/5">
                <span class="material-symbols-outlined fill text-primary text-[36px]">hub</span>
            </div>
            <h1 class="font-h1 text-[24px] font-semibold text-gray-900 tracking-tight">Grisa PKL</h1>
            <p class="font-body-md text-[14px] text-gray-500">Masuk untuk memulai aktivitas PKL Anda</p>
        </div>

        <!-- Form -->
        <form wire:submit="authenticate" class="flex flex-col gap-4">
            <!-- Identity Input -->
            <div class="flex flex-col gap-1">
                <label class="text-[12px] font-medium text-gray-600 pl-1">Email atau NISN/NIP</label>
                <input wire:model="identifier" type="text"
                    class="w-full h-[48px] px-4 rounded-xl border {{ $errors->has('identifier') ? 'border-red-500' : 'border-gray-300' }} bg-white text-gray-900 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-all shadow-sm"
                    placeholder="Masukkan identitas Anda" required />
                @error('identifier')
                    <span class="text-xs text-red-500 pl-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Input -->
            <div class="flex flex-col gap-1">
                <label class="text-[12px] font-medium text-gray-600 pl-1">Kata Sandi</label>
                <div class="relative">
                    <input wire:model="password" type="password"
                        class="w-full h-[48px] pl-4 pr-12 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-all shadow-sm"
                        placeholder="Masukkan kata sandi" required />
                    <button type="button"
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center text-gray-400 hover:text-primary transition-colors rounded-full focus:outline-none">
                        <span class="material-symbols-outlined">visibility_off</span>
                    </button>
                </div>
            </div>

            <!-- Forgot Password Link -->
            <div class="flex justify-end mt-[-4px] mb-2">
                <a href="#" class="text-[12px] text-primary hover:underline font-medium">Lupa Kata Sandi?</a>
            </div>

            <!-- Submit Action -->
            <button type="submit"
                class="w-full h-[48px] bg-gradient-to-r from-primary to-primary/90 text-white font-semibold rounded-xl flex items-center justify-center gap-2 transition-all active:scale-[0.98] shadow-lg hover:shadow-primary/30">
                <span wire:loading.remove wire:target="authenticate">Masuk</span>
                <span wire:loading wire:target="authenticate">Memeriksa...</span>
                <span wire:loading.remove wire:target="authenticate"
                    class="material-symbols-outlined text-[20px]">arrow_forward</span>
            </button>
        </form>

        <!-- Footer Segment -->
        <div class="mt-4 pt-4 border-t border-gray-100 text-center">
            <p class="text-[14px] text-gray-500">
                Belum punya akun? <br class="md:hidden" />
                <a href="#" class="text-primary font-semibold hover:underline">Hubungi Admin Sekolah</a>
            </p>
        </div>
    </div>
</div>
