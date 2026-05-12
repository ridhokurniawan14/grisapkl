<div class="w-full flex flex-col pb-6 px-1">

    <div class="flex items-center gap-3 mb-6 mt-2">
        <a href="{{ route('dudika.nilai') }}" wire:navigate
            class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 text-slate-600 hover:bg-slate-100 active:scale-95 transition-all shrink-0">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex-1 overflow-hidden">
            <h2 class="text-[20px] font-extrabold text-[#3525cd] tracking-tight leading-tight truncate">Beri Nilai Siswa
            </h2>
        </div>
    </div>

    <section class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 mb-6 relative overflow-hidden">
        <div
            class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full blur-2xl pointer-events-none -mr-10 -mt-10">
        </div>

        <div class="flex items-center gap-4 relative z-10">
            <div class="relative">
                <div
                    class="w-16 h-16 rounded-full bg-indigo-50 border-2 border-indigo-200 flex items-center justify-center overflow-hidden text-[#3525cd] font-bold text-[24px]">
                    @if ($studentData['avatar'])
                        <img src="{{ $studentData['avatar'] }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($studentData['name'], 0, 1)) }}
                    @endif
                </div>
                <div class="absolute -bottom-1 -right-1 bg-emerald-500 w-4 h-4 rounded-full border-2 border-white">
                </div>
            </div>
            <div class="flex-1">
                <h2 class="text-[18px] font-extrabold text-slate-800 leading-tight">{{ $studentData['name'] }}</h2>
                <p class="text-[12px] font-bold text-slate-500 mt-0.5">{{ $studentData['field'] }}</p>
                <div class="mt-2 flex gap-1.5">
                    <span
                        class="bg-indigo-50 text-[#3525cd] px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider border border-indigo-100">{{ $studentData['class'] }}</span>
                </div>
            </div>
        </div>
    </section>

    <div class="flex items-center justify-between mb-3 px-1">
        <h3 class="text-[16px] font-extrabold text-slate-800">Indikator Penilaian</h3>
        <span
            class="text-[11px] text-red-500 font-extrabold italic bg-red-50 px-2 py-0.5 rounded-md border border-red-100">Wajib:
            85 - 100</span>
    </div>

    <form wire:submit.prevent="saveAssessment" class="space-y-4">

        @if (count($indicators) == 0)
            <div class="bg-amber-50 p-4 rounded-2xl border border-amber-200 text-center">
                <span class="material-symbols-outlined text-amber-500 text-[32px] mb-2">warning</span>
                <p class="text-[12px] font-bold text-amber-700">Skema penilaian belum diatur oleh admin sekolah. Silakan
                    hubungi admin.</p>
            </div>
        @endif

        @foreach ($indicators as $ind)
            <div x-data="{ val: @entangle('scores.' . $ind->id) }" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-center mb-3">
                    <label class="font-extrabold text-slate-800 text-[13px] pr-2">{{ $ind->name }}</label>
                    <span
                        class="text-[#3525cd] font-black text-[20px] bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100"
                        x-text="val"></span>
                </div>

                <input x-model="val" type="range" min="85" max="100"
                    class="w-full h-2.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-[#3525cd] shadow-inner mb-2">
                @error('scores.' . $ind->id)
                    <p class="text-[10px] font-bold text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <section class="space-y-3 pt-2">
            <h3 class="text-[16px] font-extrabold text-slate-800 px-1">Catatan Tambahan</h3>

            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Kehadiran
                </label>
                <textarea wire:model="attendance_notes"
                    class="w-full p-4 rounded-2xl border border-slate-200 bg-white focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] outline-none text-[13px] font-medium transition-all placeholder:text-slate-400 resize-none shadow-sm"
                    placeholder="Contoh: Siswa disiplin dan jarang membolos..." rows="3"></textarea>
            </div>

            <div class="flex flex-col gap-1 mt-2">
                <label class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest pl-1">Evaluasi
                    Kualitatif </label>
                <textarea wire:model="assessment_notes"
                    class="w-full p-4 rounded-2xl border border-slate-200 bg-white focus:ring-2 focus:ring-[#3525cd]/20 focus:border-[#3525cd] outline-none text-[13px] font-medium transition-all placeholder:text-slate-400 resize-none shadow-sm"
                    placeholder="Contoh: Penguasaan praktek sangat bagus namun butuh peningkatan pada teori..." rows="3"></textarea>
            </div>
        </section>

        <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100 flex gap-3 mt-4">
            <span class="material-symbols-outlined text-[#3525cd] shrink-0">info</span>
            <p class="text-[11px] font-bold text-indigo-800 leading-relaxed">
                Nilai yang telah dikirim akan langsung tersinkronisasi dengan database sekolah untuk keperluan cetak
                sertifikat dan laporan.
            </p>
        </div>

        <div class="mt-4 mb-8">
            <button type="submit"
                class="w-full h-[54px] bg-[#3525cd] text-white font-extrabold rounded-[1.25rem] shadow-[0_8px_20px_rgba(53,37,205,0.3)] hover:bg-[#2c1eb3] transition-colors active:scale-95 flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="saveAssessment"
                    class="material-symbols-outlined text-[20px]">send</span>
                <span wire:loading.remove wire:target="saveAssessment">Simpan & Kirim Nilai</span>

                <span wire:loading wire:target="saveAssessment"
                    class="material-symbols-outlined animate-spin text-[20px]">sync</span>
                <span wire:loading wire:target="saveAssessment">Menyimpan...</span>
            </button>
        </div>

    </form>
</div>
