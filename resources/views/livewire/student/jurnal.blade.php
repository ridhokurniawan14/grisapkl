<div wire:poll.30s class="relative w-full pb-2 min-h-[calc(100vh-4rem)]" x-data="{
    showDetailModal: false,
    showPrintModal: false,
    selectedJournal: null,
    fullScreenImg: null,
    showIncompleteModal: false,
    openDetail(journal) {
        this.selectedJournal = journal;
        this.showDetailModal = true;
    }
}" x-init="showIncompleteModal = @js($incompleteCount > 0)">

    <section class="flex justify-between items-center mt-4 px-2">
        <div class="flex flex-col gap-1">
            <h2 class="text-[22px] font-extrabold text-slate-800 tracking-tight">Jurnal Kegiatan</h2>
            <p class="text-[13px] font-medium text-slate-500">Pantau aktivitas & kelola revisi.</p>
        </div>

        <button @click="showPrintModal = true"
            class="bg-[#3525cd] text-white px-3 py-2 rounded-[0.75rem] text-[12px] font-bold flex items-center gap-1 shadow-md hover:bg-indigo-700 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-[18px]">print</span> Cetak
        </button>
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

        <div class="flex gap-2 w-full mt-1">
            <div class="relative flex-1 group">
                <span class="absolute -top-2.5 left-3 bg-slate-50 px-1 text-[10px] font-bold text-slate-400 z-10">Mulai
                    Tanggal</span>
                <input wire:model.live="filterStartDate" type="date"
                    class="w-full bg-white border border-slate-200 rounded-[1rem] h-[46px] px-3 text-[13px] text-slate-700 focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all shadow-sm" />
            </div>

            <div class="relative flex-1 group">
                <span class="absolute -top-2.5 left-3 bg-slate-50 px-1 text-[10px] font-bold text-slate-400 z-10">Sampai
                    Tanggal</span>
                <input wire:model.live="filterEndDate" type="date"
                    class="w-full bg-white border border-slate-200 rounded-[1rem] h-[46px] px-3 text-[13px] text-slate-700 focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all shadow-sm" />
            </div>
        </div>
    </section>

    <section class="flex flex-col gap-3 mt-5 px-1">

        @if ($showOnlyIncomplete)
            <div
                class="flex items-center justify-between gap-2 bg-amber-50 border border-amber-200 rounded-2xl px-3.5 py-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500 text-[20px]">warning</span>
                    <p class="text-[12px] font-bold text-amber-700">Menampilkan jurnal yang foto kegiatannya belum
                        lengkap.</p>
                </div>
                <button wire:click="resetModeLengkap"
                    class="shrink-0 w-7 h-7 rounded-lg bg-white/70 flex items-center justify-center text-amber-600 hover:bg-white active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>
        @endif

        @if (
            !$showOnlyIncomplete &&
                empty($selectedMonth) &&
                empty($selectedStatus) &&
                empty($filterStartDate) &&
                empty($filterEndDate))
            <div class="flex flex-col items-center justify-center py-12 px-6 text-center opacity-80 mt-4">
                <div class="w-20 h-20 bg-slate-200 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[36px] text-slate-400">filter_alt</span>
                </div>
                <h3 class="text-[16px] font-bold text-slate-700">Tentukan Filter</h3>
                <p class="text-[13px] text-slate-500 mt-1">Silakan pilih bulan, status, atau rentang tanggal di atas
                    untuk menampilkan daftar jurnal kamu.</p>
            </div>
        @else
            @forelse($journals as $journal)
                @php
                    $isApproved = $journal->is_valid == true;
                    $isRejected = $journal->is_valid === 0 || $journal->is_valid === false;

                    // Cek kelengkapan foto sesuai status kehadiran
                    if ($journal->attend_status === 'Hadir') {
                        $isPhotoIncomplete = empty($journal->attendance_photo_path) || empty($journal->photo_path);
                    } elseif (in_array($journal->attend_status, ['Sakit', 'Izin'])) {
                        $isPhotoIncomplete = empty($journal->photo_path);
                    } else {
                        $isPhotoIncomplete = false;
                    }

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

                    @if ($isPhotoIncomplete)
                        <div
                            class="flex items-center gap-1.5 bg-amber-50 border border-amber-200 rounded-lg px-2.5 py-1.5 -mt-0.5">
                            <span class="material-symbols-outlined text-amber-500 text-[15px]">photo_camera</span>
                            <p class="text-[10px] font-bold text-amber-700">Foto kegiatan belum lengkap, segera upload.
                            </p>
                        </div>
                    @endif

                    @if ($journal->attendance_photo_path || $journal->photo_path)
                        <div
                            class="flex gap-2 overflow-x-auto mt-1 pb-2 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

                            @if ($journal->attendance_photo_path)
                                <div @click="fullScreenImg = '{{ asset('storage/' . $journal->attendance_photo_path) }}'"
                                    class="relative w-24 h-24 shrink-0 rounded-[0.75rem] overflow-hidden bg-slate-100 border border-slate-200 cursor-pointer active:scale-95 transition-transform group">
                                    <img src="{{ asset('storage/' . $journal->attendance_photo_path) }}"
                                        class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-black/50 backdrop-blur-sm p-1">
                                        <p class="text-white text-[8px] font-bold text-center uppercase tracking-wider">
                                            Selfie Absen</p>
                                    </div>
                                </div>
                            @endif

                            @if ($journal->photo_path)
                                <div @click="fullScreenImg = '{{ asset('storage/' . $journal->photo_path) }}'"
                                    class="relative w-32 h-24 shrink-0 rounded-[0.75rem] overflow-hidden bg-slate-100 border border-slate-200 cursor-pointer active:scale-95 transition-transform group">
                                    <img src="{{ asset('storage/' . $journal->photo_path) }}"
                                        class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-black/50 backdrop-blur-sm p-1">
                                        <p class="text-white text-[8px] font-bold text-center uppercase tracking-wider">
                                            Bukti Kegiatan</p>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endif

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
                    <p class="text-[13px] font-bold text-slate-500">Tidak ada jurnal yang sesuai dengan rentang tanggal
                        ini.</p>
                </div>
            @endforelse
        @endif
    </section>

    <div x-show="showDetailModal" x-cloak
        class="fixed inset-0 z-[10000] flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm sm:px-4">
        <div x-show="showDetailModal" @click.away="if(fullScreenImg === null) showDetailModal = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
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

    <div x-show="showIncompleteModal" x-cloak
        class="fixed inset-0 z-[10500] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">

        <div @click.away="showIncompleteModal = false" x-show="showIncompleteModal"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white w-full max-w-[350px] rounded-[1.5rem] p-5 shadow-2xl relative">

            <button @click="showIncompleteModal = false"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 active:scale-95 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>

            <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-amber-500 text-[28px]">photo_camera</span>
            </div>

            <h3 class="text-lg font-extrabold text-slate-800 mb-1">Foto Kegiatan Belum Lengkap</h3>
            <p class="text-[12.5px] text-slate-500 leading-relaxed mb-3">
                Ada <span class="font-bold text-amber-600">{{ $incompleteCount }} jurnal</span> kamu yang belum
                dilengkapi foto. Ingat ya: <span class="font-semibold text-slate-700">Hadir</span> wajib foto selfie
                absen &amp; foto kegiatan, sedangkan <span class="font-semibold text-slate-700">Sakit/Izin</span>
                cukup foto kegiatan saja.
            </p>

            @if (!empty($incompleteDates))
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 mb-4 max-h-[140px] overflow-y-auto">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tanggal yang perlu
                        dilengkapi</p>
                    <ul class="flex flex-col gap-1.5">
                        @foreach ($incompleteDates as $item)
                            <li class="flex items-center justify-between text-[11.5px]">
                                <span class="font-semibold text-slate-600">{{ $item['date'] }}</span>
                                <span class="text-slate-400">{{ $item['status'] }}</span>
                            </li>
                        @endforeach
                        @if ($incompleteCount > count($incompleteDates))
                            <li class="text-[10.5px] text-slate-400 italic pt-0.5">
                                +{{ $incompleteCount - count($incompleteDates) }} jurnal lainnya...</li>
                        @endif
                    </ul>
                </div>
            @endif

            <div class="flex gap-2">
                <button @click="showIncompleteModal = false"
                    class="flex-1 h-[46px] rounded-xl bg-slate-100 text-slate-600 font-bold text-[13px] hover:bg-slate-200 active:scale-95 transition-all">
                    Nanti Saja
                </button>
                <button wire:click="tampilkanBelumLengkap" @click="showIncompleteModal = false"
                    class="flex-1 h-[46px] rounded-xl bg-[#3525cd] text-white font-bold text-[13px] flex items-center justify-center gap-1.5 hover:bg-indigo-700 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Perbaiki
                </button>
            </div>
        </div>
    </div>

    <div x-show="showPrintModal" x-cloak
        class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">

        <div @click.away="showPrintModal = false" x-show="showPrintModal"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white w-full max-w-[350px] rounded-[1.5rem] p-5 shadow-2xl relative" x-data="{
                startDate: '{{ \Carbon\Carbon::now()->subMonth()->format('Y-m-d') }}',
                endDate: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}',
                errorMsg: '',
                checkDate(e) {
                    this.errorMsg = '';
            
                    if (!this.startDate || !this.endDate) {
                        this.errorMsg = 'Tanggal awal dan akhir wajib diisi.';
                        e.preventDefault();
                        return;
                    }
            
                    let d1 = new Date(this.startDate);
                    let d2 = new Date(this.endDate);
            
                    if (d2 < d1) {
                        this.errorMsg = 'Tanggal akhir harus lebih besar atau sama dengan tanggal awal.';
                        e.preventDefault();
                        return;
                    }
            
                    let diffTime = Math.abs(d2 - d1);
                    let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
                    if (diffDays > 31) {
                        this.errorMsg = 'Rentang waktu maksimal 31 hari bro, biar server nggak lemot.';
                        e.preventDefault();
                        return;
                    }
            
                    setTimeout(() => { showPrintModal = false; }, 500);
                }
            }">

            <button @click="showPrintModal = false"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 active:scale-95 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>

            <div class="flex items-center gap-2 mb-4 text-[#3525cd]">
                <span class="material-symbols-outlined text-[24px]">print</span>
                <h3 class="text-lg font-extrabold text-slate-800">Cetak Laporan</h3>
            </div>

            <p class="text-[12px] text-slate-500 mb-4">Pilih rentang tanggal jurnal yang ingin dicetak (Maksimal 1
                Bulan).</p>

            <div class="flex flex-col gap-3">
                <div>
                    <label class="text-[11px] font-bold text-slate-500 mb-1 block">Dari Tanggal</label>
                    <input type="date" x-model="startDate"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl h-[42px] px-3 text-[13px] text-slate-700 outline-none focus:border-[#3525cd] focus:ring-1 focus:ring-[#3525cd] transition-all">
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-500 mb-1 block">Sampai Tanggal</label>
                    <input type="date" x-model="endDate"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl h-[42px] px-3 text-[13px] text-slate-700 outline-none focus:border-[#3525cd] focus:ring-1 focus:ring-[#3525cd] transition-all">
                </div>

                <template x-if="errorMsg">
                    <div
                        class="bg-red-50 text-red-600 p-2 rounded-lg border border-red-100 flex items-start gap-1 mt-1">
                        <span class="material-symbols-outlined text-[16px]">error</span>
                        <p class="text-[11px] font-medium leading-tight" x-text="errorMsg"></p>
                    </div>
                </template>
            </div>

            <a :href="`{{ route('siswa.jurnal.cetak') }}?start=${startDate}&end=${endDate}`" target="_blank"
                @click="checkDate($event)"
                class="w-full bg-[#3525cd] text-white font-bold text-[13px] rounded-xl h-[46px] mt-5 flex items-center justify-center gap-2 hover:bg-indigo-700 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Download PDF</span>
            </a>
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
