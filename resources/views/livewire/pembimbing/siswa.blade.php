<div class="w-full flex flex-col -mt-4 pb-0" wire:poll.3s>

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
        class="sticky top-[-16px] z-30 bg-slate-100/95 backdrop-blur-md -mx-4 px-4 pb-3 pt-4 border-b border-slate-200/60 shadow-sm">

        <div class="flex items-center gap-3 mb-3">
            <h2 class="text-[20px] font-extrabold text-slate-800 leading-tight">Siswa Bimbingan</h2>
            <div
                class="bg-[#e2dfff] text-[#3525cd] px-2.5 py-0.5 rounded-full text-[11px] font-extrabold shadow-sm border border-white flex items-center justify-center">
                {{ $totalSiswa }} Siswa
            </div>
        </div>

        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-slate-400 text-[20px]">search</span>
            </div>
            <input wire:model.live.debounce.500ms="search" type="text"
                class="w-full pl-11 pr-11 py-3 bg-white border border-slate-200 rounded-2xl text-[14px] font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all shadow-sm"
                placeholder="Cari nama siswa...">

            @if ($search)
                <button wire:click="$set('search', '')"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            @endif
        </div>
    </div>

    <div
        class="mt-4 flex gap-2 overflow-x-auto -mx-4 px-4 pb-2 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
        <button wire:click="$set('filterDudika', 'Semua Instansi')"
            class="shrink-0 px-4 py-2 rounded-full text-[12px] font-bold transition-all shadow-sm {{ $filterDudika === 'Semua Instansi' ? 'bg-[#3525cd] text-white border border-[#3525cd]' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Semua Instansi
        </button>

        @foreach ($dudikaList as $dudika)
            <button wire:click="$set('filterDudika', '{{ $dudika }}')"
                class="shrink-0 px-4 py-2 rounded-full text-[12px] font-bold transition-all shadow-sm {{ $filterDudika === $dudika ? 'bg-[#3525cd] text-white border border-[#3525cd]' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                {{ $dudika }}
            </button>
        @endforeach
    </div>

    <div class="mt-2 flex flex-col gap-3 pb-4">
        @forelse($siswaList as $siswa)
            <div x-data="{ open: false }" wire:key="siswa-{{ $siswa['id'] }}"
                class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm transition-all duration-300">

                <button @click="open = !open"
                    class="w-full p-4 flex items-center justify-between text-left focus:outline-none hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-[1rem] bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0 overflow-hidden shadow-inner">
                            @if ($siswa['avatar'])
                                <img src="{{ $siswa['avatar'] }}" class="w-full h-full object-cover">
                            @else
                                <span
                                    class="text-[#3525cd] font-extrabold text-[16px]">{{ strtoupper(substr($siswa['name'], 0, 1)) }}</span>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-[15px] font-extrabold text-slate-800 leading-tight">{{ $siswa['name'] }}
                            </h3>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate max-w-[180px]">
                                {{ $siswa['dudika_name'] }}
                                @if ($siswa['pkl_field'])
                                    • {{ $siswa['pkl_field'] }}
                                @else
                                    • <span class="text-red-500 italic font-bold">Bidang keahlian belum diisi</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div
                        class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100">
                        <span class="material-symbols-outlined text-slate-400 transition-transform duration-300"
                            :class="open ? 'rotate-180' : ''">expand_more</span>
                    </div>
                </button>

                <div x-show="open" x-collapse>
                    <div class="p-4 pt-0 border-t border-slate-50 mt-2">

                        <div class="grid grid-cols-2 gap-3 mb-4 mt-4">
                            <div
                                class="bg-[#f5f2ff] rounded-xl p-3 border border-indigo-50 flex flex-col justify-center">
                                <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest mb-1.5">
                                    Kehadiran</p>
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="w-2 h-2 rounded-full {{ $siswa['attendance_percent'] >= 80 ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    <span
                                        class="text-[14px] font-bold text-slate-800">{{ $siswa['attendance_percent'] }}%
                                        Hadir</span>
                                </div>
                            </div>

                            <div
                                class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex flex-col justify-center">
                                <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest mb-1.5">
                                    Status Jurnal</p>
                                <div class="text-[14px] font-bold text-slate-800">
                                    {{ $siswa['log_count'] }} / {{ $siswa['log_total'] }} Logs
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 pb-4 border-b border-slate-100 flex flex-col gap-2">

                            @if (!$siswa['is_complete'])
                                <div
                                    class="w-full bg-red-50 text-red-600 text-[11px] font-medium p-3 rounded-[1rem] flex items-start gap-2 border border-red-100">
                                    <span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5">warning</span>
                                    <div class="flex-1">
                                        <span class="font-extrabold block mb-1">Validasi Terkunci! Data laporan belum
                                            lengkap:</span>
                                        <ul class="list-disc pl-4 space-y-0.5">
                                            @foreach ($siswa['missing_fields'] as $missing)
                                                <li>{{ $missing }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @elseif(!$siswa['is_all_journals_approved'])
                                <div
                                    class="w-full bg-amber-50 text-amber-700 text-[11px] font-bold p-3 rounded-[1rem] flex items-start gap-2 border border-amber-100">
                                    <span
                                        class="material-symbols-outlined text-[16px] shrink-0 mt-0.5">pending_actions</span>
                                    <span>Validasi terkunci! Masih ada jurnal siswa yang berstatus Menunggu atau Revisi
                                        dari DUDIKA.</span>
                                </div>
                            @else
                                @if (!$siswa['is_validated'])
                                    <button wire:click="validasiLaporan({{ $siswa['placement_id'] }})"
                                        wire:loading.attr="disabled"
                                        class="w-full h-[46px] bg-emerald-500 hover:bg-emerald-600 text-white text-[13px] font-bold rounded-[1rem] shadow-sm flex items-center justify-center gap-2 active:scale-95 transition-all">
                                        <span class="material-symbols-outlined text-[18px]">check_circle</span> Validasi
                                        Laporan
                                    </button>
                                @else
                                    <div class="grid grid-cols-2 gap-2">
                                        <button wire:click="batalValidasi({{ $siswa['placement_id'] }})"
                                            wire:loading.attr="disabled"
                                            class="w-full h-[46px] bg-red-50 hover:bg-red-100 text-red-600 text-[12px] font-bold rounded-[1rem] border border-red-200 active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                            <span class="material-symbols-outlined text-[18px]">cancel</span> Batal
                                            Validasi
                                        </button>

                                        @if ($siswa['file_laporan_path'] === 'processing')
                                            <button disabled
                                                class="w-full h-[46px] bg-slate-100 text-slate-500 text-[12px] font-bold rounded-[1rem] border border-slate-200 flex items-center justify-center gap-1.5 cursor-not-allowed">
                                                <span
                                                    class="material-symbols-outlined text-[18px] animate-spin">sync</span>
                                                Diproses...
                                            </button>
                                        @elseif($siswa['file_laporan_path'])
                                            <button wire:click="downloadLaporan({{ $siswa['placement_id'] }})"
                                                wire:loading.attr="disabled"
                                                class="w-full h-[46px] bg-blue-50 hover:bg-blue-100 text-blue-600 text-[12px] font-bold rounded-[1rem] border border-blue-200 active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                                <span class="material-symbols-outlined text-[18px]">download</span>
                                                Download PDF
                                            </button>
                                        @else
                                            <button wire:click="generateLaporan({{ $siswa['placement_id'] }})"
                                                wire:loading.attr="disabled"
                                                class="w-full h-[46px] bg-amber-500 hover:bg-amber-600 text-white text-[12px] font-bold rounded-[1rem] shadow-sm active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                                <span
                                                    class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                                                Generate PDF
                                            </button>
                                        @endif
                                    </div>

                                    @if ($siswa['file_laporan_path'] && $siswa['file_laporan_path'] !== 'processing')
                                        <button wire:click="generateLaporan({{ $siswa['placement_id'] }})"
                                            wire:loading.attr="disabled"
                                            class="w-full h-[40px] mt-2 bg-amber-50 hover:bg-amber-100 text-amber-600 text-[12px] font-bold rounded-[1rem] border border-amber-200 active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                            <span class="material-symbols-outlined text-[16px]">sync</span> Generate
                                            Ulang PDF
                                        </button>
                                    @endif
                                @endif
                            @endif
                        </div>

                        @if ($siswa['phone'])
                            <a href="https://wa.me/{{ $siswa['phone'] }}" target="_blank"
                                class="w-full h-[46px] bg-[#3525cd] hover:bg-[#2c1eb3] text-white text-[13px] font-bold rounded-[1rem] shadow-sm flex items-center justify-center gap-2 active:scale-95 transition-all">
                                <span class="material-symbols-outlined text-[18px]">chat</span> Pesan via WhatsApp
                            </a>
                        @else
                            <div
                                class="w-full h-[46px] bg-slate-50 border border-slate-200 rounded-[1rem] flex items-center justify-center gap-2">
                                <span
                                    class="material-symbols-outlined text-slate-300 text-[18px]">phone_disabled</span>
                                <span class="text-[13px] font-medium text-slate-400">No. HP belum terdaftar</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 mt-4">
                <span class="material-symbols-outlined text-[48px] text-slate-300 mb-2">group_off</span>
                <p class="text-[14px] font-bold text-slate-700">Tidak ada siswa ditemukan</p>
                <p class="text-[12px] text-slate-500 mt-1">Coba ubah kata kunci atau filter instansi.</p>
            </div>
        @endforelse
    </div>

</div>
