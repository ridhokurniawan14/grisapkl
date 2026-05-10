<div class="relative w-full pb-6">

    <div class="flex items-center gap-3 mb-5 pt-2 px-1">
        <a href="{{ route('pembimbing.profil') }}" wire:navigate
            class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 text-slate-600 hover:bg-slate-100 active:scale-95 transition-all shrink-0">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex-1 overflow-hidden">
            <h2 class="text-[20px] font-extrabold text-slate-800 tracking-tight leading-tight truncate">Ubah Password
            </h2>
            <p class="text-[12px] font-bold text-[#3525cd] truncate">Ganti kata sandi keamanan akunmu</p>
        </div>
    </div>

    <section class="bg-white rounded-[1.5rem] p-5 shadow-sm border border-slate-200 flex flex-col gap-4 relative mx-1">

        <form wire:submit="updatePassword" class="flex flex-col gap-5">

            <div class="flex flex-col gap-1.5 relative group" x-data="{ show: false }">
                <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Password Saat
                    Ini</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="current_password"
                        placeholder="Masukkan sandi lama..."
                        class="w-full bg-slate-50 border {{ $errors->has('current_password') ? 'border-red-400 focus:border-red-500' : 'border-slate-200 focus:border-[#3525cd]' }} text-slate-800 font-bold text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 transition-all px-4 py-3 h-12 shadow-inner pr-12">

                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-[#3525cd] transition-colors focus:outline-none">
                        <span class="material-symbols-outlined text-[20px]"
                            x-text="show ? 'visibility' : 'visibility_off'"></span>
                    </button>
                </div>
                @error('current_password')
                    <span class="text-[11px] text-red-500 font-bold pl-1 flex items-center gap-1"><span
                            class="material-symbols-outlined text-[12px]">error</span> {{ $message }}</span>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5 relative group" x-data="{ show: false }">
                <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Password
                    Baru</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="password" placeholder="Minimal 8 karakter..."
                        class="w-full bg-slate-50 border {{ $errors->has('password') ? 'border-red-400 focus:border-red-500' : 'border-slate-200 focus:border-[#3525cd]' }} text-slate-800 font-bold text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 transition-all px-4 py-3 h-12 shadow-inner pr-12">

                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-[#3525cd] transition-colors focus:outline-none">
                        <span class="material-symbols-outlined text-[20px]"
                            x-text="show ? 'visibility' : 'visibility_off'"></span>
                    </button>
                </div>
                @error('password')
                    <span class="text-[11px] text-red-500 font-bold pl-1 flex items-center gap-1"><span
                            class="material-symbols-outlined text-[12px]">error</span> {{ $message }}</span>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5 relative group" x-data="{ show: false }">
                <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Konfirmasi
                    Password Baru</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="password_confirmation"
                        placeholder="Ulangi sandi baru..."
                        class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-3 h-12 shadow-inner pr-12">

                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-[#3525cd] transition-colors focus:outline-none">
                        <span class="material-symbols-outlined text-[20px]"
                            x-text="show ? 'visibility' : 'visibility_off'"></span>
                    </button>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 flex gap-2.5 items-start mt-2">
                <span class="material-symbols-outlined text-blue-500 text-[18px] mt-0.5">info</span>
                <p class="text-[11px] text-blue-700 font-medium leading-relaxed">
                    Setelah sandi berhasil diubah, Anda bisa menggunakan sandi baru tersebut pada saat login berikutnya.
                </p>
            </div>

            <div class="mt-2">
                <button type="submit"
                    class="w-full h-[52px] bg-[#3525cd] hover:bg-[#2c1eb3] text-white text-[15px] font-bold rounded-[1.25rem] shadow-lg flex items-center justify-center gap-2 active:scale-95 transition-all">
                    <span wire:loading.remove wire:target="updatePassword">Simpan Password</span>
                    <div wire:loading.flex wire:target="updatePassword" class="items-center justify-center gap-2">
                        <span class="material-symbols-outlined animate-spin text-[20px]">refresh</span> Menyimpan...
                    </div>
                </button>
            </div>

        </form>
    </section>
</div>
