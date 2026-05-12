<div class="w-full flex flex-col -mt-4 pb-4 px-1">

    {{-- Header Navigasi --}}
    <div
        class="flex items-center gap-3 mb-6 sticky top-[-16px] bg-slate-100 -mx-5 px-5 pt-3 pb-3 z-30 border-b border-slate-200">
        <a href="{{ route('pembimbing.lapor') }}" wire:navigate
            class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 text-slate-600 hover:bg-slate-100 active:scale-95 transition-all shrink-0">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex-1 overflow-hidden">
            <h2 class="text-[18px] font-extrabold text-slate-800 tracking-tight leading-tight truncate">Edit Laporan
                Monitoring</h2>
            <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">Unggah bukti kunjungan di instansi DUDIKA.
            </p>
        </div>
    </div>

    {{-- form (Menggunakan gap-5 agar jarak otomatis rapi) --}}
    <form wire:submit.prevent="updateMonitoring" class="flex flex-col gap-5">

        {{-- Dropdown Instansi Kustom Cantik & Searchable --}}
        <section x-data="{
            open: false,
            search: '',
            dudikas: @js($dudikaList),
            selectedId: @entangle('dudika_id'),
            get selectedName() {
                if (!this.selectedId) return '';
                return this.dudikas[this.selectedId] || '';
            },
            get filteredDudikas() {
                if (this.search === '') return this.dudikas;
                let results = {};
                for (let id in this.dudikas) {
                    if (this.dudikas[id].toLowerCase().includes(this.search.toLowerCase())) {
                        results[id] = this.dudikas[id];
                    }
                }
                return results;
            },
            selectDudika(id) {
                this.selectedId = id;
                this.search = '';
                this.open = false;
                $wire.set('dudika_id', id);
            }
        }"
            class="bg-white p-4 rounded-[1.25rem] shadow-sm border border-slate-200 relative">
            <div class="flex items-center gap-2 mb-4">
                <div
                    class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 shadow-inner">
                    <span class="material-symbols-outlined text-[20px]"
                        style="font-variation-settings: 'FILL' 1;">domain</span>
                </div>
                <div>
                    <h3 class="text-[16px] font-extrabold text-slate-800 leading-tight">Pilih Instansi DUDIKA</h3>
                    <p class="text-[11px] font-medium text-slate-500 mt-0.5">Silakan pilih instansi yang Anda kunjungi.
                    </p>
                </div>
            </div>

            <div class="relative">
                <input type="text" x-model="selectedName" @click="open = !open"
                    @click.away="open = false; search = ''"
                    class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl text-[13px] font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-inner cursor-pointer"
                    placeholder="Cari atau pilih instansi..." readonly>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">arrow_drop_down</span>
                </div>

                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-[100] mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-y-auto custom-scrollbar"
                    style="display: none;">

                    <div class="sticky top-0 p-3 bg-white border-b border-slate-100 z-10">
                        <input type="text" x-model="search" @click.stop
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-[12px] focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500"
                            placeholder="Ketik untuk mencari...">
                    </div>

                    <div class="p-1 relative z-0">
                        <template x-for="(name, id) in filteredDudikas" :key="id">
                            <button @click="selectDudika(id)" type="button"
                                class="w-full text-left px-3 py-2.5 rounded-lg text-[12px] font-medium hover:bg-emerald-50 hover:text-emerald-700 transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-emerald-500"
                                    x-show="selectedId == id">check_circle</span>
                                <span x-text="name"></span>
                            </button>
                        </template>
                        <div x-show="Object.keys(filteredDudikas).length === 0"
                            class="text-center py-4 text-[12px] text-slate-400 font-medium">Instansi tidak ditemukan.
                        </div>
                    </div>
                </div>
            </div>
            @error('dudika_id')
                <span class="text-[11px] text-red-500 font-bold pl-2 mt-1 block">{{ $message }}</span>
            @enderror
        </section>

        {{-- Tanggal & Catatan --}}
        <section class="bg-white p-4 rounded-[1.25rem] shadow-sm border border-slate-200">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Tanggal
                        Kunjungan</label>
                    <input type="date" wire:model="date" readonly
                        class="w-full bg-slate-100 border border-slate-200 text-slate-600 font-bold text-[13px] rounded-xl px-4 py-3 outline-none cursor-not-allowed shadow-inner">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Hasil
                        Monitoring / Catatan</label>
                    <textarea wire:model="notes" rows="6" placeholder="Tuliskan hasil monitoring Bapak/Ibu guru secara detail..."
                        class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-medium text-[13px] rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] outline-none resize-none transition-all placeholder:text-slate-400 shadow-inner"></textarea>
                    @error('notes')
                        <span class="text-[11px] text-red-500 font-bold pl-2">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Container Foto Tunggal --}}
        <section class="bg-white p-4 rounded-[1.25rem] shadow-sm border border-slate-200 overflow-hidden relative">
            <div class="flex items-center gap-2 mb-4 px-1">
                <div
                    class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 shadow-inner">
                    <span class="material-symbols-outlined text-[20px]"
                        style="font-variation-settings: 'FILL' 1;">add_photo_alternate</span>
                </div>
                <div>
                    <h3 class="text-[16px] font-extrabold text-slate-800 leading-tight">Bukti Foto Monitoring</h3>
                    <p class="text-[11px] font-medium text-slate-500 mt-0.5">Unggah 1 foto bukti kunjungan.</p>
                </div>
            </div>

            <input type="file" id="inputFotoTunggal" wire:model="newPhoto" accept="image/*" class="hidden" />

            <div
                class="border-2 border-dashed border-slate-200 rounded-2xl p-3 flex flex-col items-center justify-center min-h-[160px] bg-slate-50/50 hover:border-[#3525cd]/30 transition-colors">

                @if ($newPhoto)
                    <div
                        class="relative w-full h-[180px] rounded-xl overflow-hidden shadow-md border border-slate-200 mb-3">
                        <img src="{{ $newPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                    </div>
                @elseif($existingPhoto)
                    <div
                        class="relative w-full h-[180px] rounded-xl overflow-hidden shadow-md border border-slate-200 mb-3">
                        <img src="{{ asset('storage/' . $existingPhoto) }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div
                        class="w-20 h-20 bg-amber-50 text-amber-500 flex items-center justify-center rounded-full border border-amber-100 shadow-inner mb-3">
                        <span class="material-symbols-outlined text-[40px]">photo_library</span>
                    </div>
                @endif

                @if ($newPhoto || $existingPhoto)
                    <button type="button" onclick="document.getElementById('inputFotoTunggal').click()"
                        class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 text-[12px] font-bold rounded-xl shadow-sm hover:bg-slate-50 hover:border-[#3525cd]/30 active:scale-95 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">edit_note</span> Ganti Foto
                    </button>
                @else
                    <button type="button" onclick="document.getElementById('inputFotoTunggal').click()"
                        class="px-5 py-2.5 bg-[#3525cd] text-white text-[12px] font-bold rounded-xl shadow-md hover:bg-[#2c1eb3] active:scale-95 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">add_photo_alternate</span> Upload Foto
                    </button>
                @endif

                <div wire:loading wire:target="newPhoto"
                    class="mt-3 text-[11px] font-bold text-[#3525cd] animate-pulse pb-1 flex items-center gap-1.5">
                    <span class="material-symbols-outlined animate-spin text-[16px]">sync</span> Mengunggah...
                </div>
            </div>
            @error('newPhoto')
                <span class="text-[11px] text-red-500 font-bold pl-2 mt-1 block">{{ $message }}</span>
            @enderror
        </section>

        {{-- Tombol Simpan --}}
        <div class="mb-6">
            <button type="submit"
                class="w-full h-[54px] bg-[#3525cd] hover:bg-[#2c1eb3] text-white text-[15px] font-extrabold rounded-[1.25rem] shadow-lg shadow-indigo-200 flex items-center justify-center gap-2 active:scale-95 transition-all disabled:opacity-50"
                wire:loading.attr="disabled" wire:target="newPhoto">
                <span wire:loading.remove wire:target="updateMonitoring">Simpan Perubahan Laporan</span>
                <span wire:loading wire:target="updateMonitoring" class="animate-pulse flex items-center gap-2">
                    <span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Menyimpan Data...
                </span>
            </button>
        </div>

    </form>
</div>
