{{-- Background diubah jadi bg-slate-100 agar lebih abu-abu. State openPribadi diubah jadi true --}}
<div class="pb-10 -mx-4 px-4 bg-slate-100 min-h-[calc(100vh-4rem)] pt-2" x-data="{ openPribadi: true, openOrtu: false }">

    <!-- Header Halaman -->
    <div class="flex items-center gap-3 mb-6 pt-2">
        <a href="{{ route('siswa.profil') }}" wire:navigate
            class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 text-slate-600 hover:bg-slate-100 active:scale-95 transition-all">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h2 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Edit Profil</h2>
    </div>

    <form wire:submit="save" class="flex flex-col gap-4">

        <!-- ========================================== -->
        <!-- SECTION: DATA PRIBADI (COLLAPSE)           -->
        <!-- ========================================== -->
        <section
            class="bg-white rounded-[1.5rem] shadow-sm border border-slate-200 overflow-hidden transition-all duration-300">
            <!-- Header Collapse -->
            <button type="button" @click="openPribadi = !openPribadi"
                class="w-full px-5 py-4 flex items-center justify-between bg-white hover:bg-slate-50 active:bg-slate-100 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-[#3525cd]">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                    </div>
                    <h3 class="font-bold text-slate-800 text-[15px]">Data Pribadi</h3>
                </div>
                <!-- Ikon Panah Putar -->
                <span class="material-symbols-outlined text-slate-400 transition-transform duration-300"
                    :class="openPribadi ? 'rotate-180' : ''">expand_more</span>
            </button>

            <!-- Isi Collapse -->
            <div x-show="openPribadi" x-collapse x-cloak>
                <div class="px-5 pb-5 pt-2 flex flex-col gap-4 border-t border-slate-100 mt-1">

                    <!-- Input Tempat Lahir -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-semibold text-slate-500">Tempat Lahir</label>
                        <input type="text" wire:model="birth_place"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-2.5 h-12"
                            placeholder="Contoh: Banyuwangi">
                    </div>

                    <!-- Input Tanggal Lahir -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-semibold text-slate-500">Tanggal Lahir</label>
                        <input type="date" wire:model="birth_date"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-2.5 h-12">
                    </div>

                    <!-- Pilihan Agama -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-semibold text-slate-500">Agama</label>
                        <select wire:model="religion"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-2.5 h-12">
                            <option value="">Pilih Agama...</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>

                    <!-- Input WhatsApp -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-semibold text-slate-500">No. WhatsApp</label>
                        <input type="tel" wire:model="phone"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-2.5 h-12"
                            placeholder="Contoh: 08123456789">
                    </div>

                    <!-- Input Alamat -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-semibold text-slate-500">Alamat Lengkap</label>
                        <textarea wire:model="address"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-3 resize-none h-24"
                            placeholder="Masukkan alamat lengkap..."></textarea>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- SECTION: DATA ORANG TUA (COLLAPSE)         -->
        <!-- ========================================== -->
        <section
            class="bg-white rounded-[1.5rem] shadow-sm border border-slate-200 overflow-hidden transition-all duration-300">
            <!-- Header Collapse -->
            <button type="button" @click="openOrtu = !openOrtu"
                class="w-full px-5 py-4 flex items-center justify-between bg-white hover:bg-slate-50 active:bg-slate-100 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-[#e65100]">
                        <span class="material-symbols-outlined text-[18px]">family_restroom</span>
                    </div>
                    <h3 class="font-bold text-slate-800 text-[15px]">Data Orang Tua</h3>
                </div>
                <!-- Ikon Panah Putar -->
                <span class="material-symbols-outlined text-slate-400 transition-transform duration-300"
                    :class="openOrtu ? 'rotate-180' : ''">expand_more</span>
            </button>

            <!-- Isi Collapse -->
            <div x-show="openOrtu" x-collapse x-cloak>
                <div class="px-5 pb-5 pt-2 flex flex-col gap-4 border-t border-slate-100 mt-1">

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-[12px] font-semibold text-slate-500">Nama Ayah</label>
                            <input type="text" wire:model="father_name"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-2.5 h-12"
                                placeholder="Nama Ayah">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[12px] font-semibold text-slate-500">Pekerjaan</label>
                            <input type="text" wire:model="father_job"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-2.5 h-12"
                                placeholder="Pekerjaan Ayah">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-[12px] font-semibold text-slate-500">Nama Ibu</label>
                            <input type="text" wire:model="mother_name"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-2.5 h-12"
                                placeholder="Nama Ibu">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[12px] font-semibold text-slate-500">Pekerjaan</label>
                            <input type="text" wire:model="mother_job"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-2.5 h-12"
                                placeholder="Pekerjaan Ibu">
                        </div>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-semibold text-slate-500">No. WhatsApp Ortu</label>
                        <input type="tel" wire:model="parent_phone"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-2.5 h-12"
                            placeholder="Contoh: 08123456789">
                    </div>

                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <label class="text-[12px] font-semibold text-slate-500">Alamat Ortu</label>

                            <!-- CHECKBOX ALAMAT SAMA -->
                            <label class="flex items-center gap-1.5 cursor-pointer group">
                                <input type="checkbox"
                                    @change="$event.target.checked ? $wire.parent_address = $wire.address : $wire.parent_address = ''"
                                    class="w-3.5 h-3.5 text-[#3525cd] bg-white border-slate-300 rounded focus:ring-[#3525cd] focus:ring-2 transition-all cursor-pointer">
                                <span
                                    class="text-[11px] font-semibold text-slate-500 group-hover:text-slate-700 transition-colors">Sama
                                    dengan siswa</span>
                            </label>
                        </div>

                        <textarea wire:model="parent_address"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all px-4 py-3 resize-none h-24"
                            placeholder="Masukkan alamat orang tua..."></textarea>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tombol Simpan -->
        <button type="submit"
            class="w-full h-[52px] bg-[#3525cd] hover:bg-[#2c1eb3] text-white text-[15px] font-bold rounded-[1.25rem] shadow-md flex items-center justify-center gap-2 active:scale-95 transition-all mt-2">
            <span wire:loading.remove wire:target="save">Simpan Perubahan</span>

            <div wire:loading.flex wire:target="save" class="items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span>Menyimpan...</span>
            </div>
        </button>

    </form>
</div>
