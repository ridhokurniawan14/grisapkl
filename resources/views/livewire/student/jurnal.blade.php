<div wire:poll.30s class="relative w-full pb-2 min-h-[calc(100vh-4rem)]" x-data="{
    showDetailModal: false,
    selectedJournal: null,
    fullScreenImg: null,
    openDetail(journal) {
        this.selectedJournal = journal;
        this.showDetailModal = true;
    }
}">

    <section class="flex flex-col gap-1 mt-4 px-2">
        <h2 class="text-[22px] font-extrabold text-slate-800 tracking-tight">Jurnal Kegiatan</h2>
        <p class="text-[13px] font-medium text-slate-500">Pantau aktivitas & kelola revisi prakerinmu.</p>
    </section>

    <section class="grid grid-cols-2 gap-3 mt-4 px-1">
        <div
            class="bg-gradient-to-br from-[#3525cd] to-[#2a1b9e] rounded-[1.25rem] p-4 flex flex-col justify-between shadow-md relative overflow-hidden h-[100px]">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <span class="material-symbols-outlined text-indigo-200 text-[20px]"
                style="font-variation-settings: 'FILL' 1;">book</span>
            <div>
                <p class="text-[10px] text-indigo-200 uppercase tracking-widest font-bold mb-0.5">Total Jurnal</p>
                <p class="text-[22px] font-extrabold text-white leading-none">{{ $totalJurnal }}</p>
            </div>
        </div>

        <div
            class="bg-red-50 rounded-[1.25rem] p-4 flex flex-col justify-between shadow-sm border border-red-100 h-[100px]">
            <span class="material-symbols-outlined text-red-500 text-[20px]">error</span>
            <div>
                <p class="text-[10px] text-red-400 uppercase tracking-widest font-bold mb-0.5">Total Revisi</p>
                <p class="text-[22px] font-extrabold text-red-600 leading-none">{{ $totalRevisi }}</p>
            </div>
        </div>
    </section>

    <section class="flex flex-col gap-2 mt-5 px-1 relative z-20">
        <div class="flex gap-2 w-full">
            <div class="relative flex-1 group">
                <select wire:model.live="selectedMonth"
                    class="w-full bg-white border border-slate-200 rounded-[1rem] h-[46px] pl-4 pr-10 text-[13px] font-bold text-slate-700 appearance-none !bg-none focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all shadow-sm cursor-pointer hover:bg-slate-50">
                    <option value="">Pilih Bulan...</option>
                    @foreach ($months as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <span
                        class="material-symbols-outlined text-slate-400 text-[20px] group-hover:text-[#3525cd] transition-colors">expand_more</span>
                </div>
            </div>

            <div class="relative flex-1 group">
                <select wire:model.live="selectedStatus"
                    class="w-full bg-white border border-slate-200 rounded-[1rem] h-[46px] pl-4 pr-10 text-[13px] font-bold text-slate-700 appearance-none !bg-none focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all shadow-sm cursor-pointer hover:bg-slate-50">
                    <option value="">Semua Status...</option>
                    <option value="Hadir">Hadir</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Libur">Libur</option>
                    <option value="Revisi">Perlu Revisi</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <span
                        class="material-symbols-outlined text-slate-400 text-[18px] group-hover:text-[#3525cd] transition-colors">filter_list</span>
                </div>
            </div>
        </div>

        @if (!empty($selectedMonth) || !empty($selectedStatus))
            <div class="relative w-full animate-fade-in-up mt-1 group">
                <span
                    class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] group-focus-within:text-[#3525cd] transition-colors">search</span>
                <input wire:model.live.debounce.500ms="search"
                    class="w-full bg-white border border-slate-200 rounded-[1rem] h-[46px] pl-10 pr-4 text-[13px] text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all shadow-sm"
                    placeholder="Cari kegiatan jurnal..." type="text" />
            </div>
        @endif
    </section>

    <section class="flex flex-col gap-3 mt-5 px-1">

        @if (empty($selectedMonth) && empty($selectedStatus) && empty($search))
            <div class="flex flex-col items-center justify-center py-12 px-6 text-center opacity-80 mt-4">
                <div class="w-20 h-20 bg-slate-200 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[36px] text-slate-400">filter_alt</span>
                </div>
                <h3 class="text-[16px] font-bold text-slate-700">Tentukan Filter</h3>
                <p class="text-[13px] text-slate-500 mt-1">Silakan pilih bulan atau status di atas untuk menampilkan
                    daftar jurnal kamu.</p>
            </div>
        @else
            @forelse($journals as $journal)
                @php
                    $isApproved = $journal->is_valid == true;
                    $isRejected = $journal->is_valid === 0 || $journal->is_valid === false;

                    if ($journal->attend_status == 'Libur') {
                        $bgColorClass = 'bg-blue-50 text-blue-700';
                        $iconClass = 'event_available';
                    } elseif ($isApproved) {
                        $bgColorClass = 'bg-green-50 text-green-700';
                        $iconClass = 'check_circle';
                    } elseif ($isRejected) {
                        $bgColorClass = 'bg-red-50 text-red-700';
                        $iconClass = 'error';
                    } else {
                        $bgColorClass = 'bg-amber-50 text-amber-700';
                        $iconClass = 'schedule';
                    }
                @endphp

                <article
                    class="bg-white rounded-2xl p-3.5 shadow-sm border border-slate-100 flex flex-col gap-2 relative group hover:border-[#3525cd]/30 transition-colors">

                    <header class="flex justify-between items-center">
                        <div class="flex items-center gap-1.5 text-slate-500">
                            <span class="material-symbols-outlined text-[16px]">calendar_clock</span>
                            <span
                                class="text-[11px] font-bold">{{ \Carbon\Carbon::parse($journal->date)->isoFormat('D MMM YYYY') }}
                                • {{ \Carbon\Carbon::parse($journal->time)->format('H:i') }}</span>
                        </div>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-md {{ $bgColorClass }} font-bold text-[10px]">
                            <span class="material-symbols-outlined text-[12px] mr-1">{{ $iconClass }}</span>
                            {{ $isApproved ? 'Disetujui' : ($isRejected ? 'Revisi' : ($journal->attend_status == 'Libur' ? 'Libur' : 'Menunggu')) }}
                        </span>
                    </header>

                    <div>
                        <h3 class="text-[14px] font-bold text-slate-800 line-clamp-1">{{ $journal->attend_status }}</h3>
                        <p class="text-[12px] text-slate-500 leading-snug line-clamp-2 mt-0.5">
                            {{ $journal->activity ?: 'Belum ada catatan kegiatan.' }}</p>
                    </div>

                    @if ($isRejected)
                        <div class="bg-red-50 border border-red-100 rounded-lg p-2 mt-1">
                            <p class="text-[10px] text-red-600 leading-snug"><span class="font-bold">Revisi:</span>
                                Silakan lengkapi atau perbaiki jurnal ini sesuai arahan pembimbing.</p>
                        </div>
                    @endif

                    <footer class="flex justify-end gap-2 mt-1 border-t border-slate-50 pt-2">
                        @if ($journal->is_editable)
                            <a href="{{ route('siswa.jurnal.edit', $journal->id) }}" wire:navigate
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#3525cd] hover:bg-indigo-50 active:scale-95 transition-all">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                        @endif

                        <button @click="openDetail({{ $journal->toJson() }})"
                            class="h-8 px-3 rounded-lg flex items-center gap-1.5 bg-slate-50 text-slate-600 hover:bg-[#3525cd] hover:text-white active:scale-95 transition-all text-[11px] font-bold">
                            <span class="material-symbols-outlined text-[16px]">visibility</span> Detail
                        </button>
                    </footer>
                </article>
            @empty
                <div class="flex flex-col items-center justify-center py-10 opacity-60">
                    <span class="material-symbols-outlined text-[48px] text-slate-400 mb-3">search_off</span>
                    <p class="text-[13px] font-bold text-slate-500">Tidak ada jurnal yang sesuai.</p>
                </div>
            @endforelse
        @endif
    </section>

    <div x-show="showDetailModal" x-cloak
        class="fixed inset-0 z-[10000] flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm sm:px-4">
        <div x-show="showDetailModal" @click.away="if(fullScreenImg === null) showDetailModal = false"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-full"
            class="bg-white w-full max-w-[400px] sm:rounded-[2rem] rounded-t-[2rem] p-6 pb-8 flex flex-col shadow-2xl relative max-h-[90vh] overflow-y-auto">

            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-4 sm:hidden"></div>

            <template x-if="selectedJournal">
                <div class="flex flex-col gap-3">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800" x-text="selectedJournal.attend_status"></h3>
                            <p class="text-[12px] font-bold text-slate-400"
                                x-text="selectedJournal.formatted_date + ' • ' + selectedJournal.formatted_time"></p>
                        </div>
                        <button @click="showDetailModal = false"
                            class="p-1.5 bg-slate-100 rounded-full text-slate-500 active:scale-95"><span
                                class="material-symbols-outlined text-[18px]">close</span></button>
                    </div>

                    <template x-if="selectedJournal.revision_note">
                        <div class="bg-red-50 p-3 rounded-xl border border-red-100 mb-1">
                            <div class="flex items-center gap-1.5 mb-1 text-red-600">
                                <span class="material-symbols-outlined text-[16px]">error</span>
                                <span class="text-[11px] font-extrabold uppercase tracking-widest">Catatan Revisi
                                    DUDIKA</span>
                            </div>
                            <p class="text-[12px] font-medium text-red-700 leading-relaxed"
                                x-text="selectedJournal.revision_note"></p>
                        </div>
                    </template>

                    <template x-if="selectedJournal.attendance_photo_url">
                        <div class="mb-1">
                            <p class="text-[11px] font-bold text-slate-400 mb-1">Foto Lokasi</p>
                            <div @click.stop="fullScreenImg = selectedJournal.attendance_photo_url"
                                class="w-full h-32 rounded-xl overflow-hidden border border-slate-100 bg-slate-50 cursor-pointer active:scale-95 transition-transform relative group">
                                <img :src="selectedJournal.attendance_photo_url" class="w-full h-full object-cover">
                                <div
                                    class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span
                                        class="material-symbols-outlined text-white drop-shadow-md text-[32px]">zoom_in</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="selectedJournal.latitude && selectedJournal.longitude">
                        <div class="bg-slate-50 p-2.5 rounded-xl flex items-center gap-2 border border-slate-100">
                            <span class="material-symbols-outlined text-[#3525cd] text-[18px]">location_on</span>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400">Koordinat</p>
                                <p class="text-[11px] font-medium text-slate-700"
                                    x-text="selectedJournal.latitude + ', ' + selectedJournal.longitude"></p>
                            </div>
                        </div>
                    </template>

                    <template x-if="selectedJournal.activity_photo_url">
                        <div class="mb-1">
                            <p class="text-[11px] font-bold text-slate-400 mb-1">Bukti Kegiatan</p>
                            <div @click.stop="fullScreenImg = selectedJournal.activity_photo_url"
                                class="w-full h-32 rounded-xl overflow-hidden border border-slate-100 bg-slate-50 cursor-pointer active:scale-95 transition-transform relative group">
                                <img :src="selectedJournal.activity_photo_url" class="w-full h-full object-cover">
                                <div
                                    class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span
                                        class="material-symbols-outlined text-white drop-shadow-md text-[32px]">zoom_in</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div>
                        <p class="text-[11px] font-bold text-slate-400 mb-1">Deskripsi Kegiatan</p>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 min-h-[60px]">
                            <p class="text-[12px] text-slate-700 leading-relaxed"
                                x-text="selectedJournal.activity || 'Tidak ada deskripsi.'"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div x-show="fullScreenImg !== null" x-cloak
        class="fixed inset-0 z-[11000] flex items-center justify-center bg-black/90 backdrop-blur-md px-4"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

        <button @click="fullScreenImg = null"
            class="absolute top-6 right-6 w-11 h-11 bg-white/20 rounded-full flex items-center justify-center text-white hover:bg-white/40 active:scale-95 transition-all shadow-lg border border-white/30 z-50">
            <span class="material-symbols-outlined text-[24px]">close</span>
        </button>

        <img :src="fullScreenImg" @click.away="fullScreenImg = null"
            class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl">
    </div>

</div>
