<div class="relative w-full pb-6 px-1">

    <div class="flex items-center gap-3 mb-5 mt-2">
        <a href="{{ route('dudika.profil') }}" wire:navigate
            class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 text-slate-600 hover:bg-slate-100 active:scale-95 transition-all shrink-0">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex-1 overflow-hidden">
            <h2 class="text-[20px] font-extrabold text-slate-800 tracking-tight leading-tight truncate">Lengkapi Profil
            </h2>
        </div>
    </div>

    <form wire:submit.prevent="saveProfile" class="flex flex-col gap-3">

        <div x-data="{ open: {{ $errors->has('name') || $errors->has('address') ? 'true' : 'true' }} }"
            class="bg-white rounded-[1.5rem] shadow-sm border border-slate-200 overflow-hidden transition-all duration-300">

            <button type="button" @click="open = !open"
                class="w-full px-5 py-4 flex items-center justify-between bg-slate-50 hover:bg-slate-100 transition-colors focus:outline-none">
                <div class="flex items-center gap-2 text-[#3525cd]">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">domain</span>
                    <span class="text-[12px] font-extrabold uppercase tracking-widest">Informasi Instansi</span>
                </div>
                <span class="material-symbols-outlined text-slate-400 transition-transform duration-300"
                    :class="open ? 'rotate-180' : ''">expand_more</span>
            </button>

            <div x-show="open" x-collapse>
                <div class="p-5 pt-4 flex flex-col gap-4 border-t border-slate-100">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Nama
                            Instansi</label>
                        <input type="text" wire:model="name" placeholder="Contoh: CV. Faris Jaya Teknik"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] outline-none transition-all">
                        @error('name')
                            <span class="text-[11px] text-red-500 font-bold pl-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Alamat
                            Lengkap</label>
                        <textarea wire:model="address" rows="3" placeholder="Masukkan alamat lengkap instansi..."
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-medium text-[13px] rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] outline-none resize-none transition-all"></textarea>
                        @error('address')
                            <span class="text-[11px] text-red-500 font-bold pl-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{ open: {{ $errors->has('head_name') || $errors->has('head_nip') ? 'true' : 'false' }} }"
            class="bg-white rounded-[1.5rem] shadow-sm border border-slate-200 overflow-hidden transition-all duration-300">

            <button type="button" @click="open = !open"
                class="w-full px-5 py-4 flex items-center justify-between bg-slate-50 hover:bg-slate-100 transition-colors focus:outline-none">
                <div class="flex items-center gap-2 text-emerald-600">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">manage_accounts</span>
                    <span class="text-[12px] font-extrabold uppercase tracking-widest">Pimpinan / Direktur</span>
                </div>
                <span class="material-symbols-outlined text-slate-400 transition-transform duration-300"
                    :class="open ? 'rotate-180' : ''">expand_more</span>
            </button>

            <div x-show="open" x-collapse>
                <div class="p-5 pt-4 flex flex-col gap-4 border-t border-slate-100">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Nama
                            Lengkap</label>
                        <input type="text" wire:model="head_name" placeholder="Nama pimpinan instansi"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        @error('head_name')
                            <span class="text-[11px] text-red-500 font-bold pl-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">NIP /
                            NIK <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <input type="text" wire:model="head_nip" placeholder="Masukkan NIP/NIK jika ada"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{ open: {{ $errors->has('supervisor_name') || $errors->has('supervisor_phone') ? 'true' : 'false' }} }"
            class="bg-white rounded-[1.5rem] shadow-sm border border-slate-200 overflow-hidden transition-all duration-300">

            <button type="button" @click="open = !open"
                class="w-full px-5 py-4 flex items-center justify-between bg-slate-50 hover:bg-slate-100 transition-colors focus:outline-none">
                <div class="flex items-center gap-2 text-amber-500">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">support_agent</span>
                    <span class="text-[12px] font-extrabold uppercase tracking-widest">Pembimbing Lapangan</span>
                </div>
                <span class="material-symbols-outlined text-slate-400 transition-transform duration-300"
                    :class="open ? 'rotate-180' : ''">expand_more</span>
            </button>

            <div x-show="open" x-collapse>
                <div class="p-5 pt-4 flex flex-col gap-4 border-t border-slate-100">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Nama
                            Lengkap</label>
                        <input type="text" wire:model="supervisor_name" placeholder="Nama pembimbing magang siswa"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                        @error('supervisor_name')
                            <span class="text-[11px] text-red-500 font-bold pl-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">NIP /
                            NIK <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <input type="text" wire:model="supervisor_nip" placeholder="Masukkan NIP/NIK jika ada"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Nomor
                            WhatsApp</label>
                        <input type="text" wire:model="supervisor_phone" placeholder="Contoh: 08123456789"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                        @error('supervisor_phone')
                            <span class="text-[11px] text-red-500 font-bold pl-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-6">
            <button type="submit"
                class="w-full h-[54px] bg-[#3525cd] hover:bg-[#2c1eb3] text-white text-[15px] font-extrabold rounded-[1.25rem] shadow-lg shadow-indigo-200 flex items-center justify-center gap-2 active:scale-95 transition-all">
                <span wire:loading.remove wire:target="saveProfile">Simpan Perubahan</span>
                <span wire:loading wire:target="saveProfile" class="animate-pulse">Menyimpan Data...</span>
            </button>
        </div>

    </form>
</div>
