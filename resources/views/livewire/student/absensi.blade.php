<div wire:poll.30s class="flex flex-col w-full relative pb-2 overflow-hidden min-h-[calc(100vh-4rem)]"
    x-data="absensiApp()" x-init="startClock()">

    <div class="relative z-10 w-full flex flex-col">

        <div class="flex flex-col items-center justify-center mb-4 text-center mt-2">
            <h2 class="text-4xl font-extrabold text-on-surface mb-1 tracking-tight" x-text="currentTime"></h2>
            <p class="text-[13px] font-medium text-outline" x-text="currentDate"></p>

            @if ($isOutsidePklRange)
                <div
                    class="mt-4 bg-red-50/90 backdrop-blur-sm px-4 py-2 rounded-full border border-red-200 flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-red-500">event_busy</span>
                    <span class="text-xs font-bold text-red-600">Di Luar Jadwal PKL</span>
                </div>
            @elseif (!$hasAttendedToday)
                <div
                    class="mt-4 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full border border-surface-container-high flex items-center gap-2 shadow-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-on-surface-variant">Siap untuk Absen</span>
                </div>
            @else
                <div
                    class="mt-4 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full border border-surface-container-high flex items-center gap-2 shadow-sm opacity-80">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                    <span class="text-xs font-medium text-on-surface-variant">Sudah Absen Hari Ini</span>
                </div>
            @endif
        </div>

        <div class="flex justify-center my-6 relative h-[260px] items-center">
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-[230px] h-[230px] rounded-full border-[3px] border-primary/20"></div>
                <div class="w-[270px] h-[270px] rounded-full border border-primary/10 absolute"></div>
            </div>

            @if ($isOutsidePklRange)
                <div
                    class="w-[160px] h-[160px] rounded-full bg-slate-100 text-slate-400 flex flex-col items-center justify-center shadow-inner z-10 border-4 border-white backdrop-blur-md">
                    <span class="material-symbols-outlined text-[56px] mb-1"
                        style="font-variation-settings: 'FILL' 1;">block</span>
                    <span class="text-[14px] font-bold text-center tracking-wide">Terkunci</span>
                </div>
            @elseif (!$hasAttendedToday)
                <button @click="openCamera()"
                    class="w-[160px] h-[160px] rounded-full bg-primary text-white flex flex-col items-center justify-center shadow-[0_10px_40px_rgba(53,37,205,0.4)] active:scale-95 transition-transform z-10 animate-subtle-pulse border-4 border-white">
                    <span class="material-symbols-outlined text-[56px] mb-1"
                        style="font-variation-settings: 'FILL' 1;">fingerprint</span>
                    <span class="text-[15px] font-semibold tracking-wide">Absen Masuk</span>
                </button>
            @else
                <div
                    class="w-[160px] h-[160px] rounded-full bg-surface-variant text-outline flex flex-col items-center justify-center shadow-inner z-10 border-4 border-white backdrop-blur-md bg-white/50">
                    <span class="material-symbols-outlined text-[56px] mb-1"
                        style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    <span class="text-[15px] font-semibold tracking-wide">Selesai</span>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-3 gap-3 mb-8 relative z-20">
            <button @if (!$hasAttendedToday && !$isOutsidePklRange) @click="openConfirm('Izin')" @endif
                {{ $hasAttendedToday || $isOutsidePklRange ? 'disabled' : '' }}
                class="flex flex-col items-center justify-center p-3 rounded-2xl shadow-sm border transition-colors h-20 bg-white/90 backdrop-blur-sm
                {{ $hasAttendedToday || $isOutsidePklRange ? 'bg-surface-variant/50 text-outline border-transparent' : 'text-on-surface hover:bg-surface-variant border-outline-variant/30 active:scale-95' }}">
                <span class="material-symbols-outlined mb-1 text-amber-600">assignment_late</span>
                <span class="text-xs font-medium">Izin</span>
            </button>
            <button @if (!$hasAttendedToday && !$isOutsidePklRange) @click="openConfirm('Sakit')" @endif
                {{ $hasAttendedToday || $isOutsidePklRange ? 'disabled' : '' }}
                class="flex flex-col items-center justify-center p-3 rounded-2xl shadow-sm border transition-colors h-20 bg-white/90 backdrop-blur-sm
                {{ $hasAttendedToday || $isOutsidePklRange ? 'bg-surface-variant/50 text-outline border-transparent' : 'text-on-surface hover:bg-surface-variant border-outline-variant/30 active:scale-95' }}">
                <span class="material-symbols-outlined mb-1 text-red-500">medical_services</span>
                <span class="text-xs font-medium">Sakit</span>
            </button>
            <button @if (!$hasAttendedToday && !$isOutsidePklRange) @click="openConfirm('Libur')" @endif
                {{ $hasAttendedToday || $isOutsidePklRange ? 'disabled' : '' }}
                class="flex flex-col items-center justify-center p-3 rounded-2xl shadow-sm border transition-colors h-20 bg-white/90 backdrop-blur-sm
                {{ $hasAttendedToday || $isOutsidePklRange ? 'bg-surface-variant/50 text-outline border-transparent' : 'text-on-surface hover:bg-surface-variant border-outline-variant/30 active:scale-95' }}">
                <span class="material-symbols-outlined mb-1 text-blue-500">event_available</span>
                <span class="text-xs font-medium">Libur</span>
            </button>
        </div>

        <div class="mb-6 relative z-20">
            <div class="flex flex-col mb-3 px-1">
                <h3 class="text-lg font-bold text-slate-800">Rekap Kehadiran PKL</h3>
                @if ($placement && $placement->start_date && $placement->end_date)
                    <p class="text-[11px] font-semibold text-[#3525cd] mt-0.5">
                        <span class="material-symbols-outlined text-[12px] align-middle mr-0.5">calendar_month</span>
                        {{ \Carbon\Carbon::parse($placement->start_date)->isoFormat('D MMM YYYY') }} s.d.
                        {{ \Carbon\Carbon::parse($placement->end_date)->isoFormat('D MMM YYYY') }}
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-5 gap-2">
                <div
                    class="bg-white/90 backdrop-blur-sm p-2 rounded-2xl shadow-sm border border-green-100 flex flex-col items-center justify-center">
                    <span class="text-[10px] font-semibold text-slate-400 mb-0.5">Hadir</span>
                    <span class="text-[16px] font-extrabold text-green-600">{{ $recap['Hadir'] }}</span>
                </div>
                <div
                    class="bg-white/90 backdrop-blur-sm p-2 rounded-2xl shadow-sm border border-amber-100 flex flex-col items-center justify-center">
                    <span class="text-[10px] font-semibold text-slate-400 mb-0.5">Izin</span>
                    <span class="text-[16px] font-extrabold text-amber-500">{{ $recap['Izin'] }}</span>
                </div>
                <div
                    class="bg-white/90 backdrop-blur-sm p-2 rounded-2xl shadow-sm border border-red-100 flex flex-col items-center justify-center">
                    <span class="text-[10px] font-semibold text-slate-400 mb-0.5">Sakit</span>
                    <span class="text-[16px] font-extrabold text-red-500">{{ $recap['Sakit'] }}</span>
                </div>
                <div
                    class="bg-white/90 backdrop-blur-sm p-2 rounded-2xl shadow-sm border border-blue-100 flex flex-col items-center justify-center">
                    <span class="text-[10px] font-semibold text-slate-400 mb-0.5">Libur</span>
                    <span class="text-[16px] font-extrabold text-blue-500">{{ $recap['Libur'] }}</span>
                </div>
                <div
                    class="bg-white/90 backdrop-blur-sm p-2 rounded-2xl shadow-sm border border-slate-200 flex flex-col items-center justify-center">
                    <span class="text-[10px] font-semibold text-slate-400 mb-0.5">Alpha</span>
                    <span class="text-[16px] font-extrabold text-slate-700">{{ $recap['Alpha'] }}</span>
                </div>
            </div>
        </div>

        @if (
            $hasAttendedToday &&
                $todayJournal &&
                $todayJournal->activity === '' &&
                in_array($todayJournal->attend_status, ['Hadir', 'Izin', 'Sakit']))
            <div
                class="bg-white/90 backdrop-blur-sm rounded-2xl p-5 shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-outline-variant/30 mb-8 animate-fade-in-up relative z-20">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-8 h-8 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed">
                        <span class="material-symbols-outlined text-[18px]"
                            style="font-variation-settings: 'FILL' 1;">edit_document</span>
                    </div>
                    <h3 class="text-lg font-bold text-on-surface">Lengkapi Bukti & Jurnal</h3>
                </div>

                <form wire:submit="saveJournal">
                    <div class="mb-4">
                        <p class="text-[13px] font-medium text-on-surface-variant mb-1.5">Foto
                            {{ $todayJournal->attend_status === 'Hadir' ? 'Kegiatan' : 'Bukti (Surat Dokter/Ortu)' }}
                        </p>
                        <label
                            class="w-full h-36 bg-surface rounded-xl border border-dashed border-outline-variant/60 flex flex-col items-center justify-center relative overflow-hidden cursor-pointer hover:bg-surface-container transition-colors">
                            @if ($activityPhoto)
                                <img src="{{ $activityPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                                <div
                                    class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-semibold">Ganti Foto</span>
                                </div>
                            @else
                                <span class="material-symbols-outlined text-[32px] text-outline mb-1">add_a_photo</span>
                                <span class="text-[12px] font-medium text-outline">Ambil atau pilih foto</span>
                            @endif
                            <input type="file" @change="compressAndUploadImage" class="hidden" accept="image/*">
                        </label>

                        <div x-show="isCompressing" x-cloak
                            class="text-xs text-primary mt-2 font-bold animate-pulse flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] animate-spin">sync</span> Memproses &
                            Mengkompres Foto...
                        </div>

                        <div wire:loading wire:target="activityPhoto" class="text-xs text-primary mt-1">Menyimpan ke
                            Server...</div>
                        @error('activityPhoto')
                            <span class="text-xs text-error mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <textarea wire:model="activity"
                        class="w-full bg-surface border {{ $errors->has('activity') ? 'border-error' : 'border-outline-variant' }} rounded-xl p-3 font-body-md text-[14px] text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none resize-none h-28 mb-4 placeholder:text-outline/60"
                        placeholder="{{ $todayJournal->attend_status === 'Hadir' ? 'Ceritakan apa saja yang kamu pelajari/kerjakan hari ini...' : 'Berikan keterangan detail...' }}"
                        required></textarea>
                    @error('activity')
                        <span class="text-xs text-error mt-[-10px] block mb-3">{{ $message }}</span>
                    @enderror

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-primary hover:bg-primary-fixed-variant text-white h-12 px-6 rounded-xl font-semibold flex items-center justify-center shadow-md active:scale-95 transition-all">
                            <span wire:loading.remove wire:target="saveJournal">Simpan Laporan</span>
                            <span wire:loading wire:target="saveJournal">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="mb-4 relative z-20">
            <div class="flex items-center justify-between mb-3 px-1">
                <h3 class="text-lg font-bold text-on-surface">History Absen</h3>
                <p class="text-[11px] font-semibold text-[#3525cd]">
                    1 Minggu
                </p>
            </div>

            <div class="flex flex-col gap-3">
                @forelse($recentJournals as $journal)
                    @if ($journal->attend_status === 'Libur')
                        <div
                            class="bg-white/90 backdrop-blur-sm p-3.5 rounded-2xl border border-surface-container shadow-sm flex items-center justify-between opacity-90">
                            <div class="flex items-center gap-3">
                                <div
                                    class="bg-blue-100 w-10 h-10 rounded-full flex items-center justify-center text-blue-700 border border-white">
                                    <span class="material-symbols-outlined text-[20px]"
                                        style="font-variation-settings: 'FILL' 1;">event_available</span>
                                </div>
                                <div>
                                    <p class="text-[14px] font-semibold text-on-surface">Libur</p>
                                    <p class="text-[11px] text-outline">{{ $journal->formatted_date }}</p>
                                </div>
                            </div>
                            <span
                                class="bg-blue-50 text-blue-700 border-blue-200 px-3 py-1 rounded-full text-[10px] font-bold border">
                                Hari Libur
                            </span>
                        </div>
                    @else
                        <div @click="openDetail({{ $journal->toJson() }})"
                            class="cursor-pointer bg-white/90 backdrop-blur-sm p-3.5 rounded-2xl border border-surface-container shadow-sm hover:shadow-md hover:border-primary/30 transition-all flex items-center justify-between active:scale-[0.98]">
                            <div class="flex items-center gap-3">
                                @php
                                    $iconBg = 'bg-green-100';
                                    $iconColor = 'text-green-700';
                                    $iconName = 'login';
                                    if ($journal->attend_status == 'Izin') {
                                        $iconBg = 'bg-amber-100';
                                        $iconColor = 'text-amber-700';
                                        $iconName = 'assignment_late';
                                    }
                                    if ($journal->attend_status == 'Sakit') {
                                        $iconBg = 'bg-red-100';
                                        $iconColor = 'text-red-700';
                                        $iconName = 'medical_services';
                                    }
                                @endphp
                                <div
                                    class="{{ $iconBg }} w-10 h-10 rounded-full flex items-center justify-center {{ $iconColor }} overflow-hidden border border-white">
                                    <span class="material-symbols-outlined text-[20px]"
                                        style="font-variation-settings: 'FILL' 1;">{{ $iconName }}</span>
                                </div>
                                <div>
                                    <p class="text-[14px] font-semibold text-on-surface">{{ $journal->attend_status }}
                                    </p>
                                    <p class="text-[11px] text-outline">{{ $journal->formatted_date }} •
                                        {{ $journal->formatted_time }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="{{ $journal->is_valid ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }} px-3 py-1 rounded-full text-[10px] font-bold border">
                                    {{ $journal->is_valid ? 'Valid' : 'Invalid' }}
                                </span>
                                <span class="material-symbols-outlined text-outline text-[18px]">chevron_right</span>
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="text-center text-sm text-outline py-4">Belum ada history absen.</p>
                @endforelse
            </div>
        </div>

    </div>
    <div x-show="showDetailModal" x-cloak
        class="fixed inset-0 z-[10000] flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm sm:px-4">
        <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full sm:scale-90 sm:translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100 sm:translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100 sm:translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full sm:scale-90 sm:translate-y-4"
            @click.away="if(fullScreenImg === null) showDetailModal = false"
            class="bg-white w-full max-w-[400px] sm:rounded-[2rem] rounded-t-[2rem] p-6 pb-8 flex flex-col shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <div class="w-12 h-1.5 bg-outline-variant/50 rounded-full mx-auto mb-4 sm:hidden"></div>
            <template x-if="selectedJournal">
                <div class="flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800"
                                x-text="'Laporan ' + selectedJournal.attend_status"></h3>
                            <p class="text-[13px] text-slate-500 mt-0.5"
                                x-text="selectedJournal.formatted_date + ' • ' + selectedJournal.formatted_time"></p>
                        </div>
                        <button @click="showDetailModal = false"
                            class="p-2 bg-slate-100 rounded-full text-slate-600 active:scale-95"><span
                                class="material-symbols-outlined text-[20px]">close</span></button>
                    </div>
                    <template x-if="selectedJournal.attendance_photo_url">
                        <div class="mb-4">
                            <p class="text-xs font-semibold text-slate-500 mb-1.5">Foto Absensi</p>
                            <div @click.stop="fullScreenImg = selectedJournal.attendance_photo_url"
                                class="w-full h-40 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 shadow-sm cursor-pointer active:scale-95 transition-transform relative group">
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
                        <div class="mb-4 bg-slate-50 p-3 rounded-xl border border-slate-200 flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">location_on</span>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-slate-700">Koordinat Lokasi</p>
                                <p class="text-[11px] text-slate-500"
                                    x-text="selectedJournal.latitude + ', ' + selectedJournal.longitude"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="selectedJournal.activity_photo_url">
                        <div class="mb-4">
                            <p class="text-xs font-semibold text-slate-500 mb-1.5">Foto Kegiatan / Bukti</p>
                            <div @click.stop="fullScreenImg = selectedJournal.activity_photo_url"
                                class="w-full h-40 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 shadow-sm cursor-pointer active:scale-95 transition-transform relative group">
                                <img :src="selectedJournal.activity_photo_url" class="w-full h-full object-cover">
                                <div
                                    class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span
                                        class="material-symbols-outlined text-white drop-shadow-md text-[32px]">zoom_in</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div class="mb-2">
                        <p class="text-xs font-semibold text-slate-500 mb-1.5">Keterangan Jurnal</p>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 min-h-[60px]">
                            <p class="text-[13px] text-slate-700 leading-relaxed"
                                x-text="selectedJournal.activity || 'Belum ada keterangan / jurnal belum diisi.'"></p>
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
    <div x-show="isCameraOpen" x-cloak class="fixed inset-0 z-[9999] bg-black flex flex-col justify-between">
        <div
            class="p-4 flex justify-between items-center text-white bg-gradient-to-b from-black/80 to-transparent z-30">
            <h3 class="font-semibold">Verifikasi Wajah & Lokasi</h3>
            <button @click="closeCamera()" class="p-2 bg-white/20 rounded-full active:scale-95"><span
                    class="material-symbols-outlined">close</span></button>
        </div>
        <div class="relative flex-1 flex items-center justify-center overflow-hidden">
            <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"
                style="transform: scaleX(-1);"></video>
            <canvas x-ref="canvas" class="hidden"></canvas>
            <div x-show="isLoading"
                class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center text-white z-40">
                <span class="material-symbols-outlined animate-spin text-[48px] mb-3">refresh</span>
                <p x-text="loadingText" class="text-sm font-semibold text-center px-4"></p>
            </div>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20 overflow-hidden">
                <div class="relative flex items-center justify-center">
                    <div
                        class="w-[220px] h-[300px] border-2 border-dashed border-white/80 rounded-[110px/150px] shadow-[0_0_0_4000px_rgba(0,0,0,0.6)]">
                    </div>
                </div>
                <div class="absolute bottom-16 w-full text-center text-white text-[14px] font-medium drop-shadow-md">
                    Posisikan wajah di dalam frame
                </div>
            </div>
        </div>
        <div class="p-8 pb-12 flex justify-center items-center bg-black z-30">
            <button @click="takeSnapshot()" :disabled="isLoading"
                class="w-20 h-20 bg-white rounded-full border-[6px] border-gray-400 active:scale-90 transition-transform disabled:opacity-50"></button>
        </div>
    </div>

    <div x-show="showConfirmModal" x-cloak
        class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
        <div x-show="showConfirmModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4" @click.away="showConfirmModal = false"
            class="bg-white w-full max-w-[320px] rounded-[2rem] p-6 flex flex-col items-center text-center shadow-2xl border border-slate-200 relative">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 shadow-inner"
                :class="confirmIconBg"><span class="material-symbols-outlined text-[36px]"
                    style="font-variation-settings: 'FILL' 1;" :class="confirmIconColor" x-text="confirmIcon"></span>
            </div>
            <h3 class="text-[20px] font-bold text-slate-800 mb-2" x-text="confirmTitle"></h3>
            <p class="text-[13px] text-slate-500 mb-6 leading-relaxed" x-text="confirmMessage"></p>
            <div class="flex w-full gap-3">
                <button @click="showConfirmModal = false"
                    class="flex-1 h-[48px] bg-slate-100 hover:bg-slate-200 text-slate-700 text-[14px] font-semibold rounded-[1.25rem] transition-all active:scale-95">Batal</button>
                <button @click="executeAttendance()"
                    class="flex-1 h-[48px] text-white text-[14px] font-semibold rounded-[1.25rem] transition-all active:scale-95 shadow-md"
                    :class="confirmBtnColor">Lanjutkan</button>
            </div>
        </div>
    </div>

    <div x-show="showErrorModal" x-cloak
        class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
        <div x-show="showErrorModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4" @click.away="showErrorModal = false"
            class="bg-white w-full max-w-[320px] rounded-[2rem] p-6 flex flex-col items-center text-center shadow-2xl border border-slate-200 relative">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4 shadow-inner"><span
                    class="material-symbols-outlined text-red-600 text-[36px]"
                    style="font-variation-settings: 'FILL' 1;">location_off</span></div>
            <h3 class="text-[20px] font-bold text-slate-800 mb-2">Perhatian!</h3>
            <p class="text-[13px] text-slate-500 mb-6 leading-relaxed" x-text="errorMessage"></p>
            <button @click="showErrorModal = false"
                class="w-full h-[48px] bg-error hover:bg-red-700 text-white text-[15px] font-semibold rounded-[1.25rem] transition-all active:scale-95 shadow-lg shadow-error/30">Mengerti</button>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('absensiApp', () => ({
                currentTime: 'Menunggu...',
                currentDate: '',
                isCompressing: false,
                isCameraOpen: false,
                isLoading: false,
                loadingText: '',
                stream: null,
                lat: null,
                lng: null,
                showErrorModal: false,
                errorMessage: '',
                showConfirmModal: false,
                confirmType: '',
                confirmTitle: '',
                confirmMessage: '',
                confirmIcon: '',
                confirmIconBg: '',
                confirmIconColor: '',
                confirmBtnColor: '',
                showDetailModal: false,
                selectedJournal: null,
                fullScreenImg: null,

                startClock() {
                    const updateTime = () => {
                        const now = new Date();
                        this.currentTime = now.toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }) + ' WIB';
                        this.currentDate = now.toLocaleDateString('id-ID', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    };
                    updateTime();
                    setInterval(updateTime, 1000);
                },

                // MANTRA SAKTI: Fungsi Kompresi Foto Instan di Browser
                compressAndUploadImage(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Validasi tipe file
                    if (!file.type.match(/image.*/)) {
                        this.errorMessage = 'File harus berupa gambar!';
                        this.showErrorModal = true;
                        return;
                    }

                    this.isCompressing = true; // Nyalakan loading
                    const reader = new FileReader();

                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const MAX_WIDTH = 1024; // Resolusi aman
                            const MAX_HEIGHT = 1024;
                            let width = img.width;
                            let height = img.height;

                            // Kalkulasi rasio agar gambar tidak gepeng
                            if (width > height) {
                                if (width > MAX_WIDTH) {
                                    height *= MAX_WIDTH / width;
                                    width = MAX_WIDTH;
                                }
                            } else {
                                if (height > MAX_HEIGHT) {
                                    width *= MAX_HEIGHT / height;
                                    height = MAX_HEIGHT;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            // Kompres menjadi format JPEG dengan kualitas 70% (0.7)
                            canvas.toBlob((blob) => {
                                // Ubah nama file menjadi .jpg agar dibaca dengan benar oleh backend
                                const newFileName = file.name.replace(/\.[^/.]+$/,
                                    ".jpg");
                                const compressedFile = new File([blob], newFileName, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });

                                // Tembakkan file yang sudah dikompres ke Livewire layaknya wire:model
                                this.$wire.upload('activityPhoto', compressedFile,
                                    (uploadedFilename) => {
                                        this.isCompressing = false; // Sukses upload
                                    },
                                    (error) => {
                                        this.isCompressing = false;
                                        this.errorMessage =
                                            'Gagal mengunggah foto. Coba lagi.';
                                        this.showErrorModal = true;
                                    },
                                    (event) => {
                                        // Bisa untuk progress bar jika diperlukan
                                    }
                                );
                            }, 'image/jpeg', 0.7);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                openDetail(journal) {
                    this.selectedJournal = journal;
                    this.showDetailModal = true;
                },

                openConfirm(type) {
                    this.confirmType = type;
                    this.showConfirmModal = true;
                    if (type === 'Izin') {
                        this.confirmTitle = 'Ajukan Izin';
                        this.confirmMessage =
                            'Anda yakin ingin mengajukan izin hari ini? Anda tetap diwajibkan mengunggah foto bukti.';
                        this.confirmIcon = 'assignment_late';
                        this.confirmIconBg = 'bg-amber-100';
                        this.confirmIconColor = 'text-amber-600';
                        this.confirmBtnColor = 'bg-amber-600 hover:bg-amber-700';
                    } else if (type === 'Sakit') {
                        this.confirmTitle = 'Lapor Sakit';
                        this.confirmMessage =
                            'Yakin ingin lapor sakit? Jangan lupa lampirkan foto surat dokter atau bukti lain pada form.';
                        this.confirmIcon = 'medical_services';
                        this.confirmIconBg = 'bg-red-100';
                        this.confirmIconColor = 'text-red-600';
                        this.confirmBtnColor = 'bg-red-600 hover:bg-red-700';
                    } else if (type === 'Libur') {
                        this.confirmTitle = 'Lapor Libur';
                        this.confirmMessage =
                            'Tandai hari ini sebagai tanggal merah atau libur? Anda tidak perlu mengisi jurnal.';
                        this.confirmIcon = 'event_available';
                        this.confirmIconBg = 'bg-blue-100';
                        this.confirmIconColor = 'text-blue-600';
                        this.confirmBtnColor = 'bg-blue-600 hover:bg-blue-700';
                    }
                },

                executeAttendance() {
                    this.showConfirmModal = false;
                    this.$wire.markAttendance(this.confirmType);
                },

                async openCamera() {
                    this.isCameraOpen = true;
                    this.isLoading = true;

                    // Ambil status radius dari Backend (Livewire Property)
                    const isRadiusEnabled = @json($isRadiusEnabled);

                    // LOGIKA JIKA RADIUS DIAKTIFKAN OLEH HUMAS
                    if (isRadiusEnabled) {
                        this.loadingText = 'Mengecek Radius & Lokasi...';
                        try {
                            const pos = await new Promise((resolve, reject) => {
                                navigator.geolocation.getCurrentPosition(resolve, reject, {
                                    enableHighAccuracy: true,
                                    timeout: 10000,
                                    maximumAge: 0
                                });
                            });

                            // Validasi ketat jika akurasi jelek (kemungkinan Fake GPS / di dalam gedung tertutup)
                            if (pos.coords.accuracy > 150) {
                                this.errorMessage =
                                    'Akurasi GPS bermasalah atau terdeteksi aplikasi Fake GPS. Pastikan Anda berada di luar ruangan dan mematikan aplikasi pihak ketiga!';
                                this.showErrorModal = true;
                                this.closeCamera();
                                return;
                            }

                            this.lat = pos.coords.latitude;
                            this.lng = pos.coords.longitude;

                            // Lempar ke Backend untuk dihitung jaraknya
                            let isWithinRadius = await this.$wire.verifyLocation(this.lat, this
                                .lng);
                            if (!isWithinRadius) {
                                this.errorMessage =
                                    'Lokasi Anda saat ini berada di luar radius DUDIKA yang diizinkan sekolah. Silakan masuk ke area tempat PKL untuk melakukan absensi.';
                                this.showErrorModal = true;
                                this.closeCamera();
                                return;
                            }
                        } catch (error) {
                            this.errorMessage =
                                'Gagal mengakses Lokasi. Pastikan izin GPS pada browser Anda sudah diberikan dan fitur GPS di HP menyala!';
                            this.showErrorModal = true;
                            this.closeCamera();
                            return;
                        }
                    }
                    // LOGIKA BYPASS JIKA RADIUS DINONAKTIFKAN OLEH HUMAS
                    else {
                        this.loadingText = 'Menyiapkan Kamera...';
                        try {
                            // Tetap coba ambil lokasi untuk dicetak di foto, tapi TIDAK DILARANG kalau akurasinya jelek
                            const pos = await new Promise((resolve, reject) => {
                                navigator.geolocation.getCurrentPosition(resolve, reject, {
                                    enableHighAccuracy: false,
                                    timeout: 5000, // Cuma nunggu 5 detik, gagal gapapa
                                    maximumAge: 10000
                                });
                            });
                            this.lat = pos.coords.latitude;
                            this.lng = pos.coords.longitude;
                        } catch (error) {
                            // Kalau gagal / ditolak lokasinya, set 0 saja, kamera tetap terbuka!
                            this.lat = 0;
                            this.lng = 0;
                        }
                    }

                    // Buka Kamera (Wajib untuk foto selfie Absen)
                    try {
                        this.loadingText = 'Membuka Kamera...';
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'user'
                            }
                        });
                        this.$refs.video.srcObject = this.stream;
                        this.isLoading = false;
                    } catch (error) {
                        this.errorMessage =
                            'Gagal mengakses Kamera. Pastikan izin Kamera pada browser Anda sudah diberikan!';
                        this.showErrorModal = true;
                        this.closeCamera();
                    }
                },

                closeCamera() {
                    this.isCameraOpen = false;
                    this.isLoading = false;
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                    }
                },

                takeSnapshot() {
                    this.isLoading = true;
                    this.loadingText = 'Menyimpan Absensi...';

                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    const ctx = canvas.getContext('2d');

                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;

                    // Fix mirror kamera depan: flip horizontal lalu restore sebelum watermark
                    ctx.save();
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    ctx.restore();

                    // Box Transparan untuk teks watermark
                    ctx.fillStyle = "rgba(0, 0, 0, 0.6)";
                    ctx.fillRect(10, canvas.height - 110, canvas.width - 20, 100);

                    // Watermark Sekolah & Waktu
                    ctx.font = "bold 24px Arial";
                    ctx.fillStyle = "white";

                    const dateObj = new Date();
                    const timeStr = dateObj.toLocaleTimeString('id-ID');
                    const dateStr = dateObj.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    ctx.fillText("SMK PGRI 1 GIRI - GRISA PKL", 20, canvas.height - 80);
                    ctx.font = "20px Arial";
                    ctx.fillText(`Waktu: ${dateStr} - ${timeStr}`, 20, canvas.height - 50);

                    // Logika Cetak Lokasi di Foto
                    if (this.lat !== null && this.lng !== null && this.lat !== 0 && this.lng !== 0) {
                        ctx.fillText(`Lokasi: ${this.lat.toFixed(5)}, ${this.lng.toFixed(5)}`, 20,
                            canvas.height - 20);
                    } else {
                        ctx.fillText(`Lokasi: Tidak Terdeteksi (Mode Bebas)`, 20, canvas.height - 20);
                    }

                    const base64Photo = canvas.toDataURL('image/jpeg', 0.8);

                    // Kirim ke backend (Kalau isRadiusEnabled false, di backend otomatis lolos verifikasi)
                    this.$wire.submitAttendance(base64Photo, this.lat, this.lng).then(() => {
                        this.closeCamera();
                    });
                }
            }))
        })
    </script>
</div>
