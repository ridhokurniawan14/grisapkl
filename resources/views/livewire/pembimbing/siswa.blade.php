<div class="w-full flex flex-col -mt-4 pb-0">

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
            <input wire:model.live.debounce.300ms="search" type="text"
                class="w-full pl-11 pr-11 py-3 bg-white border border-slate-200 rounded-2xl text-[14px] font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all shadow-sm"
                placeholder="Cari nama siswa...">

            {{-- Tombol X: muncul hanya saat ada isian --}}
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
            <div x-data="{ open: false }"
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

                        @if ($siswa['phone'])
                            <a href="https://wa.me/{{ $siswa['phone'] }}" target="_blank"
                                class="w-full h-[46px] bg-[#3525cd] hover:bg-[#2c1eb3] text-white text-[13px] font-bold rounded-[1rem] shadow-sm flex items-center justify-center gap-2 active:scale-95 transition-all">
                                <span class="material-symbols-outlined text-[18px]">chat</span> Pesan via WhatsApp
                            </a>
                        @else
                            <div
                                class="w-full h-[46px] bg-slate-50 border border-slate-200 rounded-[1rem] flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-slate-300 text-[18px]">phone_disabled</span>
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
