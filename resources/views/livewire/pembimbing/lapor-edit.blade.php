<div class="relative w-full pb-6 px-1">

    <div class="flex items-center gap-3 mb-5 mt-2">
        <a href="{{ route('pembimbing.lapor') }}" wire:navigate
            class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 text-slate-600 hover:bg-slate-100 active:scale-95 transition-all shrink-0">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex-1 overflow-hidden">
            <h2 class="text-[20px] font-extrabold text-slate-800 tracking-tight leading-tight truncate">Edit Monitoring
            </h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                {{ \Carbon\Carbon::parse($date)->isoFormat('D MMMM YYYY') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-5 flex flex-col gap-5">

        <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Instansi
                DUDIKA</label>
            <div class="relative">
                <select wire:model="dudika_id"
                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl pl-4 pr-10 py-3 outline-none focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] appearance-none transition-all">
                    <option value="">Pilih Instansi...</option>
                    @foreach ($dudikaList as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <span class="material-symbols-outlined text-slate-400">arrow_drop_down</span>
                </div>
            </div>
            @error('dudika_id')
                <span class="text-[11px] text-red-500 font-bold pl-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Foto
                Dokumentasi</label>

            @if (count($existingPhotos) > 0)
                <div class="grid grid-cols-3 gap-2 mb-2">
                    @foreach ($existingPhotos as $index => $path)
                        <div
                            class="relative aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-100 group">
                            <img src="{{ asset('storage/' . $path) }}" class="w-full h-full object-cover">
                            <button type="button" wire:click="removeExistingPhoto({{ $index }})"
                                class="absolute top-1 right-1 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center shadow-lg active:scale-90 transition-transform">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="flex items-center gap-2">
                <label
                    class="flex-1 flex flex-col items-center justify-center gap-1 border-2 border-dashed border-slate-300 bg-slate-50 rounded-xl py-4 text-slate-500 hover:border-[#3525cd] hover:text-[#3525cd] transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[28px]">add_a_photo</span>
                    <span class="text-[10px] font-bold">Tambah Foto Baru</span>
                    <input type="file" wire:model="newPhotos" multiple class="hidden" accept="image/*">
                </label>
            </div>

            @if ($newPhotos)
                <div class="mt-2 flex items-center gap-2 overflow-x-auto pb-1">
                    @foreach ($newPhotos as $photo)
                        <div class="w-12 h-12 rounded-lg border border-indigo-200 overflow-hidden shrink-0">
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                    <p class="text-[10px] font-bold text-indigo-600 animate-pulse">+ Menambahkan...</p>
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Catatan
                Pemantauan</label>
            <textarea wire:model="notes" rows="4"
                class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-medium text-[13px] rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] outline-none transition-all"
                placeholder="Tulis perkembangan siswa dan kondisi di lapangan..."></textarea>
            @error('notes')
                <span class="text-[11px] text-red-500 font-bold pl-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-2">
            <button type="button" wire:click="updateMonitoring" wire:loading.attr="disabled"
                class="w-full h-[54px] bg-[#3525cd] hover:bg-[#2c1eb3] text-white text-[15px] font-extrabold rounded-[1.25rem] shadow-lg shadow-indigo-200 flex items-center justify-center gap-2 active:scale-95 transition-all">
                <span wire:loading.remove wire:target="updateMonitoring">Simpan Perubahan</span>
                <div wire:loading wire:target="updateMonitoring" class="flex items-center gap-2">
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
        </div>

    </div>

    <div class="text-center mt-6">
        <p class="text-[11px] font-semibold text-slate-400 italic">*Pastikan data yang diubah sudah benar sebelum
            menekan tombol simpan.</p>
    </div>

</div>
