<div class="flex flex-col w-full relative pb-10" x-data="absensiApp()">

    <!-- Time & Status Section -->
    <div class="flex flex-col items-center justify-center mb-4 text-center">
        <h2 class="text-3xl font-bold text-on-surface mb-1">{{ now()->format('H:i') }} WIB</h2>
        <p class="text-sm text-outline">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>

        @if (!$hasAttendedToday)
            <div
                class="mt-4 bg-surface-container-low px-4 py-2 rounded-full border border-surface-container-high flex items-center gap-2 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-xs font-medium text-on-surface-variant">Siap untuk Absen</span>
            </div>
        @else
            <div
                class="mt-4 bg-surface-container-low px-4 py-2 rounded-full border border-surface-container-high flex items-center gap-2 shadow-sm opacity-80">
                <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                <span class="text-xs font-medium text-on-surface-variant">Sudah Absen Hari Ini</span>
            </div>
        @endif
    </div>

    <!-- Primary Action: Huge Check-in Button -->
    <div class="flex justify-center my-6 relative h-[260px] items-center">
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-[230px] h-[230px] rounded-full border-[3px] border-primary/20"></div>
            <div class="w-[270px] h-[270px] rounded-full border border-primary/10 absolute"></div>
        </div>

        @if (!$hasAttendedToday)
            <button @click="openCamera()"
                class="w-[160px] h-[160px] rounded-full bg-primary text-white flex flex-col items-center justify-center shadow-xl active:scale-95 transition-transform z-10 animate-subtle-pulse border-4 border-surface">
                <span class="material-symbols-outlined text-[56px] mb-1"
                    style="font-variation-settings: 'FILL' 1;">fingerprint</span>
                <span class="text-[15px] font-semibold tracking-wide">Absen Masuk</span>
            </button>
        @else
            <div
                class="w-[160px] h-[160px] rounded-full bg-surface-variant text-outline flex flex-col items-center justify-center shadow-inner z-10 border-4 border-surface">
                <span class="material-symbols-outlined text-[56px] mb-1"
                    style="font-variation-settings: 'FILL' 1;">check_circle</span>
                <span class="text-[15px] font-semibold tracking-wide">Selesai</span>
            </div>
        @endif
    </div>

    <!-- Secondary Actions Bento Grid (KUNCI MATI JIKA SUDAH ABSEN) -->
    <!-- Dihilangkan wire:click dan wire:confirm bawaan, diganti ke @click AlpineJS -->
    <div class="grid grid-cols-3 gap-3 mb-8 relative z-20">
        <button @if (!$hasAttendedToday) @click="openConfirm('Izin')" @endif
            {{ $hasAttendedToday ? 'disabled' : '' }}
            class="flex flex-col items-center justify-center p-3 rounded-2xl shadow-sm border transition-colors h-20 
            {{ $hasAttendedToday ? 'bg-surface-variant text-outline border-transparent' : 'bg-white text-on-surface hover:bg-surface-variant border-outline-variant/30 active:scale-95' }}">
            <span class="material-symbols-outlined mb-1">assignment_late</span>
            <span class="text-xs font-medium">Izin</span>
        </button>

        <button @if (!$hasAttendedToday) @click="openConfirm('Sakit')" @endif
            {{ $hasAttendedToday ? 'disabled' : '' }}
            class="flex flex-col items-center justify-center p-3 rounded-2xl shadow-sm border transition-colors h-20 
            {{ $hasAttendedToday ? 'bg-surface-variant text-outline border-transparent' : 'bg-white text-on-surface hover:bg-surface-variant border-outline-variant/30 active:scale-95' }}">
            <span class="material-symbols-outlined mb-1">medical_services</span>
            <span class="text-xs font-medium">Sakit</span>
        </button>

        <button @if (!$hasAttendedToday) @click="openConfirm('Libur')" @endif
            {{ $hasAttendedToday ? 'disabled' : '' }}
            class="flex flex-col items-center justify-center p-3 rounded-2xl shadow-sm border transition-colors h-20 
            {{ $hasAttendedToday ? 'bg-surface-variant text-outline border-transparent' : 'bg-white text-on-surface hover:bg-surface-variant border-outline-variant/30 active:scale-95' }}">
            <span class="material-symbols-outlined mb-1">event_available</span>
            <span class="text-xs font-medium">Libur</span>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- MODAL CUSTOM KONFIRMASI (IZIN/SAKIT/LIBUR) -->
    <!-- ========================================== -->
    <div x-show="showConfirmModal" x-cloak
        class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
        <div x-show="showConfirmModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4" @click.away="showConfirmModal = false"
            class="bg-surface-container-lowest w-full max-w-[320px] rounded-[2rem] p-6 flex flex-col items-center text-center shadow-2xl border border-white/20 relative">

            <!-- Ikon Dinamis -->
            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 shadow-inner"
                :class="confirmIconBg">
                <span class="material-symbols-outlined text-[36px]" style="font-variation-settings: 'FILL' 1;"
                    :class="confirmIconColor" x-text="confirmIcon"></span>
            </div>

            <!-- Teks Pesan -->
            <h3 class="text-[20px] font-bold text-on-surface mb-2" x-text="confirmTitle"></h3>
            <p class="text-[13px] text-on-surface-variant mb-6 leading-relaxed" x-text="confirmMessage"></p>

            <!-- Aksi Tombol -->
            <div class="flex w-full gap-3">
                <button @click="showConfirmModal = false"
                    class="flex-1 h-[48px] bg-surface-container-highest hover:bg-surface-variant text-on-surface text-[14px] font-semibold rounded-[1.25rem] transition-all active:scale-95">
                    Batal
                </button>
                <button @click="executeAttendance()"
                    class="flex-1 h-[48px] text-white text-[14px] font-semibold rounded-[1.25rem] transition-all active:scale-95 shadow-md"
                    :class="confirmBtnColor">
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL CUSTOM ALERT PENOLAKAN RADIUS        -->
    <!-- ========================================== -->
    <div x-show="showErrorModal" x-cloak
        class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
        <div x-show="showErrorModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4" @click.away="showErrorModal = false"
            class="bg-surface-container-lowest w-full max-w-[320px] rounded-[2rem] p-6 flex flex-col items-center text-center shadow-2xl border border-white/20 relative">

            <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center mb-4 shadow-inner">
                <span class="material-symbols-outlined text-error text-[36px]"
                    style="font-variation-settings: 'FILL' 1;">location_off</span>
            </div>

            <h3 class="text-[20px] font-bold text-on-surface mb-2">Akses Ditolak!</h3>
            <p class="text-[13px] text-on-surface-variant mb-6 leading-relaxed" x-text="errorMessage"></p>

            <button @click="showErrorModal = false"
                class="w-full h-[48px] bg-error hover:bg-red-700 text-white text-[15px] font-semibold rounded-[1.25rem] transition-all active:scale-95 shadow-lg shadow-error/30">
                Mengerti
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL KAMERA                               -->
    <!-- ========================================== -->
    <div x-show="isCameraOpen" x-cloak class="fixed inset-0 z-[9999] bg-black flex flex-col justify-between">
        <div class="p-4 flex justify-between items-center text-white bg-gradient-to-b from-black/80 to-transparent">
            <h3 class="font-semibold">Verifikasi Wajah & Lokasi</h3>
            <button @click="closeCamera()" class="p-2 bg-white/20 rounded-full active:scale-95"><span
                    class="material-symbols-outlined">close</span></button>
        </div>

        <div class="relative flex-1 flex items-center justify-center overflow-hidden">
            <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
            <canvas x-ref="canvas" class="hidden"></canvas>

            <div x-show="isLoading"
                class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center text-white z-20">
                <span class="material-symbols-outlined animate-spin text-[48px] mb-3">refresh</span>
                <p x-text="loadingText" class="text-sm font-semibold"></p>
            </div>

            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-64 h-64 border-2 border-dashed border-white/50 rounded-3xl"></div>
            </div>
        </div>

        <div class="p-8 pb-12 flex justify-center items-center bg-black">
            <button @click="takeSnapshot()" :disabled="isLoading"
                class="w-20 h-20 bg-white rounded-full border-[6px] border-gray-400 active:scale-90 transition-transform disabled:opacity-50"></button>
        </div>
    </div>

    <!-- FORM JURNAL -->
    @if (
        $hasAttendedToday &&
            $todayJournal &&
            $todayJournal->activity === '' &&
            in_array($todayJournal->attend_status, ['Hadir', 'Izin', 'Sakit']))
        <div
            class="bg-surface-container-lowest rounded-2xl p-5 shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-outline-variant/30 mb-8 animate-fade-in-up">
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="w-8 h-8 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">edit_document</span>
                </div>
                <h3 class="text-lg font-bold text-on-surface">Lengkapi Bukti & Jurnal</h3>
            </div>

            <form wire:submit="saveJournal">
                <!-- INPUT FOTO KEGIATAN -->
                <div class="mb-4">
                    <p class="text-[13px] font-medium text-on-surface-variant mb-1.5">Foto
                        {{ $todayJournal->attend_status === 'Hadir' ? 'Kegiatan' : 'Bukti (Surat Dokter/Ortu)' }}</p>
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
                        <input type="file" wire:model="activityPhoto" class="hidden" accept="image/*"
                            capture="environment">
                    </label>
                    <div wire:loading wire:target="activityPhoto" class="text-xs text-primary mt-1">Mengunggah...
                    </div>
                    @error('activityPhoto')
                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- INPUT TEKS KEGIATAN -->
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

    <!-- History Absen Section -->
    <div class="mb-4">
        <div class="flex items-center justify-between mb-3 px-1">
            <h3 class="text-lg font-bold text-on-surface">History Absen</h3>
        </div>

        <div class="flex flex-col gap-3">
            @if ($todayJournal)
                <div
                    class="bg-white p-3.5 rounded-2xl border border-surface-container shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @php
                            $iconBg = 'bg-green-100';
                            $iconColor = 'text-green-700';
                            $iconName = 'login';
                            if ($todayJournal->attend_status == 'Izin') {
                                $iconBg = 'bg-amber-100';
                                $iconColor = 'text-amber-700';
                                $iconName = 'assignment_late';
                            }
                            if ($todayJournal->attend_status == 'Sakit') {
                                $iconBg = 'bg-red-100';
                                $iconColor = 'text-red-700';
                                $iconName = 'medical_services';
                            }
                            if ($todayJournal->attend_status == 'Libur') {
                                $iconBg = 'bg-blue-100';
                                $iconColor = 'text-blue-700';
                                $iconName = 'event_available';
                            }
                        @endphp

                        <div
                            class="{{ $iconBg }} w-10 h-10 rounded-full flex items-center justify-center {{ $iconColor }} overflow-hidden border border-white">
                            @if ($todayJournal->attendance_photo_path)
                                <img src="{{ asset('storage/' . $todayJournal->attendance_photo_path) }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-[20px]"
                                    style="font-variation-settings: 'FILL' 1;">{{ $iconName }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-[14px] font-semibold text-on-surface">{{ $todayJournal->attend_status }}
                            </p>
                            <p class="text-[11px] text-outline">Hari ini •
                                {{ \Carbon\Carbon::parse($todayJournal->time)->format('H:i') }}</p>
                        </div>
                    </div>
                    <span
                        class="{{ $todayJournal->is_valid ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }} px-3 py-1 rounded-full text-[10px] font-bold border">
                        {{ $todayJournal->is_valid ? 'Valid' : 'Invalid' }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- SCRIPT ALPINE.JS -->
    <script>
        document.addEventListener('alpine:init', () => {
            // Ubah nama root data agar lebih merepresentasikan seluruh page
            Alpine.data('absensiApp', () => ({
                isCameraOpen: false,
                isLoading: false,
                loadingText: '',
                stream: null,
                lat: null,
                lng: null,

                showErrorModal: false,
                errorMessage: '',

                // ===================================
                // STATE UNTUK MODAL KONFIRMASI
                // ===================================
                showConfirmModal: false,
                confirmType: '',
                confirmTitle: '',
                confirmMessage: '',
                confirmIcon: '',
                confirmIconBg: '',
                confirmIconColor: '',
                confirmBtnColor: '',

                openConfirm(type) {
                    this.confirmType = type;
                    this.showConfirmModal = true;

                    if (type === 'Izin') {
                        this.confirmTitle = 'Ajukan Izin';
                        this.confirmMessage =
                            'Anda yakin ingin mengajukan izin hari ini? Anda tetap diwajibkan mengunggah foto bukti / surat keterangan.';
                        this.confirmIcon = 'assignment_late';
                        this.confirmIconBg = 'bg-amber-100';
                        this.confirmIconColor = 'text-amber-600';
                        this.confirmBtnColor = 'bg-amber-600 hover:bg-amber-700';
                    } else if (type === 'Sakit') {
                        this.confirmTitle = 'Lapor Sakit';
                        this.confirmMessage =
                            'Semoga cepat sembuh! Yakin ingin lapor sakit? Jangan lupa lampirkan foto surat dokter atau bukti lain pada form.';
                        this.confirmIcon = 'medical_services';
                        this.confirmIconBg = 'bg-red-100';
                        this.confirmIconColor = 'text-red-600';
                        this.confirmBtnColor = 'bg-red-600 hover:bg-red-700';
                    } else if (type === 'Libur') {
                        this.confirmTitle = 'Lapor Libur';
                        this.confirmMessage =
                            'Tandai hari ini sebagai tanggal merah atau hari libur DUDIKA? Anda tidak perlu mengisi jurnal hari ini.';
                        this.confirmIcon = 'event_available';
                        this.confirmIconBg = 'bg-blue-100';
                        this.confirmIconColor = 'text-blue-600';
                        this.confirmBtnColor = 'bg-blue-600 hover:bg-blue-700';
                    }
                },

                executeAttendance() {
                    // Tutup modal
                    this.showConfirmModal = false;
                    // Tembak fungsi backend Livewire
                    this.$wire.markAttendance(this.confirmType);
                },

                // ===================================
                // FUNGSI KAMERA (Tetap)
                // ===================================
                async openCamera() {
                    this.isCameraOpen = true;
                    this.isLoading = true;
                    this.loadingText = 'Mengecek Radius & Lokasi...';

                    try {
                        const pos = await new Promise((resolve, reject) => {
                            navigator.geolocation.getCurrentPosition(resolve, reject, {
                                enableHighAccuracy: true
                            });
                        });
                        this.lat = pos.coords.latitude;
                        this.lng = pos.coords.longitude;

                        let isWithinRadius = await this.$wire.verifyLocation(this.lat, this.lng);

                        if (!isWithinRadius) {
                            this.errorMessage =
                                'Lokasi Anda saat ini berada di luar radius DUDIKA yang diizinkan sekolah. Silakan masuk ke area tempat PKL untuk melakukan absensi.';
                            this.showErrorModal = true;
                            this.closeCamera();
                            return;
                        }

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
                            'Gagal mengakses Lokasi atau Kamera. Pastikan izin GPS dan Kamera pada browser Anda sudah diberikan!';
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
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    ctx.fillStyle = "rgba(0, 0, 0, 0.6)";
                    ctx.fillRect(10, canvas.height - 110, canvas.width - 20, 100);

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
                    ctx.fillText(`Lokasi: ${this.lat.toFixed(5)}, ${this.lng.toFixed(5)}`, 20, canvas
                        .height - 20);

                    const base64Photo = canvas.toDataURL('image/jpeg', 0.8);

                    this.$wire.submitAttendance(base64Photo, this.lat, this.lng).then(() => {
                        this.closeCamera();
                    });
                }
            }))
        })
    </script>
</div>
