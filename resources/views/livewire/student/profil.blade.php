@php
    $isPribadiIncomplete =
        empty($student->nisn) ||
        empty($student->nis) ||
        empty($student->birth_place) ||
        empty($student->birth_date) ||
        empty($student->religion) ||
        empty($student->gender) ||
        empty($student->phone) ||
        empty($student->address);

    $isAkademikIncomplete = empty($student->studentClass->name);

    $isOrtuIncomplete =
        empty($student->father_name) ||
        empty($student->father_job) ||
        empty($student->mother_name) ||
        empty($student->mother_job) ||
        empty($student->parent_phone) ||
        (empty($student->parent_address) && empty($student->address));

    $gender = $student->gender ?? '-';
    if (in_array(strtolower($gender), ['l', 'laki-laki'])) {
        $genderFull = 'Laki-laki';
    } elseif (in_array(strtolower($gender), ['p', 'perempuan'])) {
        $genderFull = 'Perempuan';
    } else {
        $genderFull = $gender;
    }

    // Helper ucwords untuk data
    $name = $student->name ?? Auth::user()->name;
    $nameFmt = ucwords(strtolower($name));
    $initials = collect(explode(' ', $name))->map(fn($s) => substr($s, 0, 1))->take(2)->join('');
@endphp

<div wire:poll.30s class="relative w-full pb-3">

    {{-- Background biru --}}
    <div class="absolute top-[-80px] -left-4 -right-4 h-[280px] bg-[#3525cd] z-0 rounded-b-[2.5rem]"></div>

    <div class="flex flex-col relative z-10 w-full mt-16">

        {{-- ===================================================== --}}
        {{-- PROFILE CARD                                           --}}
        {{-- ===================================================== --}}
        <section
            class="flex flex-col items-center justify-center bg-white rounded-[2rem] p-6 pt-0 shadow-sm border border-slate-100">

            {{-- ===================================================== --}}
            {{-- AVATAR & TOMBOL ACTION (Dengan Alpine & Modal Kamera) --}}
            {{-- ===================================================== --}}
            <div x-data="profileCamera()" class="-mt-14 relative mb-3 flex flex-col items-center">

                {{-- Bungkus Avatar & Tombol Utama --}}
                <div class="relative">
                    <div
                        class="w-28 h-28 rounded-full border-[5px] border-white overflow-hidden shadow-md bg-slate-100 flex items-center justify-center text-4xl font-bold">
                        @if (!empty($student->avatar))
                            <img src="{{ Storage::url($student->avatar) }}" class="w-full h-full object-cover" />
                        @else
                            <span class="text-[#3525cd] bg-[#e2dfff] w-full h-full flex items-center justify-center">
                                {{ strtoupper($initials) }}
                            </span>
                        @endif
                    </div>

                    {{-- Tombol Utama --}}
                    <button type="button" @click="photoMenu = !photoMenu" @click.away="photoMenu = false"
                        class="absolute bottom-0.5 right-0.5 w-9 h-9 bg-[#3525cd] rounded-full shadow-lg flex items-center justify-center cursor-pointer hover:bg-[#2c1eb3] active:scale-90 transition-all border-2 border-white"
                        title="Ubah Foto Profil">
                        <span class="material-symbols-outlined text-white text-[16px]"
                            style="font-variation-settings:'FILL' 1;">photo_camera</span>
                    </button>

                    {{-- ✅ Popup Menu Pilihan (FIX ANIMASI TRANSLATE) --}}
                    <div x-show="photoMenu" x-cloak x-transition:enter="transition ease-out duration-200 origin-top"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100 origin-top"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute top-full mt-2 left-1/2 -translate-x-1/2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden z-50">
                        <div class="flex flex-col text-[13px] font-medium text-slate-700 divide-y divide-slate-50">

                            {{-- Pilihan 1: Buka Kamera Langsung --}}
                            <button type="button" @click="photoMenu = false; openCamera()"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer active:bg-slate-100 transition-colors w-full text-left">
                                <span class="material-symbols-outlined text-[18px] text-orange-500">photo_camera</span>
                                Buka Kamera
                            </button>

                            {{-- Pilihan 2: Pilih dari Galeri --}}
                            <label for="photo-upload-input" @click="photoMenu = false"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer active:bg-slate-100 transition-colors w-full">
                                <span class="material-symbols-outlined text-[18px] text-[#3525cd]">image</span>
                                Pilih dari Galeri
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Input File untuk Galeri (Bawaan Livewire yang sudah ada) --}}
                <input type="file" id="photo-upload-input" wire:model="newProfilePhoto" accept="image/*"
                    class="hidden" />

                {{-- Loading Indikator --}}
                {{-- <div wire:loading wire:target="newProfilePhoto"
                    class="text-[11px] text-indigo-500 font-semibold mb-1 mt-2 animate-pulse">
                    Mengupload foto...
                </div> --}}

                <div x-show="isLoading" x-cloak
                    class="text-[11px] text-orange-500 font-semibold mb-1 mt-2 animate-pulse" x-text="loadingText">
                </div>

                {{-- ✅ MODAL KAMERA FULL SCREEN (Di-teleport ke root canvas) --}}
                <template x-teleport="#app-canvas">
                    <div x-show="isCameraOpen" x-cloak
                        class="absolute inset-0 z-[999] bg-black flex flex-col justify-center items-center">
                        <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                        <canvas x-ref="canvas" class="hidden"></canvas>

                        {{-- Tombol Tutup --}}
                        <button type="button" @click="closeCamera"
                            class="absolute top-6 right-6 text-white bg-black/50 w-10 h-10 flex items-center justify-center rounded-full active:scale-90 z-[1000]">
                            <span class="material-symbols-outlined">close</span>
                        </button>

                        {{-- Tombol Jepret --}}
                        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-[1000]">
                            <button type="button" @click="takeSnapshot"
                                class="w-16 h-16 bg-white rounded-full border-4 border-slate-300 active:scale-90 transition-transform flex items-center justify-center">
                                <div class="w-12 h-12 bg-white border border-slate-200 rounded-full shadow-inner"></div>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Loading saat upload --}}
            <div wire:loading wire:target="newProfilePhoto"
                class="text-[11px] text-indigo-500 font-semibold mb-1 animate-pulse">
                Mengupload foto...
            </div>

            <div class="text-center">
                <h2 class="text-[18px] font-extrabold text-slate-800 tracking-wide">{{ $nameFmt }}</h2>
                <p class="text-[13px] text-[#3525cd] mt-1 font-bold uppercase tracking-wider">
                    {{ $student->studentClass->name ?? 'Kelas Belum Diatur' }}
                </p>
            </div>
        </section>

        {{-- ===================================================== --}}
        {{-- TAB BAR (Alpine.js)                                    --}}
        {{-- ===================================================== --}}
        <div x-data="{ tab: 'pribadi' }" class="mt-6">

            <div class="flex bg-white/95 backdrop-blur-sm p-1.5 rounded-2xl mb-4 shadow-lg border border-slate-200">
                <button @click="tab = 'pribadi'"
                    :class="tab === 'pribadi'
                        ?
                        'bg-white shadow-sm font-bold @if ($isPribadiIncomplete) text-red-600 @else text-[#3525cd] @endif' :
                        'font-medium @if ($isPribadiIncomplete) text-red-400 hover:text-red-600 @else text-slate-500 hover:text-slate-700 @endif'"
                    class="flex-1 py-2.5 text-[12px] rounded-xl transition-all duration-200 flex items-center justify-center gap-0.5">
                    Data Pribadi @if ($isPribadiIncomplete)
                        <span class="text-red-500 text-lg leading-none -mt-1">*</span>
                    @endif
                </button>

                <button @click="tab = 'akademik'"
                    :class="tab === 'akademik'
                        ?
                        'bg-white shadow-sm font-bold @if ($isAkademikIncomplete) text-red-600 @else text-[#006591] @endif' :
                        'font-medium @if ($isAkademikIncomplete) text-red-400 hover:text-red-600 @else text-slate-500 hover:text-slate-700 @endif'"
                    class="flex-1 py-2.5 text-[12px] rounded-xl transition-all duration-200 flex items-center justify-center gap-0.5">
                    Akademik @if ($isAkademikIncomplete)
                        <span class="text-red-500 text-lg leading-none -mt-1">*</span>
                    @endif
                </button>

                <button @click="tab = 'ortu'"
                    :class="tab === 'ortu'
                        ?
                        'bg-white shadow-sm font-bold @if ($isOrtuIncomplete) text-red-600 @else text-[#a44100] @endif' :
                        'font-medium @if ($isOrtuIncomplete) text-red-400 hover:text-red-600 @else text-slate-500 hover:text-slate-700 @endif'"
                    class="flex-1 py-2.5 text-[12px] rounded-xl transition-all duration-200 flex items-center justify-center gap-0.5">
                    Orang Tua @if ($isOrtuIncomplete)
                        <span class="text-red-500 text-lg leading-none -mt-1">*</span>
                    @endif
                </button>
            </div>

            {{-- ===== TAB: DATA PRIBADI ===== --}}
            <div x-show="tab === 'pribadi'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden divide-y divide-slate-100">

                {{-- Nama Lengkap --}}
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 shrink-0 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings:'FILL' 0;">person</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Nama Lengkap</p>
                        <p class="text-[13px] text-slate-800 font-bold">{{ $nameFmt }}</p>
                    </div>
                </div>

                {{-- NISN & NIS --}}
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 shrink-0 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings:'FILL' 0;">badge</span>
                    </div>
                    <div class="flex-1 grid grid-cols-2 gap-2">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">NISN</p>
                            <p
                                class="text-[13px] {{ empty($student->nisn) ? 'text-red-500' : 'text-slate-800' }} font-bold">
                                {{ $student->nisn ?? 'Belum Diisi' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">NIS</p>
                            <p
                                class="text-[13px] {{ empty($student->nis) ? 'text-red-500' : 'text-slate-800' }} font-bold">
                                {{ $student->nis ?? 'Belum Diisi' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Tempat, Tanggal Lahir --}}
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 shrink-0 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings:'FILL' 0;">cake</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Tempat, Tanggal Lahir</p>
                        <p
                            class="text-[13px] {{ empty($student->birth_place) || empty($student->birth_date) ? 'text-red-500' : 'text-slate-800' }} font-bold">
                            {{ $student->birth_place ? ucwords(strtolower($student->birth_place)) : '?' }},
                            {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->isoFormat('D MMMM YYYY') : 'Belum Diisi' }}
                        </p>
                    </div>
                </div>

                {{-- Agama & Jenis Kelamin --}}
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 shrink-0 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings:'FILL' 0;">account_balance</span>
                    </div>
                    <div class="flex-1 grid grid-cols-2 gap-2">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Agama</p>
                            <p
                                class="text-[13px] {{ empty($student->religion) ? 'text-red-500' : 'text-slate-800' }} font-bold">
                                {{ $student->religion ? ucwords(strtolower($student->religion)) : 'Belum Diisi' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Jenis Kelamin</p>
                            <p
                                class="text-[13px] {{ empty($student->gender) ? 'text-red-500' : 'text-slate-800' }} font-bold">
                                {{ empty($student->gender) ? 'Belum Diisi' : $genderFull }}</p>
                        </div>
                    </div>
                </div>

                {{-- No. WhatsApp --}}
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 shrink-0 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings:'FILL' 0;">call</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-semibold text-slate-400 mb-0.5">No. WhatsApp</p>
                        <p
                            class="text-[13px] {{ empty($student->phone) ? 'text-red-500' : 'text-slate-800' }} font-bold">
                            {{ $student->phone ?? 'Belum Diisi' }}</p>
                    </div>
                </div>

                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 shrink-0 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings:'FILL' 0;">location_on</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Alamat Lengkap</p>
                        <p
                            class="text-[13px] {{ empty($student->address) ? 'text-red-500' : 'text-slate-800' }} font-bold leading-tight">
                            {{ ucwords(strtolower($student->address ?? 'Belum Diisi')) }}</p>
                    </div>
                </div>
            </div>

            {{-- ===== TAB: AKADEMIK ===== --}}
            <div x-show="tab === 'akademik'" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden divide-y divide-slate-100">
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 shrink-0 rounded-full bg-sky-100 flex items-center justify-center text-sky-600">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings:'FILL' 0;">school</span>
                    </div>
                    <div class="flex-1 grid grid-cols-2 gap-2">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Kelas</p>
                            {{-- Kelas tetap uppercase karena format kode seperti XII PH 1 --}}
                            <p
                                class="text-[13px] {{ empty($student->studentClass->name) ? 'text-red-500' : 'text-slate-800' }} font-bold uppercase">
                                {{ $student->studentClass->name ?? 'Belum Diisi' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Tahun Ajaran</p>
                            <p class="text-[13px] text-slate-800 font-bold">2025/2026</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== TAB: ORANG TUA ===== --}}
            <div x-show="tab === 'ortu'" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden divide-y divide-slate-100">

                {{-- Ayah --}}
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 shrink-0 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings:'FILL' 0;">family_restroom</span>
                    </div>
                    <div class="flex-1 grid grid-cols-2 gap-2">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Nama Ayah</p>
                            <p
                                class="text-[13px] {{ empty($student->father_name) ? 'text-red-500' : 'text-slate-800' }} font-bold">
                                {{ $student->father_name ? ucwords(strtolower($student->father_name)) : 'Belum Diisi' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Pekerjaan</p>
                            <p
                                class="text-[13px] {{ empty($student->father_job) ? 'text-red-500' : 'text-slate-800' }} font-bold">
                                {{ $student->father_job ? ucwords(strtolower($student->father_job)) : 'Belum Diisi' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Ibu --}}
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 shrink-0 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings:'FILL' 0;">pregnant_woman</span>
                    </div>
                    <div class="flex-1 grid grid-cols-2 gap-2">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Nama Ibu</p>
                            <p
                                class="text-[13px] {{ empty($student->mother_name) ? 'text-red-500' : 'text-slate-800' }} font-bold">
                                {{ $student->mother_name ? ucwords(strtolower($student->mother_name)) : 'Belum Diisi' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Pekerjaan</p>
                            <p
                                class="text-[13px] {{ empty($student->mother_job) ? 'text-red-500' : 'text-slate-800' }} font-bold">
                                {{ $student->mother_job ? ucwords(strtolower($student->mother_job)) : 'Belum Diisi' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- No. WA Ortu --}}
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 shrink-0 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings:'FILL' 0;">phone_android</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-semibold text-slate-400 mb-0.5">No. WhatsApp Ortu</p>
                        <p
                            class="text-[13px] {{ empty($student->parent_phone) ? 'text-red-500' : 'text-slate-800' }} font-bold">
                            {{ $student->parent_phone ?? 'Belum Diisi' }}</p>
                    </div>
                </div>

                {{-- Alamat Ortu — tidak pakai ucwords --}}
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 shrink-0 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                        <span class="material-symbols-outlined text-[20px]"
                            style="font-variation-settings:'FILL' 0;">home</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Alamat Ortu</p>
                        <p
                            class="text-[13px] {{ empty($student->parent_address) && empty($student->address) ? 'text-red-500' : 'text-slate-800' }} font-bold leading-tight">
                            {{ ucwords(strtolower($student->parent_address ?? ($student->address ?? 'Belum Diisi'))) }}
                        </p>
                    </div>
                </div>
            </div>

        </div>{{-- End Alpine Tab --}}

        {{-- Action Buttons --}}
        <section class="flex flex-col gap-3 mt-6">
            <a href="{{ route('siswa.profil.edit') }}" wire:navigate
                class="w-full h-12 bg-[#3525cd] hover:bg-[#2c1eb3] text-white text-[14px] font-bold rounded-[1rem] shadow-sm flex items-center justify-center gap-2 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[20px]">edit</span> Edit Profil
            </a>

            <a href="{{ route('siswa.profil.password') }}" wire:navigate
                class="w-full h-12 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-[14px] font-bold rounded-[1rem] shadow-sm flex items-center justify-center gap-2 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[20px] text-orange-500">lock_reset</span> Ubah Password
            </a>

            @if ($pklPlacement?->file_laporan_path && $pklPlacement?->pengesah_ks_nama)
                {{-- PERBAIKAN SAKTI UNTUK IOS PWA: Unduh via Javascript Fetch (Blob) --}}
                <div x-data="{
                    isDownloading: false,
                    async forceDownload() {
                        this.isDownloading = true;
                        try {
                            // Fetch file PDF dari server
                            const response = await fetch('{{ route('siswa.laporan.download') }}');
                            if (!response.ok) throw new Error('Gagal mengambil file');
                
                            // Ubah jadi bentuk Blob (Data Mentah)
                            const blob = await response.blob();
                            const url = window.URL.createObjectURL(blob);
                
                            // Bikin elemen link sementara untuk mancing pop-up download
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;
                            a.download = 'Laporan_PKL_{{ str_replace(' ', '_', $name) }}.pdf';
                            document.body.appendChild(a);
                
                            // Paksa klik dan bersihkan
                            a.click();
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);
                        } catch (error) {
                            alert('Gagal mengunduh laporan. Periksa koneksi internet Anda.');
                        } finally {
                            this.isDownloading = false;
                        }
                    }
                }">
                    <button @click="forceDownload()" :disabled="isDownloading"
                        class="w-full h-12 bg-[#e2dfff] hover:bg-[#d0ccff] disabled:opacity-70 text-[#3525cd] text-[14px] font-bold rounded-[1rem] shadow-sm flex items-center justify-center gap-2 active:scale-95 transition-all">

                        <span x-show="!isDownloading" class="material-symbols-outlined text-[20px]">download</span>
                        <span x-show="!isDownloading">Download Laporan Kegiatan</span>

                        {{-- Animasi Loading saat proses Fetch berjalan --}}
                        <span x-show="isDownloading" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-[#3525cd]" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Menyiapkan File...
                        </span>
                    </button>
                </div>
            @else
                <button disabled
                    class="w-full h-12 bg-gray-300 text-gray-500 cursor-not-allowed text-[14px] font-bold rounded-[1rem] shadow-sm flex items-center justify-center gap-2">

                    <span class="material-symbols-outlined text-[20px]">print</span>
                    Laporan Belum Diverifikasi
                </button>
            @endif
        </section>

        {{-- Logout --}}
        <section class="mt-2 mb-6">
            <button wire:click="logout"
                class="w-full h-12 bg-[#ba1a1a] hover:bg-[#a01616] text-white text-[14px] font-bold rounded-[1rem] shadow-sm flex items-center justify-center gap-2 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[20px]">logout</span> Logout
            </button>
        </section>

        <div class="text-center mt-2 mb-2">
            <p class="text-[11px] font-semibold text-slate-400">GrisaPKL Version 1.1</p>
        </div>

    </div>
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('profileCamera', () => ({
            photoMenu: false,
            isCameraOpen: false,
            isLoading: false,
            loadingText: '',
            stream: null,

            async openCamera() {
                this.isCameraOpen = true;
                this.isLoading = true;
                this.loadingText = 'Membuka kamera...';

                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user'
                        } // Buka kamera depan (selfie)
                    });
                    this.$refs.video.srcObject = this.stream;
                    this.isLoading = false;
                } catch (error) {
                    alert('Gagal mengakses kamera. Pastikan izin kamera sudah diberikan!');
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
                this.loadingText = 'Menyimpan foto...';

                const video = this.$refs.video;
                const canvas = this.$refs.canvas;
                const ctx = canvas.getContext('2d');

                // Samakan ukuran canvas dengan video
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Convert gambar ke base64
                const base64Photo = canvas.toDataURL('image/jpeg', 0.8);

                // Kirim hasil base64 ke fungsi Livewire
                this.$wire.saveBase64Photo(base64Photo).then(() => {
                    this.closeCamera();
                });
            }
        }));
    });
</script>
