<div class="w-full flex flex-col -mt-4 pb-4" x-data="{
    isModalOpen: false,
    modalImageSrc: '',
    showRevisionModal: false,
    revJournalId: null,
    revNote: '',
    openRevision(id) {
        this.revJournalId = id;
        this.revNote = '';
        this.showRevisionModal = true;
    },
    submitRevision() {
        if (this.revNote.trim() === '') {
            alert('Catatan revisi tidak boleh kosong!');
            return;
        }
        $wire.requestRevision(this.revJournalId, this.revNote).then(() => {
            this.showRevisionModal = false;
        });
    }
}">

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)">
            <template x-teleport="body">
                <div x-show="show" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-4"
                    class="fixed top-20 left-1/2 -translate-x-1/2 w-[90%] max-w-[360px] z-[9999] bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xl">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        <span class="text-[12px] font-bold">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false"
                        class="text-emerald-500 hover:text-emerald-700 active:scale-95 transition-transform"><span
                            class="material-symbols-outlined text-[18px]">close</span></button>
                </div>
            </template>
        </div>
    @endif

    <div
        class="sticky top-[-16px] z-30 bg-slate-100/95 backdrop-blur-md -mx-4 px-4 pb-4 pt-3 border-b border-slate-200/60 shadow-sm">
        <h2 class="text-[20px] font-extrabold text-slate-800 leading-tight mb-1">Validasi Jurnal PKL</h2>
        <p class="text-[12px] font-medium text-slate-500 mb-3">Setujui atau kembalikan jurnal harian siswa.</p>

        <div class="relative mb-3">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span wire:loading.remove wire:target="search"
                    class="material-symbols-outlined text-slate-400 text-[20px]">search</span>
                <svg wire:loading wire:target="search" class="animate-spin w-5 h-5 text-[#3525cd]"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
            <input wire:model.live.debounce.1000ms="search" type="text"
                class="w-full pl-11 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-[13px] font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all shadow-sm"
                placeholder="Cari aktivitas jurnal...">
            @if (!empty($search))
                <button wire:click="$set('search', '')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-500 transition-colors active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-2 mb-2">
            <div class="relative h-[42px]">
                <select wire:model.live="filterSiswa"
                    class="w-full h-full bg-white border border-slate-200 text-slate-700 text-[12px] font-bold rounded-xl pl-3 pr-8 outline-none focus:border-[#3525cd] shadow-sm appearance-none">
                    <option value="Semua Siswa">Semua Siswa</option>
                    @if (isset($siswaList))
                        @foreach ($siswaList as $siswa)
                            <option value="{{ $siswa }}">{{ $siswa }}</option>
                        @endforeach
                    @endif
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">arrow_drop_down</span>
                </div>
            </div>

            <div class="relative h-[42px]">
                <select wire:model.live="filterStatus"
                    class="w-full h-full bg-white border border-slate-200 text-slate-700 text-[12px] font-bold rounded-xl pl-3 pr-8 outline-none focus:border-[#3525cd] shadow-sm appearance-none">
                    <option value="Semua Status">Semua Status</option>
                    <option value="Revisi">Direvisi</option>
                    <option value="Disetujui">Disetujui</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">arrow_drop_down</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 h-[42px]">
            <div class="relative w-full h-full">
                <input wire:model.live="startDate" type="date" title="Tanggal Mulai"
                    class="w-full h-full bg-white border border-slate-200 text-slate-700 text-[12px] font-bold rounded-xl px-3 outline-none focus:border-[#3525cd] shadow-sm">
            </div>
            <span class="text-[11px] font-extrabold text-slate-400">s/d</span>
            <div class="relative w-full h-full">
                <input wire:model.live="endDate" type="date" title="Tanggal Selesai"
                    class="w-full h-full bg-white border border-slate-200 text-slate-700 text-[12px] font-bold rounded-xl px-3 outline-none focus:border-[#3525cd] shadow-sm">
            </div>
        </div>
    </div>

    <div class="mt-4 flex flex-col gap-4 px-1" wire:loading.class="opacity-50 transition-opacity duration-200"
        wire:target="search, filterSiswa, filterStatus, startDate, endDate">

        @if (!$isFiltered)
            <div class="bg-white rounded-[1.25rem] p-8 text-center border border-slate-200 shadow-sm mt-4">
                <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-[32px] text-[#3525cd]">filter_alt</span>
                </div>
                <h3 class="text-[15px] font-extrabold text-slate-800 mb-1">Pilih Filter Data</h3>
                <p class="text-[12px] font-medium text-slate-500 leading-relaxed">Pilih status atau nama siswa untuk
                    mulai mengecek dan memvalidasi jurnal.</p>
            </div>
        @elseif(isset($journals) && $journals->isEmpty())
            <div class="bg-white rounded-[1.25rem] p-8 text-center border border-slate-200 shadow-sm mt-4">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-[32px] text-slate-400">find_in_page</span>
                </div>
                <h3 class="text-[15px] font-extrabold text-slate-800 mb-1">Tidak Ditemukan</h3>
                <p class="text-[12px] font-medium text-slate-500 leading-relaxed">Tidak ada jurnal siswa pada filter
                    tersebut.</p>
            </div>
        @elseif(isset($journals))
            @foreach ($journals as $journal)
                <div class="bg-white border border-slate-200 rounded-[1.25rem] overflow-hidden shadow-sm">

                    <div class="p-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                        <div
                            class="w-10 h-10 rounded-full overflow-hidden shrink-0 border border-slate-200 bg-[#e2dfff] flex items-center justify-center text-[#3525cd] font-bold shadow-inner">
                            @if ($journal['avatar'])
                                <img src="{{ $journal['avatar'] }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($journal['student_name'], 0, 1)) }}
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-[14px] font-extrabold text-slate-800 leading-tight">
                                {{ $journal['student_name'] }}</h3>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5 leading-tight">
                                {{ $journal['date_str'] }}</p>
                        </div>
                        <div
                            class="flex items-center gap-1 {{ $journal['attend_status'] == 'Hadir' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }} px-2.5 py-1 rounded-full border">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $journal['attend_status'] == 'Hadir' ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                            <span class="text-[10px] font-bold">{{ $journal['attend_status'] }}</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <p class="text-[13px] font-medium text-slate-700 leading-relaxed mb-3">
                            {{ $journal['content'] }}</p>

                        @if ($journal['attendance_photo'] || count($journal['images']) > 0)
                            <div
                                class="flex gap-2 overflow-x-auto pb-3 mb-1 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                                @if ($journal['attendance_photo'])
                                    <div @click="modalImageSrc = '{{ $journal['attendance_photo'] }}'; isModalOpen = true"
                                        class="relative w-28 h-28 shrink-0 rounded-[1rem] overflow-hidden bg-slate-100 border border-slate-200 cursor-pointer active:scale-95 transition-transform group">
                                        <img src="{{ $journal['attendance_photo'] }}"
                                            class="w-full h-full object-cover">
                                        <div class="absolute bottom-0 left-0 right-0 bg-black/50 backdrop-blur-sm p-1">
                                            <p
                                                class="text-white text-[8px] font-bold text-center uppercase tracking-wider">
                                                Selfie Absen</p>
                                        </div>
                                    </div>
                                @endif
                                @foreach ($journal['images'] as $img)
                                    <div @click="modalImageSrc = '{{ $img }}'; isModalOpen = true"
                                        class="relative w-36 h-28 shrink-0 rounded-[1rem] overflow-hidden bg-slate-100 border border-slate-200 cursor-pointer active:scale-95 transition-transform">
                                        <img src="{{ $img }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center gap-1.5 pt-3 border-t border-slate-100">
                            @if ($journal['status'] === 'Disetujui')
                                <span class="material-symbols-outlined text-emerald-500 text-[18px]">verified</span>
                                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wide">Sudah Anda
                                    Setujui</span>
                            @elseif($journal['status'] === 'Revisi')
                                <span class="material-symbols-outlined text-red-500 text-[18px]">error</span>
                                <span
                                    class="text-[11px] font-bold text-red-600 uppercase tracking-wide">Direvisi</span>
                            @else
                                <span
                                    class="material-symbols-outlined text-amber-500 text-[18px]">pending_actions</span>
                                <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wide">Menunggu
                                    Persetujuan Anda</span>
                            @endif
                        </div>

                        @if ($journal['status'] === 'Revisi')
                            <div class="mt-3 bg-red-50 p-3 rounded-xl border border-red-100">
                                <span class="text-[10px] font-extrabold text-red-500 uppercase tracking-widest">Catatan
                                    Revisi Anda:</span>
                                <p class="text-[12px] font-medium text-red-700 mt-1 leading-relaxed">
                                    {{ $journal['revision_note'] }}</p>
                            </div>
                        @endif

                        <div class="flex gap-2 mt-4 pt-4 border-t border-slate-50">

                            @if ($journal['status'] === 'Disetujui')
                                <button @click="openRevision({{ $journal['id'] }})"
                                    class="flex-1 py-3 bg-red-50 text-red-600 hover:bg-red-100 hover:border-red-200 text-[12px] font-bold rounded-xl border border-red-100 active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">edit_note</span> Batalkan &
                                    Minta Revisi
                                </button>
                            @else
                                <button @click="openRevision({{ $journal['id'] }})"
                                    class="flex-1 py-3 bg-red-50 text-red-600 hover:bg-red-100 hover:border-red-200 text-[12px] font-bold rounded-xl border border-red-100 active:scale-95 transition-all">
                                    {{ $journal['status'] === 'Revisi' ? 'Ubah Catatan Revisi' : 'Minta Revisi' }}
                                </button>

                                <button wire:click="approveJournal({{ $journal['id'] }})"
                                    wire:loading.attr="disabled"
                                    class="flex-1 py-3 bg-[#3525cd] hover:bg-[#2c1eb3] text-white text-[12px] font-bold rounded-xl shadow-md active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                    <span wire:loading.remove
                                        wire:target="approveJournal({{ $journal['id'] }})">Setujui Jurnal</span>
                                    <span wire:loading wire:target="approveJournal({{ $journal['id'] }})"
                                        class="material-symbols-outlined animate-spin text-[16px]">sync</span>
                                </button>
                            @endif

                        </div>

                    </div>
                </div>
            @endforeach

            @if (isset($journals) && $journals->hasPages())
                <div class="mt-4 mb-2 px-2">
                    {{ $journals->links() }}
                </div>
            @endif
        @endif
    </div>

    <template x-teleport="body">
        <div x-show="isModalOpen" style="display: none;" @click="isModalOpen = false"
            class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-sm flex items-center justify-center p-4 cursor-pointer"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <img :src="modalImageSrc" @click.stop
                class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl cursor-default">
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="showRevisionModal" style="display: none;"
            class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center sm:p-4"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="absolute inset-0 cursor-pointer" @click="showRevisionModal = false"></div>

            <div class="bg-white w-full sm:w-[390px] rounded-t-[2rem] sm:rounded-[2rem] p-6 shadow-2xl relative z-10 transform transition-transform"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-full sm:translate-y-4" x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full sm:translate-y-4">

                <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-5 sm:hidden"></div>

                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">error</span>
                    </div>
                    <div>
                        <h3 class="text-[18px] font-extrabold text-slate-800 leading-tight">Minta Revisi Jurnal</h3>
                        <p class="text-[11px] font-medium text-slate-500 mt-0.5">Tuliskan bagian mana yang perlu
                            diperbaiki.</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label
                        class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-widest mb-1.5 pl-1">Catatan
                        Revisi</label>
                    <textarea x-model="revNote" rows="4"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-[13px] font-medium rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all resize-none shadow-sm"
                        placeholder="Contoh: Tolong jelaskan lebih detail alat yang digunakan..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="showRevisionModal = false"
                        class="flex-1 py-3 bg-white border border-slate-200 text-slate-600 text-[13px] font-bold rounded-xl hover:bg-slate-50 active:scale-95 transition-all shadow-sm">Batal</button>
                    <button type="button" @click="submitRevision"
                        class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white text-[13px] font-bold rounded-xl shadow-sm active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">send</span> Kirim Revisi
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
