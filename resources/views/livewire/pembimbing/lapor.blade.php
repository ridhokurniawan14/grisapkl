<div class="relative w-full pb-6 -mt-20" x-data="{ showReportForm: false, showDetailModal: false, detailData: {} }">

    <div
        class="absolute top-0 -left-4 -right-4 h-[400px] bg-gradient-to-b from-indigo-100/60 via-indigo-50/20 to-transparent z-0">
    </div>

    <div class="flex flex-col relative z-10 w-full pt-[76px] px-2">

        <section class="flex flex-col items-center justify-center pb-6 pt-2 relative">
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_center,_rgba(79,70,229,0.12)_0%,_transparent_60%)] -z-10">
            </div>

            <div class="text-center mb-6">
                <p class="text-[10px] font-extrabold text-slate-500 tracking-widest mb-1.5 uppercase">
                    {{ $scheduleName }}
                </p>
                <div
                    class="bg-[#e2dfff] text-[#3525cd] font-extrabold px-4 py-1.5 rounded-full text-[12px] shadow-sm tracking-wide border border-white">
                    {{ $scheduleDateStr }}
                </div>
            </div>

            <button @if ($isActiveWindow) @click="showReportForm = true" @else disabled @endif
                class="w-48 h-48 rounded-full flex flex-col items-center justify-center text-white group transition-all duration-300 border-[6px] border-white relative overflow-hidden {{ $isActiveWindow ? 'bg-gradient-to-br from-[#4f46e5] to-[#3525cd] shadow-[0_12px_40px_rgba(53,37,205,0.4)] active:scale-95 cursor-pointer' : 'bg-slate-300 shadow-none cursor-not-allowed grayscale' }}">

                @if ($isActiveWindow)
                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity">
                    </div>
                @endif

                <div
                    class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mb-2 {{ $isActiveWindow ? 'group-hover:scale-110' : '' }} transition-transform">
                    <span class="material-symbols-outlined text-[36px]"
                        style="font-variation-settings: 'FILL' 1;">add_location_alt</span>
                </div>
                <span class="text-[18px] font-extrabold leading-tight">Lapor<br>Monitoring</span>
            </button>

            @if ($isActiveWindow)
                <p class="mt-6 text-[12px] font-medium text-slate-500 text-center max-w-[280px] leading-relaxed">
                    Laporkan hasil kunjungan pemantauan siswa di instansi DUDIKA.
                </p>
            @else
                <div
                    class="mt-6 flex items-center gap-1.5 text-red-500 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100">
                    <span class="material-symbols-outlined text-[16px]">info</span>
                    <p class="text-[11px] font-bold">Tombol terkunci. Di luar rentang jadwal aktif.</p>
                </div>
            @endif
        </section>

        <div class="grid grid-cols-2 gap-3 mb-6 px-1">
            <div class="bg-white p-4 rounded-[1.25rem] shadow-sm border border-slate-100 flex flex-col justify-between">
                <p class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Sudah Dikunjungi</p>
                <p class="text-[28px] font-extrabold text-[#3525cd] leading-none mt-1">{{ $visited }} <span
                        class="text-[13px] font-medium text-slate-400 normal-case">Instansi</span></p>
            </div>
            <div class="bg-white p-4 rounded-[1.25rem] shadow-sm border border-slate-100 flex flex-col justify-between">
                <p class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Belum Dikunjungi</p>
                <p class="text-[28px] font-extrabold text-orange-600 leading-none mt-1">{{ $remaining }} <span
                        class="text-[13px] font-medium text-slate-400 normal-case">Instansi</span></p>
            </div>
        </div>

        <section class="px-1">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[18px] font-extrabold text-slate-800">Riwayat Monitoring</h2>
                <div class="relative">
                    <input type="month" wire:model.live="filterMonth"
                        class="bg-indigo-50 text-[#3525cd] font-extrabold text-[12px] px-3 py-1.5 rounded-full border border-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
            </div>

            <div class="flex flex-col gap-3 pb-4">
                @forelse ($history as $item)
                    <div @click="detailData = {{ json_encode($item) }}; showDetailModal = true"
                        class="bg-white p-4 rounded-[1.25rem] shadow-sm border border-slate-100 flex gap-4 hover:bg-slate-50 transition-colors cursor-pointer group">

                        <div
                            class="w-14 h-14 rounded-[1rem] bg-indigo-50 flex flex-col items-center justify-center shrink-0 border border-indigo-100 shadow-inner group-hover:bg-[#3525cd] group-hover:text-white transition-colors">
                            <span
                                class="font-extrabold text-[20px] leading-none {{ $isActiveWindow ? 'text-[#3525cd] group-hover:text-white' : '' }}">{{ $item['date_num'] }}</span>
                            <span
                                class="font-bold uppercase text-[9px] mt-0.5 opacity-70">{{ $item['date_month'] }}</span>
                        </div>

                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div class="pr-2">
                                    <h3 class="text-[14px] font-extrabold text-slate-800 leading-tight">
                                        {{ $item['dudika_name'] }}
                                    </h3>
                                    <p class="text-[11px] font-bold text-slate-500 mt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">group</span>
                                        {{ $item['students_covered'] }} Siswa Tercakup
                                    </p>
                                </div>
                                <a href="{{ route('pembimbing.lapor.edit', $item['id']) ?? '#' }}" @click.stop
                                    class="text-slate-400 hover:text-amber-500 bg-slate-50 hover:bg-amber-50 p-1.5 rounded-lg transition-colors active:scale-95 border border-slate-100 shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                </a>
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                <span
                                    class="bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase border border-emerald-100 tracking-wider">Selesai</span>
                                <span class="text-[11px] font-semibold text-slate-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">image</span>
                                    {{ $item['photos_count'] }} Foto
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-6 text-center border border-slate-200 mt-2 shadow-sm">
                        <span class="material-symbols-outlined text-[40px] text-slate-300 mb-2">event_busy</span>
                        <p class="text-[13px] font-bold text-slate-700">Belum ada riwayat</p>
                        <p class="text-[11px] text-slate-500 mt-1">Tidak ada data monitoring pada bulan ini.</p>
                    </div>
                @endforelse
            </div>
        </section>

    </div>

    <template x-teleport="body">
        <div x-show="showReportForm" style="display: none;"
            class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center sm:p-4"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="absolute inset-0" @click="showReportForm = false"></div>

            <div class="bg-white w-full sm:w-[390px] rounded-t-[2rem] sm:rounded-[2rem] p-6 shadow-2xl relative transform transition-transform"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-full sm:translate-y-4" x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full sm:translate-y-4">

                <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-5 sm:hidden"></div>

                <h2 class="text-[20px] font-extrabold text-slate-800 mb-4">Buat Laporan Baru</h2>

                <form class="flex flex-col gap-4">
                    <div>
                        <label
                            class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-widest mb-1.5">Pilih
                            DUDIKA</label>
                        <select
                            class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-[13px] font-bold rounded-xl px-3 py-3 outline-none focus:border-[#3525cd] shadow-sm appearance-none">
                            <option>Pilih Instansi...</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-widest mb-1.5">Foto
                            Dokumentasi</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button"
                                class="flex flex-col items-center justify-center gap-1 border-2 border-dashed border-slate-300 bg-slate-50 rounded-xl p-4 text-slate-500 hover:border-[#3525cd] hover:text-[#3525cd] transition-all">
                                <span class="material-symbols-outlined text-[28px]">photo_camera</span>
                                <span class="text-[11px] font-bold">Kamera Langsung</span>
                            </button>
                            <button type="button"
                                class="flex flex-col items-center justify-center gap-1 border-2 border-dashed border-slate-300 bg-slate-50 rounded-xl p-4 text-slate-500 hover:border-[#3525cd] hover:text-[#3525cd] transition-all">
                                <span class="material-symbols-outlined text-[28px]">cloud_upload</span>
                                <span class="text-[11px] font-bold">Upload Galeri</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-widest mb-1.5">Catatan
                            Pemantauan</label>
                        <textarea
                            class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-[13px] font-medium rounded-xl px-3 py-3 outline-none focus:border-[#3525cd] shadow-sm"
                            rows="3" placeholder="Tulis kondisi siswa dan saran dari pihak industri..."></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showReportForm = false"
                            class="flex-1 py-3 text-[13px] font-bold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-colors">Batal</button>
                        <button type="submit"
                            class="flex-1 py-3 text-[13px] font-bold text-white bg-[#3525cd] hover:bg-[#2c1eb3] rounded-xl shadow-lg active:scale-95 transition-all">Kirim
                            Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="showDetailModal" style="display: none;"
            class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="absolute inset-0 cursor-pointer" @click="showDetailModal = false"></div>

            <div class="bg-white w-full max-w-[340px] rounded-[1.5rem] p-6 shadow-2xl relative z-10" @click.stop>
                <div class="flex items-center gap-3 mb-4 border-b border-slate-100 pb-4">
                    <div
                        class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">verified</span>
                    </div>
                    <div>
                        <h3 class="text-[16px] font-extrabold text-slate-800 leading-tight"
                            x-text="detailData.dudika_name"></h3>
                        <p class="text-[11px] font-medium text-slate-500 mt-0.5" x-text="detailData.date_full"></p>
                    </div>
                </div>

                <div class="space-y-3 mb-6">
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Siswa Tercakup
                        </p>
                        <p class="text-[13px] font-bold text-slate-700 flex items-center gap-1 mt-0.5"><span
                                class="material-symbols-outlined text-[16px] text-[#3525cd]">group</span> <span
                                x-text="detailData.students_covered"></span> Orang</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Catatan
                            Kunjungan</p>
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 mt-1">
                            <p class="text-[12px] font-medium text-slate-700 leading-relaxed"
                                x-text="detailData.notes"></p>
                        </div>
                    </div>
                </div>

                <button @click="showDetailModal = false"
                    class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-bold rounded-xl active:scale-95 transition-all">
                    Tutup Detail
                </button>
            </div>
        </div>
    </template>

</div>
