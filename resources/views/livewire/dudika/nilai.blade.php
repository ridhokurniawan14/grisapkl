<div class="w-full flex flex-col -mt-4 pb-4">

    <div
        class="sticky top-[-16px] z-30 bg-slate-100/95 backdrop-blur-md -mx-4 px-4 pb-4 pt-3 border-b border-slate-200/60 shadow-sm">
        <h2 class="text-[20px] font-extrabold text-[#3525cd] leading-tight mb-1">Penilaian Siswa</h2>
        <p class="text-[12px] font-medium text-slate-500 mb-3">Kelola evaluasi kinerja siswa magang di instansi Anda.</p>

        <div class="relative">
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
            <input wire:model.live.debounce.500ms="search" type="text"
                class="w-full pl-11 pr-10 py-3 bg-white border border-slate-200 rounded-2xl text-[13px] font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all shadow-sm"
                placeholder="Cari nama siswa atau jurusan...">
            @if (!empty($search))
                <button wire:click="$set('search', '')"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-red-500 transition-colors active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            @endif
        </div>
    </div>

    <div class="mt-4 flex flex-col gap-4 px-1" wire:loading.class="opacity-50 transition-opacity duration-200"
        wire:target="search">
        @forelse ($students as $siswa)
            <div
                class="bg-white p-4 rounded-[1.25rem] shadow-sm border border-slate-200 flex flex-col gap-4 relative overflow-hidden transition-all hover:shadow-md">

                @if ($siswa['status'] === 'Sudah Dinilai' && $siswa['average_score'] >= 85)
                    <div class="absolute top-0 right-0 w-24 h-24 overflow-hidden pointer-events-none">
                        <div
                            class="absolute top-0 right-0 translate-x-8 translate-y-[-10px] rotate-45 bg-indigo-50 w-full text-center py-1 border-b border-indigo-100">
                            <span class="text-[8px] font-extrabold text-[#3525cd] uppercase tracking-widest">Sangat
                                Baik</span>
                        </div>
                    </div>
                @endif

                <div class="flex items-center gap-3">
                    <div
                        class="w-14 h-14 rounded-full bg-indigo-50 border-2 border-indigo-100 flex items-center justify-center shrink-0 overflow-hidden text-[#3525cd] font-bold text-[20px] shadow-inner">
                        @if ($siswa['avatar'])
                            <img src="{{ $siswa['avatar'] }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($siswa['name'], 0, 1)) }}
                        @endif
                    </div>

                    <div class="flex-1 pr-6">
                        <h3 class="text-[16px] font-extrabold text-slate-800 leading-tight">{{ $siswa['name'] }}</h3>
                        <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">{{ $siswa['field'] }}</p>

                        <div class="mt-1.5 inline-block">
                            @if ($siswa['status'] === 'Belum Dinilai')
                                <span
                                    class="px-2 py-0.5 rounded border border-red-200 bg-red-50 text-[9px] font-bold text-red-600 uppercase tracking-wider">Belum
                                    Dinilai</span>
                            @else
                                <span
                                    class="px-2 py-0.5 rounded border border-emerald-200 bg-emerald-50 text-[9px] font-bold text-emerald-600 uppercase tracking-wider">Sudah
                                    Dinilai</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($siswa['status'] === 'Sudah Dinilai')
                    <div class="flex items-center justify-between bg-slate-50 border border-slate-100 p-3 rounded-xl">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Rata-rata
                                Nilai</span>
                            <span
                                class="text-[20px] font-black text-[#3525cd] leading-none mt-1">{{ $siswa['average_score'] }}</span>
                        </div>
                        <div class="flex -space-x-1">
                            @php
                                // Logika sederhana mengubah skor (0-100) menjadi 5 bintang
                                $stars = round($siswa['average_score'] / 20);
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                <span
                                    class="material-symbols-outlined text-[22px] {{ $i <= $stars ? 'text-amber-400' : 'text-slate-200' }}"
                                    style="font-variation-settings: 'FILL' {{ $i <= $stars ? '1' : '0' }};">star</span>
                            @endfor
                        </div>
                    </div>
                @endif

                <button
                    class="w-full h-[44px] rounded-xl flex items-center justify-center gap-2 font-bold text-[13px] active:scale-95 transition-transform {{ $siswa['status'] === 'Belum Dinilai' ? 'bg-[#3525cd] hover:bg-[#2c1eb3] text-white shadow-md' : 'bg-white border-2 border-[#3525cd] text-[#3525cd] hover:bg-indigo-50' }}">
                    @if ($siswa['status'] === 'Belum Dinilai')
                        <span class="material-symbols-outlined text-[18px]">edit_note</span> Beri Nilai
                    @else
                        <span class="material-symbols-outlined text-[18px]">visibility</span> Lihat / Edit Nilai
                    @endif
                </button>

            </div>
        @empty
            <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 mt-2 shadow-sm">
                <span class="material-symbols-outlined text-[48px] text-slate-300 mb-2">person_search</span>
                <p class="text-[14px] font-bold text-slate-700">Siswa tidak ditemukan</p>
                <p class="text-[12px] text-slate-500 mt-1">Coba gunakan kata kunci lain.</p>
            </div>
        @endforelse
    </div>

    <div
        class="mt-6 mx-1 bg-gradient-to-br from-[#4f46e5] to-[#3525cd] rounded-[1.5rem] p-5 text-white shadow-[0_10px_30px_rgba(53,37,205,0.3)] relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/20 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute top-0 right-10 w-20 h-20 bg-indigo-400/30 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10">
            <p class="text-[10px] font-extrabold text-indigo-200 uppercase tracking-widest mb-1.5">Status Keseluruhan
            </p>
            <div class="flex items-end justify-between">
                <div>
                    <h4 class="text-[24px] font-black leading-none mb-1">{{ $gradedCount }} / {{ $totalStudents }}
                        Dinilai</h4>
                    <p class="text-[12px] font-medium text-indigo-100">{{ $progressPercent }}% Progres Penilaian</p>
                </div>
                <div
                    class="w-14 h-14 rounded-full border-[4px] border-white/20 flex items-center justify-center relative">
                    <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                        <path class="text-white" stroke-dasharray="{{ $progressPercent }}, 100"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                    </svg>
                    <span class="text-[12px] font-bold">{{ $progressPercent }}%</span>
                </div>
            </div>
        </div>
    </div>

</div>
