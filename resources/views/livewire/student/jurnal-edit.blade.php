<div class="relative w-full pb-6 min-h-[calc(100vh-4rem)] pt-2" x-data="{
    originalStatus: '{{ $originalStatus }}',
    currentStatus: '{{ $originalStatus }}',
    isCameraOpen: false,
    isLoading: false,
    loadingText: '',
    stream: null,
    lat: null,
    lng: null,
    showErrorModal: false,
    errorMessage: '',

    requiresCamera() {
        if (this.currentStatus === 'Libur') return false;
        // Kamera HANYA DIWAJIBKAN jika sebelumnya BUKAN Hadir, dan SEKARANG diubah ke Hadir
        return this.originalStatus !== 'Hadir' && this.currentStatus === 'Hadir';
    },

    async openCamera() {
        this.isCameraOpen = true;
        this.isLoading = true;
        this.loadingText = 'Mengecek Radius & Lokasi...';

        try {
            const pos = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            });

            // Toleransi sama persis seperti di Absensi.php
            if (pos.coords.accuracy > 150) {
                this.errorMessage = 'Akurasi GPS bermasalah atau terdeteksi aplikasi Fake GPS. Pastikan Anda berada di luar ruangan dan mematikan aplikasi pihak ketiga!';
                this.showErrorModal = true;
                this.closeCamera();
                return;
            }

            this.lat = pos.coords.latitude;
            this.lng = pos.coords.longitude;

            // CEK RADIUS KE SERVER LIVEWIRE
            let isWithinRadius = await this.$wire.verifyLocation(this.lat, this.lng);

            if (!isWithinRadius) {
                this.errorMessage = 'Lokasi Anda saat ini berada di luar radius DUDIKA yang diizinkan sekolah. Silakan masuk ke area tempat PKL untuk melakukan absensi.';
                this.showErrorModal = true;
                this.closeCamera();
                return;
            }

            this.loadingText = 'Membuka Kamera...';
            this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            this.$refs.video.srcObject = this.stream;
            this.isLoading = false;

        } catch (error) {
            console.error('Kamera Error:', error);
            this.errorMessage = 'Gagal mengakses Lokasi atau Kamera. Pastikan izin GPS dan Kamera pada browser Anda sudah diberikan!';
            this.showErrorModal = true;
            this.closeCamera();
        }
    },

    closeCamera() {
        this.isCameraOpen = false;
        this.isLoading = false;
        if (this.stream) this.stream.getTracks().forEach(t => t.stop());
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
        ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
        ctx.fillRect(10, canvas.height - 110, canvas.width - 20, 100);
        ctx.font = 'bold 24px Arial';
        ctx.fillStyle = 'white';

        const dObj = new Date();
        const tStr = dObj.toLocaleTimeString('id-ID');
        const dStr = dObj.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

        ctx.fillText('SMK PGRI 1 GIRI - GRISA PKL', 20, canvas.height - 80);
        ctx.font = '20px Arial';
        ctx.fillText(`Waktu: ${dStr} - ${tStr}`, 20, canvas.height - 50);
        ctx.fillText(`Lokasi: ${this.lat.toFixed(5)}, ${this.lng.toFixed(5)}`, 20, canvas.height - 20);

        const base64Photo = canvas.toDataURL('image/jpeg', 0.8);

        this.$wire.updateWithCamera(base64Photo, this.lat, this.lng).then((success) => {
            if (success) window.location.href = '{{ route('siswa.jurnal') }}';
            this.closeCamera();
        }).catch((err) => {
            console.error(err);
            this.errorMessage = 'Terjadi kesalahan internal server saat menyimpan data.';
            this.showErrorModal = true;
            this.closeCamera();
        });
    }
}">

    <div class="flex items-center gap-3 mb-5 pt-2">
        <a href="{{ route('siswa.jurnal') }}" wire:navigate
            class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 text-slate-600 hover:bg-slate-100 shrink-0"><span
                class="material-symbols-outlined">arrow_back</span></a>
        <div class="flex-1 overflow-hidden">
            <h2 class="text-[20px] font-extrabold text-slate-800 truncate">Edit Jurnal</h2>
            <p class="text-[12px] font-bold text-[#3525cd] truncate">
                {{ \Carbon\Carbon::parse($journal->date)->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
    </div>

    @if ($journal->is_valid === 0 || $journal->is_valid === false)
        <div class="bg-red-50 border border-red-200 rounded-[1rem] p-3 mb-4 flex gap-3 items-start shadow-sm">
            <span class="material-symbols-outlined text-red-500 mt-0.5 text-[20px]">error</span>
            <p class="text-[12px] text-red-700 leading-relaxed font-medium">Jurnal ini butuh revisi. Silakan perbaiki
                data sesuai arahan pembimbing.</p>
        </div>
    @endif

    <section
        class="bg-white rounded-[1.5rem] p-5 shadow-sm border border-slate-200 flex flex-col gap-4 relative overflow-hidden">

        <div class="flex flex-col gap-1.5 relative group">
            <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Status
                Kehadiran</label>
            <select wire:model="attend_status" x-model="currentStatus"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 px-4 py-3 h-12 shadow-inner appearance-none !bg-none cursor-pointer">
                <option value="Hadir">Hadir di Lokasi PKL</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
                <option value="Libur">Libur / Tanggal Merah</option>
            </select>
            <div class="absolute inset-y-0 right-0 top-[22px] flex items-center pr-3 pointer-events-none"><span
                    class="material-symbols-outlined text-slate-400">expand_more</span></div>
        </div>

        <div x-show="requiresCamera()" x-cloak x-transition
            class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex gap-2.5 items-start">
            <span class="material-symbols-outlined text-amber-600 text-[18px] mt-0.5">add_a_photo</span>
            <p class="text-[11px] text-amber-800 font-medium">Status berubah ke Hadir. Anda diwajibkan mengambil foto
                <i>selfie</i> kehadiran dan verifikasi radius lokasi PKL.
            </p>
        </div>

        <div x-show="currentStatus !== 'Libur'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="flex flex-col gap-4 mt-2">

                @if ($journal->attendance_photo_path)
                    <div x-show="originalStatus === 'Hadir' && currentStatus === 'Hadir'"
                        class="flex flex-col gap-1.5 mb-2">
                        <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Selfie
                            Kehadiran Anda</label>
                        <div
                            class="w-full h-32 bg-slate-100 rounded-xl border border-slate-200 overflow-hidden relative shadow-inner">
                            <img src="{{ asset('storage/' . $journal->attendance_photo_path) }}"
                                class="w-full h-full object-cover">
                            <div class="absolute bottom-2 right-2 bg-black/50 backdrop-blur-sm px-2 py-1 rounded-md">
                                <span class="text-[10px] text-white font-semibold flex items-center gap-1"><span
                                        class="material-symbols-outlined text-[12px]">verified</span> Terverifikasi
                                    Sistem</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1"
                        x-text="currentStatus === 'Hadir' ? 'Ubah Foto Kegiatan' : 'Upload Bukti Surat'"></label>
                    <label
                        class="w-full h-36 bg-slate-50 rounded-xl border-2 border-dashed border-slate-300 flex flex-col items-center justify-center relative overflow-hidden cursor-pointer hover:bg-slate-100 transition-colors">
                        @if ($activityPhoto)
                            <img src="{{ $activityPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                            <div
                                class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                <span class="text-white text-xs font-semibold">Ubah Foto Pilihan</span>
                            </div>
                        @elseif($journal->photo_path)
                            <img src="{{ asset('storage/' . $journal->photo_path) }}"
                                class="w-full h-full object-cover">
                            <div
                                class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
                                <span class="material-symbols-outlined text-white mb-1 text-[28px]">cloud_upload</span>
                                <span class="text-white text-[11px] font-bold">Ganti dengan Foto Baru</span>
                            </div>
                        @else
                            <span
                                class="material-symbols-outlined text-[32px] text-slate-400 mb-1">add_photo_alternate</span>
                            <span class="text-[12px] font-semibold text-slate-500">Pilih gambar baru...</span>
                        @endif
                        <input type="file" wire:model="activityPhoto" class="hidden" accept="image/*"
                            capture="environment">
                    </label>
                    <div wire:loading wire:target="activityPhoto"
                        class="text-[11px] text-[#3525cd] mt-1 pl-1 font-bold animate-pulse">Mengupload foto...</div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Keterangan /
                        Aktivitas</label>
                    <textarea wire:model="activity"
                        class="w-full bg-slate-50 border {{ $errors->has('activity') ? 'border-red-400' : 'border-slate-200' }} text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 px-4 py-3 resize-none h-32 shadow-inner"
                        placeholder="Tulis rincian kegiatan / alasan di sini..."></textarea>
                    @error('activity')
                        <span class="text-[11px] text-red-500 block pl-1 font-bold">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </section>

    <div class="mt-4 w-full">
        <button x-show="!requiresCamera()" wire:click="updateWithoutCamera" type="button"
            class="w-full h-[52px] bg-[#3525cd] hover:bg-[#2c1eb3] text-white text-[15px] font-bold rounded-[1.25rem] shadow-lg flex items-center justify-center gap-2 active:scale-95 transition-all">
            <span wire:loading.remove wire:target="updateWithoutCamera">Simpan Perubahan</span>
            <span wire:loading wire:target="updateWithoutCamera" class="flex items-center gap-2"><svg
                    class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg> Menyimpan...</span>
        </button>

        <button x-show="requiresCamera()" x-cloak @click="openCamera()" type="button"
            class="w-full h-[52px] bg-green-600 hover:bg-green-700 text-white text-[15px] font-bold rounded-[1.25rem] shadow-md flex items-center justify-center gap-2 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-[20px]">photo_camera</span> Ambil Selfie & Simpan
        </button>
    </div>

    <template x-teleport="body">
        <div x-show="showErrorModal" x-cloak
            class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
            <div
                class="bg-white w-full max-w-[320px] rounded-[2rem] p-6 flex flex-col items-center text-center shadow-2xl relative">
                <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mb-4"><span
                        class="material-symbols-outlined text-red-500 text-[36px]">location_off</span></div>
                <h3 class="text-[20px] font-bold text-slate-800 mb-2">Gagal Absen!</h3>
                <p class="text-[13px] text-slate-500 mb-6 leading-relaxed" x-text="errorMessage"></p>
                <button @click="showErrorModal = false"
                    class="w-full h-[48px] bg-red-500 text-white text-[15px] font-bold rounded-[1.25rem] active:scale-95">Mengerti</button>
            </div>
        </div>

        <div x-show="isCameraOpen" x-cloak class="fixed inset-0 z-[9999] bg-black flex flex-col justify-between">
            <div
                class="p-4 flex justify-between items-center text-white bg-gradient-to-b from-black/80 to-transparent z-30">
                <h3 class="font-semibold text-[15px]">Verifikasi Ulang Wajah</h3>
                <button @click="closeCamera()" class="p-2 bg-white/20 rounded-full active:scale-95"><span
                        class="material-symbols-outlined text-[20px]">close</span></button>
            </div>
            <div class="relative flex-1 flex items-center justify-center overflow-hidden">
                <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                <canvas x-ref="canvas" class="hidden"></canvas>
                <div x-show="isLoading"
                    class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center text-white z-40">
                    <span class="material-symbols-outlined animate-spin text-[48px] mb-3">refresh</span>
                    <p x-text="loadingText" class="text-sm font-bold tracking-widest"></p>
                </div>
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                    <div
                        class="w-[220px] h-[300px] border-2 border-dashed border-white/80 rounded-[110px/150px] shadow-[0_0_0_4000px_rgba(0,0,0,0.6)]">
                    </div>
                </div>
            </div>
            <div class="p-8 pb-12 flex justify-center items-center bg-black z-30">
                <button @click="takeSnapshot()" :disabled="isLoading"
                    class="w-20 h-20 bg-white rounded-full border-[6px] border-slate-300 active:scale-90 transition-transform disabled:opacity-50 flex items-center justify-center">
                    <div class="w-14 h-14 bg-white rounded-full shadow-inner border border-slate-100"></div>
                </button>
            </div>
        </div>
    </template>
</div>
