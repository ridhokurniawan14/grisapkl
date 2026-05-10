<div class="relative w-full pb-6 min-h-[calc(100vh-4rem)] pt-2" x-data="{
    originalStatus: '{{ $originalStatus }}',
    currentStatus: '{{ $originalStatus }}',
    isCameraOpen: false,
    isLoading: false,
    isSaving: false,
    loadingText: '',
    stream: null,
    lat: null,
    lng: null,
    showErrorModal: false,
    errorMessage: '',

    requiresCamera() {
        return this.originalStatus !== 'Hadir' && this.currentStatus === 'Hadir';
    },

    photoRequired() {
        return ['Hadir', 'Izin', 'Sakit'].includes(this.currentStatus);
    },

    showError(msg) {
        this.errorMessage = msg;
        this.showErrorModal = true;
        this.closeCamera();
    },

    async saveWithoutCamera() {
        this.isSaving = true;
        try {
            await this.$wire.updateWithoutCamera();
        } catch (e) {
            this.isSaving = false;
        }
    },

    async openCamera() {
        this.isCameraOpen = true;
        this.isLoading = true;
        this.loadingText = 'MEMBACA LOKASI...';

        await this.$nextTick();

        // ── STEP 1: Geolocation ─────────────────────────────────────────────
        let pos;
        try {
            pos = await new Promise((resolve, reject) =>
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 20000,
                    maximumAge: 0
                })
            );
        } catch (e) {
            if (e.code === 1) return this.showError('Akses GPS ditolak browser. Izinkan lokasi di address bar.');
            if (e.code === 2) return this.showError('Sinyal GPS tidak ditemukan. Pindah ke tempat terbuka.');
            if (e.code === 3) return this.showError('GPS Timeout. Coba refresh halaman.');
            return this.showError('Gagal membaca GPS. Pastikan lokasi aktif di perangkat.');
        }

        this.lat = pos.coords.latitude;
        this.lng = pos.coords.longitude;

        if (pos.coords.accuracy > 4000) {
            return this.showError('Akurasi GPS terlalu rendah. Matikan Fake GPS atau pindah ke area terbuka.');
        }

        // ── STEP 2: Verifikasi Radius ke Server ─────────────────────────────
        this.loadingText = 'VERIFIKASI LOKASI...';
        try {
            const ok = await this.$wire.verifyLocation(this.lat, this.lng);
            if (!ok) return this.showError('Anda di luar radius DUDIKA. Masuk area PKL terlebih dahulu.');
        } catch (e) {
            console.error('verifyLocation:', e);
            return this.showError('Gagal verifikasi ke server. Periksa koneksi dan coba lagi.');
        }

        // ── STEP 3: Ambil stream kamera ─────────────────────────────────────
        this.loadingText = 'MEMBUKA KAMERA...';

        if (!navigator.mediaDevices?.getUserMedia) {
            return this.showError('Kamera tidak tersedia. Pastikan akses via HTTPS dan izin kamera diberikan.');
        }

        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } }
            });
        } catch (e) {
            if (e.name === 'NotAllowedError') return this.showError('Akses kamera ditolak. Izinkan kamera di address bar browser.');
            if (e.name === 'NotFoundError') return this.showError('Kamera tidak ditemukan di perangkat ini.');
            if (e.name === 'NotReadableError') return this.showError('Kamera dipakai aplikasi lain. Tutup lalu coba lagi.');
            return this.showError('Gagal buka kamera: ' + (e.message || e.name));
        }

        // ── STEP 4: Pasang stream ────────────────────────────────────────────
        this.$refs.video.srcObject = this.stream;
        try { await this.$refs.video.play(); } catch (_) {}
        this.isLoading = false;
    },

    closeCamera() {
        this.isCameraOpen = false;
        this.isLoading = false;
        if (this.stream) {
            this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
        }
        if (this.$refs.video) this.$refs.video.srcObject = null;
    },

    takeSnapshot() {
        this.isLoading = true;
        this.loadingText = 'MENYIMPAN...';

        const video = this.$refs.video;
        const canvas = this.$refs.canvas;
        const ctx = canvas.getContext('2d');

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        ctx.fillStyle = 'rgba(0,0,0,0.65)';
        ctx.fillRect(10, canvas.height - 110, canvas.width - 20, 100);
        ctx.fillStyle = 'white';
        ctx.font = 'bold 24px Arial';
        const d = new Date();
        const tStr = d.toLocaleTimeString('id-ID');
        const dStr = d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        ctx.fillText('SMK PGRI 1 GIRI - GRISA PKL', 20, canvas.height - 80);
        ctx.font = '20px Arial';
        ctx.fillText('Waktu: ' + dStr + ', ' + tStr, 20, canvas.height - 50);
        ctx.fillText('Lokasi: ' + this.lat.toFixed(5) + ', ' + this.lng.toFixed(5), 20, canvas.height - 20);

        const photo = canvas.toDataURL('image/jpeg', 0.85);

        this.$wire.updateWithCamera(photo, this.lat, this.lng)
            .then(ok => {
                if (ok) window.location.href = '{{ route('siswa.jurnal') }}';
                else this.closeCamera();
            })
            .catch(err => {
                console.error(err);
                this.showError('Gagal menyimpan ke server. Periksa koneksi dan coba lagi.');
            });
    }
}">

    {{-- ══ Modal Error ══════════════════════════════════════════════════════ --}}
    <div x-show="showErrorModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-cloak
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
        <div class="bg-white w-full max-w-[340px] rounded-[2rem] p-6 flex flex-col items-center text-center shadow-2xl">
            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-red-500 text-[36px]">photo_camera</span>
            </div>
            <h3 class="text-[20px] font-bold text-slate-800 mb-2">Gagal Absen!</h3>
            <p class="text-[13px] text-slate-500 mb-6 leading-relaxed" x-text="errorMessage"></p>
            <button @click="showErrorModal = false"
                class="w-full h-[48px] bg-red-500 text-white text-[15px] font-bold rounded-[1.25rem] active:scale-95 transition-all">
                Mengerti
            </button>
        </div>
    </div>

    {{-- ══ Overlay Kamera ═══════════════════════════════════════════════════ --}}
    <div x-show="isCameraOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak
        class="fixed inset-0 z-[9998] bg-black flex flex-col">

        <div
            class="absolute top-0 inset-x-0 z-10 px-4 pt-4 pb-10 flex justify-between items-center text-white bg-gradient-to-b from-black/80 to-transparent">
            <h3 class="font-semibold text-[15px]">Verifikasi Ulang Wajah</h3>
            <button @click="closeCamera()" class="p-2 bg-white/20 rounded-full active:scale-95">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="relative flex-1 overflow-hidden">
            <video x-ref="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
            <canvas x-ref="canvas" class="hidden"></canvas>

            <div x-show="isLoading"
                class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center text-white z-20">
                <svg class="animate-spin h-12 w-12 text-white mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4" />
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                <p x-text="loadingText" class="text-sm font-bold tracking-widest"></p>
            </div>

            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                <div
                    class="w-[220px] h-[300px] rounded-[110px/150px] border-2 border-dashed border-white/80 shadow-[0_0_0_9999px_rgba(0,0,0,0.55)]">
                </div>
            </div>
        </div>

        <div class="py-10 flex justify-center bg-black">
            <button @click="takeSnapshot()" :disabled="isLoading"
                class="w-20 h-20 bg-white rounded-full border-[6px] border-slate-300 flex items-center justify-center shadow-lg active:scale-90 transition-transform disabled:opacity-40">
                <div class="w-14 h-14 bg-white rounded-full border border-slate-100 shadow-inner"></div>
            </button>
        </div>
    </div>

    {{-- ══ Konten Halaman ═══════════════════════════════════════════════════ --}}

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-5 pt-2">
        <a href="{{ route('siswa.jurnal') }}" wire:navigate
            class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 text-slate-600 hover:bg-slate-100 shrink-0">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex-1 overflow-hidden">
            <h2 class="text-[20px] font-extrabold text-slate-800 truncate">Edit Jurnal</h2>
            <p class="text-[12px] font-bold text-[#3525cd] truncate">
                {{ \Carbon\Carbon::parse($journal->date)->isoFormat('dddd, D MMMM YYYY') }}
            </p>
        </div>
    </div>

    {{-- Banner revisi --}}
    @if ($journal->is_valid === 0 || $journal->is_valid === false)
        <div class="bg-red-50 border border-red-200 rounded-[1rem] p-3 mb-4 flex gap-3 items-start shadow-sm">
            <span class="material-symbols-outlined text-red-500 mt-0.5 text-[20px]">error</span>
            <p class="text-[12px] text-red-700 leading-relaxed font-medium">
                Jurnal ini butuh revisi. Silakan perbaiki sesuai arahan pembimbing.
            </p>
        </div>
    @endif

    {{-- Form card --}}
    <section class="bg-white rounded-[1.5rem] p-5 shadow-sm border border-slate-200 flex flex-col gap-4">

        {{-- Status dropdown --}}
        <div class="flex flex-col gap-1.5 relative">
            <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">
                Status Kehadiran
            </label>
            <select wire:model="attend_status" x-model="currentStatus"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 px-4 py-3 h-12 shadow-inner appearance-none !bg-none cursor-pointer">
                <option value="Hadir">Hadir di Lokasi PKL</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
                <option value="Libur">Libur / Tanggal Merah</option>
            </select>
            <div class="absolute inset-y-0 right-0 top-[22px] flex items-center pr-3 pointer-events-none">
                <span class="material-symbols-outlined text-slate-400">expand_more</span>
            </div>
        </div>

        {{-- Info wajib selfie --}}
        <div x-show="requiresCamera()" x-cloak x-transition
            class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex gap-2.5 items-start">
            <span class="material-symbols-outlined text-amber-600 text-[18px] mt-0.5">add_a_photo</span>
            <p class="text-[11px] text-amber-800 font-medium">
                Status dirubah ke <b>Hadir</b>. Wajib ambil <i>selfie</i> dan verifikasi GPS.
            </p>
        </div>

        {{-- Form detail --}}
        <div x-show="currentStatus !== 'Libur'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="flex flex-col gap-4 mt-2">

                {{-- Foto selfie lama --}}
                @if ($journal->attendance_photo_path)
                    <div x-show="originalStatus === 'Hadir' && currentStatus === 'Hadir'" class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">
                            Selfie Kehadiran Sebelumnya
                        </label>
                        <div
                            class="w-full h-32 bg-slate-100 rounded-xl border border-slate-200 overflow-hidden relative shadow-inner">
                            <img src="{{ asset('storage/' . $journal->attendance_photo_path) }}"
                                class="w-full h-full object-cover">
                            <div class="absolute bottom-2 right-2 bg-black/50 backdrop-blur-sm px-2 py-1 rounded-md">
                                <span class="text-[10px] text-white font-semibold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">verified</span> Terverifikasi
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ── Upload Foto Kegiatan / Surat ──────────────────────── --}}
                <div class="flex flex-col gap-1.5">
                    <label
                        class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1 flex items-center gap-1">
                        <span x-text="currentStatus === 'Hadir' ? 'Foto Kegiatan' : 'Bukti Surat'"></span>
                        {{-- Tanda wajib — tampil jika status bukan Libur dan belum ada foto lama --}}
                        @if (!$journal->photo_path)
                            <span x-show="photoRequired()" class="text-red-500 text-[13px] font-black">*</span>
                        @endif
                    </label>

                    <label
                        class="w-full h-36 bg-slate-50 rounded-xl border-2 border-dashed
                                  {{ $errors->has('activityPhoto') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}
                                  flex flex-col items-center justify-center relative overflow-hidden
                                  cursor-pointer hover:bg-slate-100 transition-colors">
                        @if ($activityPhoto)
                            <img src="{{ $activityPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif ($journal->photo_path)
                            <img src="{{ asset('storage/' . $journal->photo_path) }}"
                                class="w-full h-full object-cover">
                        @else
                            <span
                                class="material-symbols-outlined text-[32px] {{ $errors->has('activityPhoto') ? 'text-red-400' : 'text-slate-400' }} mb-1">
                                add_photo_alternate
                            </span>
                            <span
                                class="text-[12px] font-semibold {{ $errors->has('activityPhoto') ? 'text-red-500' : 'text-slate-500' }}">
                                {{ $errors->has('activityPhoto') ? 'Foto wajib diupload!' : 'Pilih gambar...' }}
                            </span>
                        @endif
                        <input type="file" wire:model="activityPhoto" class="hidden" accept="image/*"
                            capture="environment">
                    </label>

                    {{-- Loading upload --}}
                    <div wire:loading wire:target="activityPhoto"
                        class="text-[11px] text-[#3525cd] mt-1 pl-1 font-bold animate-pulse">
                        Mengupload foto...
                    </div>

                    {{-- Pesan error dari server --}}
                    @error('activityPhoto')
                        <div class="flex items-center gap-1.5 mt-0.5 pl-1">
                            <span class="material-symbols-outlined text-red-500 text-[14px]">error</span>
                            <span class="text-[11px] text-red-500 font-bold">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Textarea aktivitas --}}
                <div class="flex flex-col gap-1.5">
                    <label
                        class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1 flex items-center gap-1">
                        Keterangan / Aktivitas
                        <span x-show="photoRequired()" class="text-red-500 text-[13px] font-black">*</span>
                    </label>
                    <textarea wire:model="activity"
                        class="w-full bg-slate-50 border {{ $errors->has('activity') ? 'border-red-400' : 'border-slate-200' }} text-slate-800 text-[14px] rounded-xl focus:ring-2 focus:ring-[#3525cd]/20 px-4 py-3 resize-none h-32 shadow-inner"
                        placeholder="Tulis rincian kegiatan / alasan di sini..."></textarea>
                    @error('activity')
                        <div class="flex items-center gap-1.5 mt-0.5 pl-1">
                            <span class="material-symbols-outlined text-red-500 text-[14px]">error</span>
                            <span class="text-[11px] text-red-500 font-bold">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

            </div>
        </div>
    </section>

    {{-- ── Tombol aksi ─────────────────────────────────────────────────────── --}}
    <div class="mt-4 relative" style="height:52px;">

        {{-- Simpan Perubahan --}}
        <div class="absolute inset-0 transition-all duration-300"
            :class="requiresCamera() ?
                'opacity-0 pointer-events-none translate-y-1' :
                'opacity-100 pointer-events-auto translate-y-0'">
            <button @click="saveWithoutCamera()" :disabled="isSaving" type="button"
                class="relative w-full h-[52px] bg-[#3525cd] hover:bg-[#2c1eb3] disabled:opacity-75 text-white font-bold rounded-[1.25rem] shadow-lg overflow-hidden active:scale-95 transition-all">
                <span x-show="!isSaving" x-transition:leave="transition duration-100"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="absolute inset-0 flex items-center justify-center gap-2 text-[15px]">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </span>
                <span x-show="isSaving" x-transition:enter="transition duration-150"
                    x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
                    class="absolute inset-0 flex items-center justify-center">
                    <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                </span>
            </button>
        </div>

        {{-- Ambil Selfie --}}
        <div class="absolute inset-0 transition-all duration-300"
            :class="requiresCamera() ?
                'opacity-100 pointer-events-auto translate-y-0' :
                'opacity-0 pointer-events-none translate-y-1'">
            <button @click="openCamera()" type="button"
                class="w-full h-[52px] bg-green-600 hover:bg-green-700 text-white text-[15px] font-bold rounded-[1.25rem] shadow-md flex items-center justify-center gap-2 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                Ambil Selfie & Simpan
            </button>
        </div>
    </div>

</div>
