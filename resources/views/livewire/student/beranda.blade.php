<div class="w-full pt-2">

    <section class="flex items-center gap-4 mt-2 px-1">
        <div
            class="w-16 h-16 rounded-[1.25rem] overflow-hidden bg-indigo-50 border border-slate-200 flex-shrink-0 shadow-sm flex items-center justify-center">
            @if (!empty($student->avatar))
                <img class="w-full h-full object-cover" src="{{ asset('storage/' . $student->avatar) }}" />
            @else
                <span class="text-[#3525cd] font-bold text-[24px]">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            @endif
        </div>
        <div class="flex flex-col">
            <p class="text-[13px] font-medium text-slate-500">{{ $greeting }}</p>
            <h2
                class="text-[20px] font-extrabold text-slate-800 tracking-tight leading-tight mt-0.5 truncate max-w-[200px]">
                {{ $user->name }}</h2>
            <p
                class="text-[11px] font-bold text-[#3525cd] mt-1 flex items-center gap-1 bg-[#3525cd]/10 w-max px-2 py-0.5 rounded-md">
                <span class="material-symbols-outlined text-[14px]">business_center</span>
                <span class="truncate max-w-[160px]">{{ $placement->dudika->name ?? 'Belum ada DUDIKA' }}</span>
            </p>
        </div>
    </section>

    @if ($announcements && $announcements->count() > 0)
        <section class="mt-6 px-1 flex flex-col gap-4">
            @foreach ($announcements as $index => $announcement)
                @php
                    // Beda warna tergantung Target Audience
                    $isUmum = $announcement->target_audience === 'Umum';
                    $bgGradient = $isUmum ? 'from-emerald-500 to-teal-600' : 'from-[#4f46e5] to-[#3525cd]';
                    $iconName = $isUmum ? 'public' : 'campaign';
                @endphp
                <div
                    class="relative overflow-hidden rounded-[1.25rem] bg-gradient-to-br {{ $bgGradient }} p-5 shadow-md flex flex-col gap-3">
                    <div
                        class="absolute -right-10 -top-10 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none">
                    </div>

                    <div class="flex items-start justify-between relative z-10">
                        <div class="flex flex-col gap-1">
                            <div
                                class="flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full w-max border border-white/10">
                                <span class="material-symbols-outlined text-[14px] text-white"
                                    style="font-variation-settings: 'FILL' 1;">{{ $iconName }}</span>
                                <span class="text-[10px] font-extrabold text-white uppercase tracking-wider">Pengumuman
                                    {{ $announcement->target_audience }}</span>
                            </div>
                            <p class="text-[10px] font-medium text-white/80 mt-1 pl-1">Diunggah:
                                {{ \Carbon\Carbon::parse($announcement->created_at)->isoFormat('D MMM YYYY') }}</p>
                        </div>

                        @if ($index === 0)
                            <span
                                class="text-[9px] font-bold text-white bg-white/20 px-2 py-1 rounded border border-white/10 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[11px]"
                                    style="font-variation-settings: 'FILL' 1;">new_releases</span> Terbaru
                            </span>
                        @endif
                    </div>

                    <div
                        class="relative z-10 mt-1 pl-1 text-[13px] text-white/90 leading-relaxed font-medium [&>p]:mb-2 [&>p:last-child]:mb-0 [&>strong]:font-extrabold [&>ul]:list-disc [&>ul]:pl-4">
                        <h3 class="text-[16px] font-extrabold text-white mb-1.5">{{ $announcement->title }}</h3>
                        <div>
                            {!! $announcement->content !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    <section class="flex flex-col gap-3 mt-6 px-1">
        <h3 class="text-[16px] font-extrabold text-slate-800">Status Kelengkapan Data</h3>
        <div class="bg-white rounded-2xl p-4 flex flex-col gap-3 shadow-sm border border-slate-100">

            <div class="flex items-center justify-between {{ !$isBiodataComplete ? 'group cursor-pointer' : '' }}"
                @if (!$isBiodataComplete) onclick="window.location.href='{{ route('siswa.profil.edit') }}'" @endif>
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full {{ $isBiodataComplete ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center">
                        <span
                            class="material-symbols-outlined text-[18px]">{{ $isBiodataComplete ? 'check' : 'priority_high' }}</span>
                    </div>
                    <span class="text-[13px] font-semibold text-slate-700">Biodata diri
                        {{ $isBiodataComplete ? 'lengkap' : 'belum lengkap' }}</span>
                </div>
                @if (!$isBiodataComplete)
                    <span class="material-symbols-outlined text-red-400 group-hover:text-red-600">chevron_right</span>
                @endif
            </div>

            <div class="h-px bg-slate-100"></div>

            <div class="flex items-center justify-between {{ !$isParentComplete ? 'group cursor-pointer' : '' }}"
                @if (!$isParentComplete) onclick="window.location.href='{{ route('siswa.profil.edit') }}'" @endif>
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full {{ $isParentComplete ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center">
                        <span
                            class="material-symbols-outlined text-[18px]">{{ $isParentComplete ? 'check' : 'priority_high' }}</span>
                    </div>
                    <span class="text-[13px] font-semibold text-slate-700">Data orang tua
                        {{ $isParentComplete ? 'lengkap' : 'belum lengkap' }}</span>
                </div>
                @if (!$isParentComplete)
                    <span class="material-symbols-outlined text-red-400 group-hover:text-red-600">chevron_right</span>
                @endif
            </div>

            <div class="h-px bg-slate-100"></div>

            <div class="flex items-center justify-between {{ !$isDudikaComplete ? 'group cursor-pointer' : '' }}"
                @if (!$isDudikaComplete) onclick="window.location.href='{{ route('siswa.dudika') }}'" @endif>
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full {{ $isDudikaComplete ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center">
                        <span
                            class="material-symbols-outlined text-[18px]">{{ $isDudikaComplete ? 'check' : 'priority_high' }}</span>
                    </div>
                    <span class="text-[13px] font-semibold text-slate-700">Data Bidang Pekerjaan
                        {{ $isDudikaComplete ? 'lengkap' : 'belum diisi' }}</span>
                </div>
                @if (!$isDudikaComplete)
                    <span class="material-symbols-outlined text-amber-500">chevron_right</span>
                @endif
            </div>

            <div class="h-px bg-slate-100"></div>

            <div class="flex items-center justify-between {{ !$isJurnalRevisiClean ? 'group cursor-pointer' : '' }}"
                @if (!$isJurnalRevisiClean) onclick="window.location.href='{{ route('siswa.jurnal') }}'" @endif>
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full {{ $isJurnalRevisiClean ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center">
                        <span
                            class="material-symbols-outlined text-[18px]">{{ $isJurnalRevisiClean ? 'check' : 'priority_high' }}</span>
                    </div>
                    <span class="text-[13px] font-semibold text-slate-700">Jurnal Revisi
                        {{ $isJurnalRevisiClean ? 'aman (tidak ada)' : "($revisiCount perlu diperbaiki)" }}</span>
                </div>
                @if (!$isJurnalRevisiClean)
                    <span class="material-symbols-outlined text-red-500">chevron_right</span>
                @endif
            </div>

        </div>
    </section>

    <section class="flex flex-col gap-3 mt-6 px-1 relative z-10">
        <div class="flex items-center justify-between">
            <h3 class="text-[16px] font-extrabold text-slate-800">Rekap Absensi</h3>
            <span class="text-[11px] font-bold text-[#3525cd] bg-[#3525cd]/10 px-2 py-1 rounded-md">Bulan Ini</span>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-[1rem] p-4 shadow-sm border border-slate-100 flex flex-col gap-2">
                <div
                    class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center border border-green-100">
                    <span class="material-symbols-outlined text-green-600"
                        style="font-variation-settings: 'FILL' 1;">check_circle</span>
                </div>
                <div class="mt-1">
                    <div class="text-[24px] font-extrabold text-slate-800 leading-none">{{ $recap['Hadir'] }}</div>
                    <div class="text-[11px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Hadir (Hari)</div>
                </div>
            </div>

            <div class="bg-white rounded-[1rem] p-4 shadow-sm border border-slate-100 flex flex-col gap-2">
                <div
                    class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center border border-amber-100">
                    <span class="material-symbols-outlined text-amber-600"
                        style="font-variation-settings: 'FILL' 1;">edit_note</span>
                </div>
                <div class="mt-1">
                    <div class="text-[24px] font-extrabold text-slate-800 leading-none">{{ $recap['Izin'] }}</div>
                    <div class="text-[11px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Izin (Hari)</div>
                </div>
            </div>

            <div class="bg-white rounded-[1rem] p-4 shadow-sm border border-slate-100 flex flex-col gap-2">
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center border border-red-100">
                    <span class="material-symbols-outlined text-red-500"
                        style="font-variation-settings: 'FILL' 1;">medical_services</span>
                </div>
                <div class="mt-1">
                    <div class="text-[24px] font-extrabold text-slate-800 leading-none">{{ $recap['Sakit'] }}</div>
                    <div class="text-[11px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Sakit (Hari)</div>
                </div>
            </div>

            <div class="bg-white rounded-[1rem] p-4 shadow-sm border border-slate-100 flex flex-col gap-2">
                <div
                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200">
                    <span class="material-symbols-outlined text-slate-600"
                        style="font-variation-settings: 'FILL' 1;">cancel</span>
                </div>
                <div class="mt-1">
                    <div class="text-[24px] font-extrabold text-slate-800 leading-none">{{ $recap['Alpha'] }}</div>
                    <div class="text-[11px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Alpha (Hari)</div>
                </div>
            </div>
        </div>
    </section>

    <div class="fixed bottom-[85px] right-4 z-[60]">
        <a href="{{ route('siswa.bot') }}" wire:navigate
            class="flex h-14 w-14 items-center justify-center rounded-full bg-[#3525cd] text-white shadow-[0_4px_15px_rgba(53,37,205,0.4)] active:scale-90 hover:bg-[#2c1eb3] transition-all border-[3px] border-white">
            <span class="material-symbols-outlined text-[26px]"
                style="font-variation-settings: 'FILL' 1;">smart_toy</span>
        </a>
    </div>

</div>
