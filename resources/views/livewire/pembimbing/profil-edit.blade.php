<div class="relative w-full pb-6 px-1">

    <div class="flex items-center gap-3 mb-4 mt-2">
        <a href="{{ route('pembimbing.profil') }}" wire:navigate
            class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 text-slate-600 hover:bg-slate-100 active:scale-95 transition-all shrink-0">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex-1 overflow-hidden">
            <h2 class="text-[20px] font-extrabold text-slate-800 tracking-tight leading-tight truncate">Edit Data Guru
            </h2>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-5 flex flex-col gap-4">

        <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Nomor HP / WA</label>
            <input type="text" wire:model="phone"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] outline-none">
            @error('phone')
                <span class="text-[11px] text-red-500 font-bold pl-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Mata
                Pelajaran</label>
            <input type="text" wire:model="subject"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-[14px] rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] outline-none">
            @error('subject')
                <span class="text-[11px] text-red-500 font-bold pl-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5 mt-2" x-data="{
            isDrawing: false,
            ctx: null,
            isEmpty: true,
            init() {
                setTimeout(() => {
                    const canvas = this.$refs.canvas;
                    if (canvas) {
                        canvas.width = canvas.offsetWidth;
                        canvas.height = canvas.offsetHeight;
                        this.ctx = canvas.getContext('2d');
                        this.ctx.lineWidth = 3;
                        this.ctx.lineCap = 'round';
                        this.ctx.lineJoin = 'round';
                        this.ctx.strokeStyle = '#000000';
                    }
                }, 300);
            },
            getCoordinates(e) {
                const rect = this.$refs.canvas.getBoundingClientRect();
                let x, y;
                if (e.touches && e.touches.length > 0) {
                    x = e.touches[0].clientX - rect.left;
                    y = e.touches[0].clientY - rect.top;
                } else {
                    x = e.clientX - rect.left;
                    y = e.clientY - rect.top;
                }
                return { x, y };
            },
            startDrawing(e) {
                this.isDrawing = true;
                this.isEmpty = false;
                const { x, y } = this.getCoordinates(e);
                this.ctx.beginPath();
                this.ctx.moveTo(x, y);
            },
            draw(e) {
                if (!this.isDrawing) return;
                const { x, y } = this.getCoordinates(e);
                this.ctx.lineTo(x, y);
                this.ctx.stroke();
            },
            stopDrawing() {
                if (this.isDrawing) {
                    this.ctx.closePath();
                    this.isDrawing = false;
                }
            },
            clearPad() {
                this.ctx.clearRect(0, 0, this.$refs.canvas.width, this.$refs.canvas.height);
                this.isEmpty = true;
            },
            save() {
                let base64 = this.isEmpty ? null : this.$refs.canvas.toDataURL('image/png');
                this.$wire.saveProfile(base64);
            }
        }">

            <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Tanda Tangan Digital
                Baru</label>
            <div class="relative">
                <canvas x-ref="canvas" @mousedown="startDrawing" @mousemove="draw" @mouseup="stopDrawing"
                    @mouseleave="stopDrawing" @touchstart.prevent="startDrawing" @touchmove.prevent="draw"
                    @touchend.prevent="stopDrawing"
                    class="w-full h-48 border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 touch-none cursor-crosshair">
                </canvas>

                <button type="button" @click="clearPad"
                    class="absolute top-2 right-2 bg-red-100 text-red-600 px-3 py-1 rounded-lg text-[10px] font-bold active:scale-95 transition-transform flex items-center gap-1 shadow-sm">
                    <span class="material-symbols-outlined text-[14px]">delete</span> Bersihkan
                </button>
            </div>
            <p class="text-[10px] font-medium text-slate-400 pl-1 mt-1 leading-relaxed">
                *Coret di dalam kotak di atas untuk membuat tanda tangan baru. <br>
                <span class="font-bold text-amber-500">Biarkan kosong jika tidak mengubah TTD!</span>
            </p>

            <div class="mt-4">
                <button type="button" @click="save"
                    class="w-full h-[52px] bg-[#3525cd] hover:bg-[#2c1eb3] text-white text-[15px] font-bold rounded-[1.25rem] shadow-lg flex items-center justify-center gap-2 active:scale-95 transition-all">
                    <span wire:loading.remove wire:target="saveProfile">Simpan Perubahan</span>
                    <span wire:loading wire:target="saveProfile" class="animate-pulse">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>
