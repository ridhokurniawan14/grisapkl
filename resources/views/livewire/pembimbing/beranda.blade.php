<div class="w-full flex flex-col pb-2 pt-2">

    <section class="mt-2 px-2">
        <h1 class="text-[20px] font-extrabold text-slate-800 leading-tight">{{ $greeting }} <br> <span
                class="text-[#3525cd]">{{ $user->name }}</span></h1>
        <p class="text-[12px] font-medium text-slate-500 mt-1">
            {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
        </p>
    </section>

    @if ($announcements && $announcements->count() > 0)
        <section class="mt-5 px-2 flex flex-col gap-4">
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
                                <h3 class="text-[14px] font-extrabold text-white mb-1">{{ $announcement->title }}</h3>
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
    @endif

    <section class="grid grid-cols-2 gap-3 mt-5 px-2">
        <a href="{{ route('pembimbing.siswa') }}" wire:navigate
            class="col-span-1 bg-[#f5f2ff] hover:bg-indigo-100 transition-colors rounded-[1rem] p-4 flex flex-col justify-between h-[130px] shadow-sm border border-indigo-50 active:scale-95">
            <span class="material-symbols-outlined text-[#3525cd] text-[28px]"
                style="font-variation-settings: 'FILL' 1;">groups</span>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Siswa Bimbingan</p>
                <p class="text-[32px] font-extrabold text-[#3525cd] leading-none mt-1">{{ $totalSiswa }}</p>
            </div>
        </a>

        <a href="{{ route('pembimbing.data') }}" wire:navigate
            class="col-span-1 bg-amber-50 hover:bg-amber-100 transition-colors rounded-[1rem] p-4 flex flex-col justify-between h-[130px] shadow-sm border border-amber-100 active:scale-95">
            <span class="material-symbols-outlined text-amber-500 text-[28px]"
                style="font-variation-settings: 'FILL' 1;">fact_check</span>
            <div>
                <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">Butuh Validasi</p>
                <p class="text-[32px] font-extrabold text-amber-600 leading-none mt-1">{{ $jurnalMenunggu }}</p>
            </div>
        </a>
    </section>

    <section class="mt-6 px-2" x-data="{ openDudika: true, openSiswa: false }">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-[16px] font-extrabold text-slate-800">Kelengkapan Data</h3>
        </div>

        @if ($isTeacherComplete)
            <div
                class="flex items-center justify-between p-4 bg-white border border-slate-200 rounded-[1rem] shadow-sm mb-3">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[18px]"
                            style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    </div>
                    <div>
                        <p class="text-[13px] font-extrabold text-slate-800 leading-tight">Profil Guru Pembimbing</p>
                        <p class="text-[11px] font-bold text-emerald-600 mt-0.5">Lengkap & Aktif</p>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('pembimbing.profil') }}" wire:navigate
                class="flex items-center justify-between p-4 bg-red-50 hover:bg-red-100 transition-colors border border-red-200 rounded-[1rem] shadow-sm mb-3 group active:scale-95">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[18px]"
                            style="font-variation-settings: 'FILL' 1;">error</span>
                    </div>
                    <div>
                        <p class="text-[13px] font-extrabold text-red-800 leading-tight">Profil Belum Lengkap</p>
                        <p class="text-[11px] font-bold text-red-600 mt-0.5">Lengkapi TTD & Mapel (Klik di sini)</p>
                    </div>
                </div>
                <span
                    class="material-symbols-outlined text-red-400 group-hover:text-red-600 transition-colors">chevron_right</span>
            </a>
        @endif

        <div class="bg-white border border-slate-200 rounded-[1rem] overflow-hidden shadow-sm mb-3 transition-all">
            <button @click="openDudika = !openDudika"
                class="w-full px-4 py-3 bg-slate-50 hover:bg-slate-100 transition-colors flex justify-between items-center outline-none">
                <div class="flex items-center gap-2">
                    <span class="text-[13px] font-extrabold text-slate-800">Informasi DUDIKA</span>
                    @if ($dudikaPendingCount > 0)
                        <span
                            class="text-[9px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">{{ $dudikaPendingCount }}
                            Belum Lengkap</span>
                    @else
                        <span
                            class="text-[9px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Lengkap</span>
                    @endif
                </div>
                <span class="material-symbols-outlined text-slate-400 transition-transform duration-300"
                    :class="openDudika ? 'rotate-180' : ''">expand_more</span>
            </button>

            <div x-show="openDudika" x-collapse>
                <div class="divide-y divide-slate-100">
                    @forelse($dudikaList as $dudika)
                        <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <span
                                    class="material-symbols-outlined {{ $dudika['is_complete'] ? 'text-emerald-500' : 'text-amber-500' }} text-[20px]">
                                    {{ $dudika['is_complete'] ? 'check_box' : 'indeterminate_check_box' }}
                                </span>
                                <span
                                    class="text-[13px] font-semibold text-slate-700 truncate max-w-[150px]">{{ $dudika['name'] }}</span>
                            </div>

                            @if (!$dudika['is_complete'] && !empty($dudika['phone']))
                                <a href="https://wa.me/{{ $dudika['phone'] }}?text={{ $dudika['wa_message'] }}"
                                    target="_blank"
                                    class="flex items-center gap-1 bg-[#25D366] hover:bg-[#20bd5a] text-white px-2.5 py-1.5 rounded-[0.5rem] transition-transform active:scale-95 shadow-sm">
                                    <span class="material-symbols-outlined text-[14px]">chat</span>
                                    <span class="text-[10px] font-bold">WhatsApp</span>
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="p-4 text-[12px] text-center text-slate-400">Belum ada penempatan DUDIKA aktif.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-[1rem] overflow-hidden shadow-sm transition-all">
            <button @click="openSiswa = !openSiswa"
                class="w-full px-4 py-3 bg-slate-50 hover:bg-slate-100 transition-colors flex justify-between items-center outline-none">
                <div class="flex items-center gap-2">
                    <span class="text-[13px] font-extrabold text-slate-800">Kelengkapan Siswa</span>
                    @if ($studentPendingCount > 0)
                        <span
                            class="text-[9px] font-bold bg-red-100 text-red-700 px-2 py-0.5 rounded-full">{{ $studentPendingCount }}
                            Belum Lengkap</span>
                    @else
                        <span
                            class="text-[9px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Lengkap</span>
                    @endif
                </div>
                <span class="material-symbols-outlined text-slate-400 transition-transform duration-300"
                    :class="openSiswa ? 'rotate-180' : ''">expand_more</span>
            </button>

            <div x-show="openSiswa" x-collapse>
                <div class="divide-y divide-slate-100">
                    @forelse($studentList as $student)
                        <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <span
                                    class="material-symbols-outlined {{ $student['is_complete'] ? 'text-emerald-500' : 'text-red-500' }} text-[20px]">
                                    {{ $student['is_complete'] ? 'verified_user' : 'warning' }}
                                </span>
                                <span
                                    class="text-[13px] font-semibold text-slate-700 truncate max-w-[150px]">{{ $student['name'] }}</span>
                            </div>

                            @if (!$student['is_complete'] && !empty($student['phone']))
                                <a href="https://wa.me/{{ $student['phone'] }}?text={{ $student['wa_message'] }}"
                                    target="_blank"
                                    class="flex items-center gap-1 bg-[#25D366] hover:bg-[#20bd5a] text-white px-2.5 py-1.5 rounded-[0.5rem] transition-transform active:scale-95 shadow-sm shrink-0">
                                    <span class="material-symbols-outlined text-[14px]">chat</span>
                                    <span class="text-[10px] font-bold">Ingatkan</span>
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="p-4 text-[12px] text-center text-slate-400">Belum ada siswa bimbingan aktif.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </section>
    <div class="absolute bottom-[85px] right-4 z-[60]">
        <a href="{{ route('pembimbing.bot') }}" wire:navigate
            class="flex h-14 w-14 items-center justify-center rounded-full bg-[#3525cd] text-white shadow-[0_4px_15px_rgba(53,37,205,0.4)] active:scale-90 hover:bg-[#2c1eb3] transition-all border-[3px] border-white">
            <span class="material-symbols-outlined text-[26px]"
                style="font-variation-settings: 'FILL' 1;">smart_toy</span>
        </a>
    </div>
</div>
