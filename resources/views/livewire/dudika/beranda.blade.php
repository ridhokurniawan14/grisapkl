<div class="w-full flex flex-col pb-4 pt-2">

    <section class="mt-2 px-2 flex justify-between items-start">
        <div>
            <h1 class="text-[20px] font-extrabold text-slate-800 leading-tight">{{ $greeting }} <br> <span
                    class="text-[#3525cd]">{{ $dudikaName }}</span></h1>
            <p class="text-[12px] font-medium text-slate-500 mt-1">
                {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
            </p>
        </div>
    </section>

    @if (isset($announcements) && $announcements->count() > 0)
        <section class="mt-5 mb-5 px-2 flex flex-col gap-4">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-[#3525cd] text-[18px]">campaign</span>
                <h3 class="text-[14px] font-extrabold text-slate-800">Informasi Terbaru</h3>
            </div>
            @foreach ($announcements as $index => $announcement)
                <div
                    class="relative overflow-hidden rounded-[1rem] bg-gradient-to-br from-[#4f46e5] to-[#3525cd] p-4 shadow-sm">
                    <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none">
                        <span class="material-symbols-outlined text-[100px]">info</span>
                    </div>

                    <div class="relative z-10 flex items-start gap-3">
                        <div class="bg-white/20 p-2 rounded-lg shrink-0">
                            <span class="material-symbols-outlined text-white text-[20px]"
                                style="font-variation-settings: 'FILL' 1;">
                                {{ $announcement->target_audience === 'Umum' ? 'public' : 'campaign' }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h3 class="text-[14px] font-extrabold text-white mb-1">{{ $announcement->title }}
                                </h3>
                            </div>
                            <div
                                class="text-[12px] font-medium text-indigo-100 opacity-90 leading-relaxed [&>p]:mb-1 [&>p:last-child]:mb-0 [&>strong]:font-extrabold">
                                {!! $announcement->content !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
    @else
        <div class="mt-4"></div>
    @endif

    <section class="px-2">
        @if ($isComplete)
            <div
                class="flex items-center justify-between p-4 bg-white border border-slate-200 rounded-[1.25rem] shadow-sm mb-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    </div>
                    <div>
                        <p class="text-[14px] font-extrabold text-slate-800 leading-tight">Profil Instansi DUDIKA</p>
                        <p class="text-[11px] font-bold text-emerald-600 mt-0.5">Sudah Lengkap & Aktif</p>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('dudika.profil.edit') }}" wire:navigate
                class="flex items-center justify-between p-4 bg-red-50 hover:bg-red-100 transition-colors border border-red-200 rounded-[1.25rem] shadow-sm mb-4 group active:scale-95">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings: 'FILL' 1;">domain_add</span>
                    </div>
                    <div>
                        <p class="text-[14px] font-extrabold text-red-800 leading-tight">Data DUDIKA Belum Lengkap</p>
                        <p class="text-[11px] font-bold text-red-600 mt-0.5">Lengkapi profil perusahaan (Klik di sini)
                        </p>
                    </div>
                </div>
                <span
                    class="material-symbols-outlined text-red-400 group-hover:text-red-600 transition-colors">chevron_right</span>
            </a>
        @endif
    </section>

    <section class="mt-2 px-2">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-[16px] font-extrabold text-slate-800">Siswa Magang & Rekap</h3>
            <span
                class="bg-[#e2dfff] text-[#3525cd] px-2.5 py-0.5 rounded-full text-[11px] font-extrabold shadow-sm border border-white">
                {{ count($students) }} Siswa
            </span>
        </div>

        <div class="relative mb-4">
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
                class="w-full pl-11 pr-11 py-2.5 bg-white border border-slate-200 rounded-xl text-[13px] font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] transition-all shadow-sm"
                placeholder="Cari nama siswa atau bidang magang...">

            @if ($search)
                <button wire:click="$set('search', '')"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-red-500 transition-colors active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            @endif
        </div>

        <div class="flex flex-col gap-3 pb-4" wire:loading.class="opacity-50 transition-opacity duration-200"
            wire:target="search">
            @forelse ($students as $siswa)
                <div
                    class="bg-white border border-slate-200 rounded-[1.25rem] overflow-hidden shadow-sm hover:shadow-md transition-shadow">

                    <div class="p-4 border-b border-slate-50 flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-[1rem] bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0 overflow-hidden shadow-inner text-[#3525cd] font-bold">
                            @if ($siswa['avatar'])
                                <img src="{{ $siswa['avatar'] }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($siswa['name'], 0, 1)) }}
                            @endif
                        </div>

                        <div class="flex-1">
                            <h3 class="text-[15px] font-extrabold text-slate-800 leading-tight">{{ $siswa['name'] }}
                            </h3>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate max-w-[190px]">
                                {{ $siswa['field'] }}
                            </p>
                        </div>

                        @if (!empty($siswa['phone']))
                            <a href="https://wa.me/{{ $siswa['phone'] }}" target="_blank"
                                class="w-9 h-9 rounded-full bg-[#25D366]/10 text-[#25D366] hover:bg-[#25D366] hover:text-white flex items-center justify-center transition-colors shrink-0 active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">chat</span>
                            </a>
                        @endif
                    </div>

                    <div class="p-4 pt-3 bg-slate-50/50">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Total
                            Kehadiran Siswa</p>

                        <div class="grid grid-cols-5 gap-1.5">
                            <div
                                class="bg-white border border-emerald-100 rounded-xl py-1.5 flex flex-col items-center justify-center shadow-sm">
                                <span class="text-[9px] font-bold text-slate-400 mb-0.5">Hadir</span>
                                <span
                                    class="text-[14px] font-extrabold text-emerald-600 leading-none">{{ $siswa['recap']['H'] }}</span>
                            </div>
                            <div
                                class="bg-white border border-amber-100 rounded-xl py-1.5 flex flex-col items-center justify-center shadow-sm">
                                <span class="text-[9px] font-bold text-slate-400 mb-0.5">Izin</span>
                                <span
                                    class="text-[14px] font-extrabold text-amber-500 leading-none">{{ $siswa['recap']['I'] }}</span>
                            </div>
                            <div
                                class="bg-white border border-red-100 rounded-xl py-1.5 flex flex-col items-center justify-center shadow-sm">
                                <span class="text-[9px] font-bold text-slate-400 mb-0.5">Sakit</span>
                                <span
                                    class="text-[14px] font-extrabold text-red-500 leading-none">{{ $siswa['recap']['S'] }}</span>
                            </div>
                            <div
                                class="bg-white border border-blue-100 rounded-xl py-1.5 flex flex-col items-center justify-center shadow-sm">
                                <span class="text-[9px] font-bold text-slate-400 mb-0.5">Libur</span>
                                <span
                                    class="text-[14px] font-extrabold text-blue-500 leading-none">{{ $siswa['recap']['L'] }}</span>
                            </div>
                            <div
                                class="bg-white border border-slate-200 rounded-xl py-1.5 flex flex-col items-center justify-center shadow-sm">
                                <span class="text-[9px] font-bold text-slate-400 mb-0.5">Alpha</span>
                                <span
                                    class="text-[14px] font-extrabold text-slate-700 leading-none">{{ $siswa['recap']['A'] }}</span>
                            </div>
                        </div>

                        <a href="{{ route('dudika.jurnal') }}" wire:navigate
                            class="w-full mt-3 h-[38px] bg-white border border-indigo-100 text-[#3525cd] text-[12px] font-extrabold rounded-[0.75rem] shadow-sm flex items-center justify-center gap-1.5 active:scale-95 transition-transform hover:bg-indigo-50">
                            <span class="material-symbols-outlined text-[16px]">menu_book</span> Lihat Jurnal Siswa
                        </a>
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 mt-2 shadow-sm">
                    <span class="material-symbols-outlined text-[48px] text-slate-300 mb-2">person_search</span>
                    <p class="text-[14px] font-bold text-slate-700">Siswa tidak ditemukan</p>
                    <p class="text-[12px] text-slate-500 mt-1">Belum ada data siswa magang atau coba ubah kata kunci.
                    </p>
                </div>
            @endforelse
        </div>
    </section>
    <div class="absolute bottom-[85px] right-4 z-[60]">
        <a href="{{ route('dudika.bot') }}" wire:navigate
            class="flex h-14 w-14 items-center justify-center rounded-full bg-[#3525cd] text-white shadow-[0_4px_15px_rgba(53,37,205,0.4)] active:scale-90 hover:bg-[#2c1eb3] transition-all border-[3px] border-white">
            <span class="material-symbols-outlined text-[26px]"
                style="font-variation-settings: 'FILL' 1;">smart_toy</span>
        </a>
    </div>
</div>
