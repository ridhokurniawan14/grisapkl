{{-- x-data ditambahkan state untuk Instruktur (true) dan Guru (false) --}}
<div class="relative w-full pb-5" x-data="{ isEditingField: false, openInstruktur: true, openGuru: false }">
    @if ($placement && $placement->dudika)

        <div class="absolute -top-[100px] -left-4 -right-4 h-[360px] z-0 rounded-b-[3.5rem] overflow-hidden shadow-md">
            <img class="w-full h-full object-cover"
                src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop"
                alt="Cover Perusahaan" />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-100 via-slate-100/30 to-[#3525cd]/60"></div>
        </div>

        <div class="flex flex-col relative z-10 w-full mt-[140px] gap-4">

            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 flex flex-col gap-1">

                <div class="flex items-center justify-between">
                    <span
                        class="text-[11px] font-bold text-[#006591] bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full inline-flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-[14px]">handshake</span>
                        Mitra Resmi
                    </span>
                    <span
                        class="text-[11px] font-bold text-green-600 bg-green-50 border border-green-100 px-3 py-1.5 rounded-full flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-[14px]">verified</span>
                        Aktif
                    </span>
                </div>

                <div>
                    <h1 class="text-[22px] font-extrabold text-slate-800 leading-tight">{{ $placement->dudika->name }}
                    </h1>
                    <p class="text-[13px] font-medium text-slate-500 flex items-start gap-1.5 mt-2">
                        <span
                            class="material-symbols-outlined text-[18px] text-[#3525cd] shrink-0 mt-0.5">location_on</span>
                        <span>{{ ucwords(strtolower($placement->dudika->address ?? '-')) }}</span>
                    </p>
                </div>
            </div>

            <div class="bg-sky-50 rounded-[1.5rem] p-5 shadow-sm border border-sky-100 flex flex-col gap-2 relative">
                <h2 class="text-[12px] font-bold text-[#006591] uppercase tracking-widest flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">work</span>
                    Bidang Pekerjaan (PKL Field)
                </h2>

                <div x-show="!isEditingField" class="flex items-center justify-between mt-1">
                    <div>
                        @if (empty($placement->pkl_field))
                            <p class="text-[14px] font-bold text-red-500 italic flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">warning</span> Belum Diisi
                            </p>
                        @else
                            <p class="text-[16px] font-bold text-slate-800 capitalize">{{ $placement->pkl_field }}</p>
                        @endif
                    </div>
                    <button @click="isEditingField = true"
                        class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-sky-600 shadow-sm active:scale-90 transition-transform">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                </div>

                <div x-show="isEditingField" x-cloak class="flex flex-col gap-2 mt-1">
                    <input type="text" wire:model="pkl_field"
                        class="w-full bg-white border border-sky-200 text-slate-800 text-[14px] rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sky-500"
                        placeholder="Ketik bidang pekerjaan...">
                    <div class="flex gap-2 justify-end">
                        <button @click="isEditingField = false"
                            class="px-4 py-2 text-[12px] font-bold text-slate-500 hover:text-slate-700">Batal</button>
                        <button wire:click="savePklField" @click="isEditingField = false"
                            class="px-5 py-2 text-[12px] font-bold text-white bg-sky-600 rounded-xl shadow-sm hover:bg-sky-700 active:scale-95">Simpan</button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[1.5rem] p-5 shadow-sm border border-slate-300 flex flex-col gap-3">
                <h2 class="text-[14px] font-bold text-slate-800 flex items-center gap-2 mb-1">
                    <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                        <span class="material-symbols-outlined text-[18px]">corporate_fare</span>
                    </div>
                    Pimpinan / Direktur
                </h2>
                <div class="pl-10">
                    <p class="text-[11px] font-semibold text-slate-400">Nama Pimpinan</p>
                    @if (empty($placement->dudika->head_name))
                        <p class="text-[13px] font-bold text-red-500 italic">Belum Diisi</p>
                    @else
                        <p class="text-[14px] font-bold text-slate-800 capitalize">
                            {{ ucwords(strtolower($placement->dudika->head_name)) }}</p>
                        @if (!empty($placement->dudika->head_nip))
                            <p class="text-[11px] font-semibold text-slate-500 mt-0.5">NIP/NIK:
                                {{ $placement->dudika->head_nip }}</p>
                        @endif
                    @endif
                </div>
            </div>

            <div
                class="bg-[#3525cd] rounded-[1.5rem] shadow-md flex flex-col relative overflow-hidden transition-all duration-300">
                <div
                    class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none">
                </div>

                <button type="button" @click="openInstruktur = !openInstruktur"
                    class="w-full px-5 py-4 flex items-center justify-between text-white relative z-10 active:bg-white/5 transition-colors">
                    <h2 class="text-[12px] font-bold text-indigo-200 uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">engineering</span> Instruktur DUDIKA
                    </h2>
                    <span class="material-symbols-outlined text-indigo-200 transition-transform duration-300"
                        :class="openInstruktur ? 'rotate-180' : ''">expand_more</span>
                </button>

                <div x-show="openInstruktur" x-collapse x-cloak>
                    <div class="px-5 pb-5 pt-1 flex flex-col gap-4 relative z-10">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white border border-white/30 backdrop-blur-sm shrink-0">
                                <span class="material-symbols-outlined text-[24px]">engineering</span>
                            </div>
                            <div>
                                @if (empty($placement->dudika->supervisor_name))
                                    <h3 class="text-[16px] font-bold text-red-300 italic">Belum Diisi</h3>
                                @else
                                    <h3 class="text-[16px] font-bold text-white capitalize">
                                        {{ ucwords(strtolower($placement->dudika->supervisor_name)) }}</h3>
                                    @if (!empty($placement->dudika->supervisor_nip))
                                        <p class="text-[11px] text-indigo-200 font-medium mt-0.5">NIP/NIK:
                                            {{ $placement->dudika->supervisor_nip }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if (!empty($placement->dudika->supervisor_phone))
                            @php $waInstruktur = preg_replace('/^0/', '62', $placement->dudika->supervisor_phone); @endphp
                            <a href="https://wa.me/{{ $waInstruktur }}" target="_blank"
                                class="w-full h-11 bg-white text-[#3525cd] rounded-xl font-bold text-[13px] flex items-center justify-center gap-2 hover:bg-slate-50 active:scale-95 transition-all shadow-sm">
                                <svg class="w-5 h-5 fill-current text-green-500" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                                </svg>
                                Hubungi Instruktur
                            </a>
                        @endif
                    </div>
                </div>

                @php
                    // Ambil Logo Sekolah untuk Ikon Guru Pembimbing
                    $school = \App\Models\SchoolProfile::first();
                    $schoolLogo = $school && $school->logo_path ? asset('storage/' . $school->logo_path) : null;
                @endphp

                <div
                    class="bg-white rounded-[1.5rem] shadow-sm border border-slate-100 flex flex-col relative overflow-hidden transition-all duration-300 mt-2">

                    <button type="button" @click="openGuru = !openGuru"
                        class="w-full px-5 py-4 flex items-center justify-between text-slate-800 relative z-10 hover:bg-slate-50 active:bg-slate-100 transition-colors">
                        <h2 class="text-[12px] font-bold uppercase tracking-widest flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center border border-slate-200 overflow-hidden p-1 shadow-sm shrink-0">
                                @if ($schoolLogo)
                                    <img src="{{ $schoolLogo }}" class="w-full h-full object-contain" alt="Logo">
                                @else
                                    <span class="material-symbols-outlined text-[18px] text-slate-500">school</span>
                                @endif
                            </div>
                            Guru Pembimbing
                        </h2>
                        <span class="material-symbols-outlined text-slate-400 transition-transform duration-300"
                            :class="openGuru ? 'rotate-180' : ''">expand_more</span>
                    </button>

                    <div x-show="openGuru" x-collapse x-cloak>
                        <div class="px-5 pb-5 pt-1 flex flex-col gap-4 relative z-10 border-t border-slate-100 mt-1">
                            @if ($placement->teacher)
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center border border-slate-200 shrink-0 overflow-hidden p-1.5 shadow-sm">

                                        <span class="material-symbols-outlined text-[24px] text-slate-500">school</span>
                                    </div>
                                    <div>
                                        <h3 class="text-[16px] font-bold text-slate-800 capitalize">
                                            {{ ucwords(strtolower($placement->teacher->name)) }}</h3>
                                        @if (!empty($placement->teacher->nip))
                                            <p class="text-[11px] text-slate-500 font-semibold mt-0.5">NIP:
                                                {{ $placement->teacher->nip }}</p>
                                        @endif
                                    </div>
                                </div>

                                @if (!empty($placement->teacher->phone))
                                    @php $waGuru = preg_replace('/^0/', '62', $placement->teacher->phone); @endphp
                                    <a href="https://wa.me/{{ $waGuru }}" target="_blank"
                                        class="w-full h-11 bg-[#3525cd] text-white rounded-xl font-bold text-[13px] flex items-center justify-center gap-2 hover:bg-[#2c1eb3] active:scale-95 transition-all shadow-md">
                                        <svg class="w-5 h-5 fill-current text-white" viewBox="0 0 24 24">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                                        </svg>
                                        Hubungi Guru
                                    </a>
                                @endif
                            @else
                                <p class="text-[12px] font-semibold text-red-500 italic pl-16">Belum ada guru
                                    pembimbing.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-[60vh] text-center px-6">
                    <div class="w-20 h-20 bg-slate-200 rounded-full flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[40px] text-slate-400">domain_disabled</span>
                    </div>
                    <h2 class="text-[18px] font-bold text-slate-800">Belum Ada DUDIKA</h2>
                    <p class="text-[13px] text-slate-500 mt-2">Anda belum diplot ke instansi tempat PKL. Silakan
                        hubungi
                        admin / ketua pokja PKL.</p>
                </div>
    @endif
</div>
