<div wire:poll.30s class="w-full flex flex-col" x-data="{
    showReportForm: false,
    showDetailModal: false,
    showMonthFilter: false,
    detailData: {},
    fullScreenImg: null,
    openDetail(item) {
        this.detailData = item;
        this.showDetailModal = true;
    }
}"
    x-on:open-report-form.window="showReportForm = true" x-on:close-report-form.window="showReportForm = false">

    {{-- ══ JADWAL & CIRCLE BUTTON ══════════════════════════════════════════ --}}
    <div class="flex flex-col items-center pt-2 pb-4">

        <p class="text-[10px] font-extrabold text-slate-400 tracking-[0.15em] uppercase mb-2">
            {{ $scheduleName }}
        </p>

        <span class="bg-[#3525cd] text-white text-[12px] font-bold px-5 py-1.5 rounded-full shadow-sm shadow-indigo-200">
            {{ $scheduleDateStr }}
        </span>

        <div class="mt-7 mb-2">
            @if ($isActiveWindow && $remaining > 0)
                <button wire:click="openReportForm" wire:loading.attr="disabled"
                    class="w-36 h-36 rounded-full bg-[#3525cd] flex flex-col items-center justify-center shadow-2xl shadow-indigo-300/60 active:scale-95 transition-all animate-subtle-pulse disabled:opacity-70">
                    <span class="material-symbols-outlined text-white text-[40px]"
                        style="font-variation-settings: 'FILL' 1;">add_location_alt</span>
                    <span
                        class="text-white font-extrabold text-[14px] mt-1.5 text-center leading-tight tracking-tight">Lapor<br>Monitoring</span>
                </button>
            @else
                <div class="w-36 h-36 rounded-full bg-slate-200 flex flex-col items-center justify-center">
                    <span class="material-symbols-outlined text-slate-400 text-[40px]"
                        style="font-variation-settings: 'FILL' 1;">add_location_alt</span>
                    <span
                        class="text-slate-500 font-extrabold text-[14px] mt-1.5 text-center leading-tight tracking-tight">Lapor<br>Monitoring</span>
                </div>
            @endif
        </div>

        @if (!$isActiveWindow)
            <div class="flex items-center gap-1.5 mt-3 bg-red-50 border border-red-100 rounded-full px-4 py-2">
                <span class="material-symbols-outlined text-red-500 text-[15px]">error</span>
                <span class="text-red-500 text-[11px] font-bold">Tombol terkunci. Di luar rentang jadwal aktif.</span>
            </div>
        @elseif ($totalDudika == 0)
            <div class="flex items-center gap-1.5 mt-3 bg-amber-50 border border-amber-100 rounded-full px-4 py-2">
                <span class="material-symbols-outlined text-amber-500 text-[15px]">warning</span>
                <span class="text-amber-600 text-[11px] font-bold">Belum ada siswa/instansi bimbingan.</span>
            </div>
        @elseif ($remaining <= 0)
            <div class="flex items-center gap-1.5 mt-3 bg-emerald-50 border border-emerald-100 rounded-full px-4 py-2">
                <span class="material-symbols-outlined text-emerald-500 text-[15px]">task_alt</span>
                <span class="text-emerald-600 text-[11px] font-bold">Semua instansi telah selesai dikunjungi.</span>
            </div>
        @endif
    </div>

    {{-- ══ STATS ════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-3 mb-5">
        <div class="bg-white rounded-2xl px-4 py-4 shadow-sm border border-slate-100">
            <p class="text-[9px] font-extrabold text-slate-400 tracking-[0.12em] uppercase mb-1">Sudah Dikunjungi</p>
            <p class="text-[32px] font-extrabold text-[#3525cd] leading-none">{{ $visited }}</p>
            <p class="text-[11px] text-slate-400 font-semibold mt-0.5">Instansi</p>
        </div>
        <div class="bg-white rounded-2xl px-4 py-4 shadow-sm border border-slate-100">
            <p class="text-[9px] font-extrabold text-slate-400 tracking-[0.12em] uppercase mb-1">Belum Dikunjungi</p>
            <p class="text-[32px] font-extrabold text-orange-500 leading-none">{{ $remaining }}</p>
            <p class="text-[11px] text-slate-400 font-semibold mt-0.5">Instansi</p>
        </div>
    </div>

    {{-- ══ RIWAYAT MONITORING ══════════════════════════════════════════════ --}}
    <div class="flex flex-col">

        {{-- Header + Filter --}}
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-[16px] font-extrabold text-slate-800">Riwayat Monitoring</h3>
            @if ($availableMonths->isNotEmpty())
                <button @click="showMonthFilter = !showMonthFilter"
                    class="flex items-center gap-1 text-[12px] font-bold text-[#3525cd]">
                    <span>{{ \Carbon\Carbon::parse($filterMonth . '-01')->isoFormat('MMM YYYY') }}</span>
                    <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                </button>
            @endif
        </div>

        {{-- Pills bulan --}}
        @if ($availableMonths->isNotEmpty())
            <div x-show="showMonthFilter" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="flex gap-2 overflow-x-auto pb-2 mb-3 scrollbar-none -mx-4 px-4" style="display: none;">
                @foreach ($availableMonths as $m)
                    <button wire:click="setFilterMonth('{{ $m['value'] }}')" @click="showMonthFilter = false"
                        class="flex-shrink-0 text-[11px] font-bold px-4 py-1.5 rounded-full border transition-all
                            {{ $filterMonth === $m['value']
                                ? 'bg-[#3525cd] text-white border-[#3525cd]'
                                : 'bg-white text-slate-500 border-slate-200' }}">
                        {{ $m['label'] }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Cards --}}
        @if ($history->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined text-slate-300 text-[28px]">assignment</span>
                </div>
                <p class="text-slate-500 font-bold text-[13px]">Belum ada riwayat</p>
                <p class="text-slate-400 text-[11px] mt-0.5">
                    @if ($availableMonths->isEmpty())
                        Belum ada data monitoring.
                    @else
                        Tidak ada data di bulan ini.
                    @endif
                </p>
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($history as $item)
                    <div
                        class="bg-white rounded-2xl px-4 py-3.5 shadow-sm border border-slate-100 flex items-center gap-3">
                        <button @click="openDetail({{ json_encode($item) }})"
                            class="flex-shrink-0 w-12 h-12 bg-indigo-50 rounded-xl flex flex-col items-center justify-center border border-indigo-100/80 active:scale-95 transition-all">
                            <span
                                class="text-[#3525cd] font-extrabold text-[17px] leading-none">{{ $item['date_num'] }}</span>
                            <span
                                class="text-[#3525cd]/50 font-bold text-[9px] uppercase tracking-wider">{{ $item['date_month'] }}</span>
                        </button>

                        <button @click="openDetail({{ json_encode($item) }})"
                            class="flex-1 min-w-0 text-left active:opacity-70 transition-opacity">
                            <p class="text-slate-800 font-extrabold text-[13px] truncate leading-tight">
                                {{ $item['dudika_name'] }}</p>
                            <p class="text-slate-400 text-[11px] font-semibold mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px]">group</span>
                                {{ $item['students_covered'] }} Siswa Tercakup
                            </p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span
                                    class="bg-emerald-50 text-emerald-600 border border-emerald-200 text-[9px] font-extrabold px-2 py-0.5 rounded-full tracking-wider uppercase">Selesai</span>
                                @if ($item['photos_count'] > 0)
                                    <span class="flex items-center gap-1 text-slate-400 text-[10px] font-semibold">
                                        <span class="material-symbols-outlined text-[12px]">photo_camera</span>
                                        {{ $item['photos_count'] }} Foto
                                    </span>
                                @endif
                            </div>
                        </button>

                        <a href="{{ route('pembimbing.lapor.edit', ['monitoring_id' => $item['id']]) }}"
                            class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:bg-indigo-50 hover:text-[#3525cd] hover:border-indigo-200 active:scale-90 transition-all"
                            title="Edit" @click.stop>
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ══ MODAL: FORM LAPOR ════════════════════════════════════════════════ --}}
    <div x-show="showReportForm" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-full" class="fixed inset-0 z-[60] flex flex-col justify-end"
        style="display: none;">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showReportForm = false"></div>

        <div class="relative bg-white rounded-t-3xl shadow-2xl z-10 flex flex-col max-h-[90dvh]">

            <div class="flex-shrink-0 flex flex-col items-center pt-3 pb-4 px-5 border-b border-slate-100">
                <div class="w-10 h-1 bg-slate-200 rounded-full mb-4"></div>
                <div class="flex items-center justify-between w-full">
                    <div>
                        <h3 class="text-[16px] font-extrabold text-slate-800">Lapor Monitoring</h3>
                        <p class="text-[#3525cd] text-[11px] font-semibold">{{ now()->isoFormat('dddd, D MMMM YYYY') }}
                        </p>
                    </div>
                    <button @click="showReportForm = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="submitMonitoring" class="flex flex-col flex-1 overflow-hidden">
                <div class="overflow-y-auto flex-1 px-5 py-4 flex flex-col gap-4">
                    <div>
                        <label
                            class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">
                            Pilih DUDIKA / Instansi
                        </label>
                        <div class="relative">
                            <select wire:model="selectedDudikaId"
                                class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-800 font-semibold text-[13px] rounded-xl px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-[#3525cd]/30 focus:border-[#3525cd] transition-all">
                                <option value="">-- Pilih Instansi --</option>
                                @forelse ($dudikasForTeacher as $dudika)
                                    <option value="{{ $dudika->id }}">{{ $dudika->name }}</option>
                                @empty
                                    <option disabled>Tidak ada DUDIKA terdaftar</option>
                                @endforelse
                            </select>
                            <span
                                class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
                        </div>
                        @error('selectedDudikaId')
                            <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror

                        @if ($selectedDudikaId)
                            @php
                                $jumlahSiswa = \App\Models\PklPlacement::where(
                                    'teacher_id',
                                    \App\Models\Teacher::where('user_id', auth()->id())->value('id'),
                                )
                                    ->where('dudika_id', $selectedDudikaId)
                                    ->where('status', 'Aktif')
                                    ->count();
                            @endphp
                            <p class="text-[11px] text-[#3525cd] font-semibold mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">info</span>
                                Kunjungan mencakup {{ $jumlahSiswa }} siswa.
                            </p>
                        @endif
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Catatan
                            Pemantauan</label>
                        <textarea wire:model="monitoringNotes" rows="3" placeholder="Deskripsikan hasil pemantauan..."
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-medium text-[13px] rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3525cd]/30 focus:border-[#3525cd] transition-all resize-none"></textarea>
                        @error('monitoringNotes')
                            <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">
                            Foto Dokumentasi <span class="normal-case font-normal">(opsional)</span>
                        </label>

                        @if ($monitoringPhoto)
                            <div class="relative rounded-xl overflow-hidden border border-slate-200">
                                <img src="{{ $monitoringPhoto->temporaryUrl() }}" class="w-full h-36 object-cover">
                                <button type="button" wire:click="$set('monitoringPhoto', null)"
                                    class="absolute top-2 right-2 w-7 h-7 flex items-center justify-center bg-red-500 text-white rounded-full shadow">
                                    <span class="material-symbols-outlined text-[14px]">close</span>
                                </button>
                            </div>
                        @else
                            <label
                                class="flex flex-col items-center justify-center gap-1.5 border-2 border-dashed border-slate-200 bg-slate-50 rounded-xl py-5 cursor-pointer hover:border-[#3525cd] hover:bg-indigo-50/40 transition-all group">
                                <span
                                    class="material-symbols-outlined text-[30px] text-slate-300 group-hover:text-[#3525cd] transition-colors">add_a_photo</span>
                                <span
                                    class="text-[12px] font-bold text-slate-500 group-hover:text-[#3525cd] transition-colors">Tambah
                                    Foto</span>
                                <span class="text-[10px] text-slate-400">Pilih dari kamera atau galeri</span>
                                <input type="file" wire:model="monitoringPhoto" accept="image/*" class="hidden">
                            </label>
                        @endif

                        <div wire:loading wire:target="monitoringPhoto"
                            class="flex items-center gap-1.5 mt-1.5 text-[11px] text-slate-400 font-medium">
                            <svg class="animate-spin w-3 h-3" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                            </svg>
                            Mengupload foto...
                        </div>
                        @error('monitoringPhoto')
                            <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex-shrink-0 px-5 pt-3 pb-6 border-t border-slate-100 bg-white">
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full bg-[#3525cd] text-white font-extrabold text-[14px] py-4 rounded-2xl shadow-lg shadow-indigo-200 active:scale-95 transition-all disabled:opacity-60 flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="submitMonitoring"
                            class="material-symbols-outlined text-[18px]">check_circle</span>
                        <span wire:loading.remove wire:target="submitMonitoring">Simpan Laporan</span>
                        <svg wire:loading wire:target="submitMonitoring" class="animate-spin w-4 h-4"
                            viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                        </svg>
                        <span wire:loading wire:target="submitMonitoring">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══ MODAL: DETAIL RIWAYAT (z-[60]) ══════════════════════════════════ --}}
    <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4" class="fixed inset-0 z-[60] flex flex-col justify-end"
        style="display: none;">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDetailModal = false"></div>

        <div class="relative bg-white rounded-t-3xl shadow-2xl z-10 p-5 pb-8">
            <div class="w-10 h-1 bg-slate-200 rounded-full mx-auto mb-4"></div>

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[15px] font-extrabold text-slate-800">Detail Monitoring</h3>
                <div class="flex items-center gap-2">
                    <a :href="'{{ route('pembimbing.lapor.edit') }}?monitoring_id=' + detailData.id"
                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-indigo-50 border border-indigo-200 text-[#3525cd]"
                        title="Edit">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </a>
                    <button @click="showDetailModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>
            </div>

            <div class="flex flex-col gap-2.5">
                <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl">
                    <span class="material-symbols-outlined text-[#3525cd] text-[18px] mt-0.5">business</span>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Instansi</p>
                        <p class="text-slate-800 font-bold text-[13px]" x-text="detailData.dudika_name"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="flex items-start gap-2 p-3 bg-slate-50 rounded-xl">
                        <span class="material-symbols-outlined text-[#3525cd] text-[18px] mt-0.5">calendar_today</span>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tanggal</p>
                            <p class="text-slate-800 font-bold text-[12px]" x-text="detailData.date_full"></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2 p-3 bg-slate-50 rounded-xl">
                        <span class="material-symbols-outlined text-[#3525cd] text-[18px] mt-0.5">schedule</span>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Waktu</p>
                            <p class="text-slate-800 font-bold text-[12px]" x-text="detailData.time"></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="flex items-center gap-2 p-3 bg-indigo-50 rounded-xl">
                        <span class="material-symbols-outlined text-[#3525cd] text-[18px]">group</span>
                        <div>
                            <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-wider">Siswa</p>
                            <p class="text-[#3525cd] font-extrabold text-[16px]" x-text="detailData.students_covered">
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 p-3 bg-indigo-50 rounded-xl">
                        <span class="material-symbols-outlined text-[#3525cd] text-[18px]">photo_camera</span>
                        <div>
                            <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-wider">Foto</p>
                            <p class="text-[#3525cd] font-extrabold text-[16px]" x-text="detailData.photos_count"></p>
                        </div>
                    </div>
                </div>

                <template x-if="detailData.photo_url">
                    <div class="rounded-xl overflow-hidden border border-slate-200 mt-1 cursor-pointer relative group"
                        @click="fullScreenImg = detailData.photo_url">
                        <img :src="detailData.photo_url" class="w-full h-36 object-cover">
                        <div
                            class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span
                                class="material-symbols-outlined text-white drop-shadow-md text-[32px]">zoom_in</span>
                        </div>
                    </div>
                </template>

                <div class="p-3 bg-slate-50 rounded-xl mt-1">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Catatan</p>
                    <p class="text-slate-700 text-[12px] font-medium leading-snug" x-text="detailData.notes"></p>
                </div>
            </div>
        </div>
    </div>
    {{-- ══ MODAL ZOOM GAMBAR FULLSCREEN ══════════════════════════════════ --}}
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
