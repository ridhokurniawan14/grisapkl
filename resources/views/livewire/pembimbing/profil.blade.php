<div class="relative w-full pb-6 -mt-20">

    <div class="fixed top-0 left-0 right-0 sm:left-1/2 sm:-translate-x-1/2 sm:w-[390px] h-16 z-[51] pointer-events-none opacity-20"
        style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 24px 24px;">
    </div>

    <div
        class="absolute top-0 -left-4 -right-4 h-[150px] bg-gradient-to-b from-[#1c10a0] via-[#2d1fc5] to-[#4f46e5] z-0 rounded-b-[2.5rem] shadow-inner overflow-hidden">
        <div class="absolute inset-0 opacity-20"
            style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 24px 24px;">
        </div>
    </div>

    <div class="flex flex-col relative z-10 w-full pt-[120px] px-1">

        @if (session()->has('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)">
                <template x-teleport="body">
                    <div x-show="show" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-4"
                        class="fixed top-20 left-1/2 -translate-x-1/2 w-[90%] max-w-[360px] z-[9999] bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xl">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            <span class="text-[12px] font-bold">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false"
                            class="text-emerald-500 hover:text-emerald-700 active:scale-95 transition-transform"><span
                                class="material-symbols-outlined text-[18px]">close</span></button>
                    </div>
                </template>
            </div>
        @endif

        <div
            class="bg-white rounded-[2rem] shadow-lg border border-slate-100 p-6 pt-0 flex flex-col items-center text-center relative mb-6 mx-1">
            <div
                class="w-24 h-24 rounded-full border-[6px] border-white overflow-hidden shadow-md bg-slate-100 flex items-center justify-center text-4xl font-bold -mt-12 mb-3">
                <span class="text-[#3525cd] bg-[#e2dfff] w-full h-full flex items-center justify-center">
                    {{ strtoupper(substr($guruData['name'], 0, 1)) }}
                </span>
            </div>

            <h2 class="text-[20px] font-extrabold text-slate-800 leading-tight">{{ $guruData['name'] }}</h2>
            <p class="text-[12px] font-semibold text-slate-500 mt-1">Guru Pembimbing PKL</p>

            @if (!empty($guruData['nip']))
                <div
                    class="mt-3 px-4 py-1.5 bg-[#39b8fd]/10 border border-[#39b8fd]/20 text-[#004666] text-[11px] font-bold rounded-full tracking-wider">
                    NIP: {{ $guruData['nip'] }}
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-3 mx-1">
            <div class="bg-white rounded-[1.25rem] shadow-sm border border-slate-100 p-4 flex flex-col gap-1">
                <div class="flex items-center gap-2 text-[#3525cd]">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">call</span>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest">Nomor HP / WA</span>
                </div>
                <p class="text-[14px] font-extrabold text-slate-800 pl-[26px] mt-0.5">{{ $guruData['phone'] }}</p>
            </div>

            <div class="bg-white rounded-[1.25rem] shadow-sm border border-slate-100 p-4 flex flex-col gap-1">
                <div class="flex items-center gap-2 text-[#3525cd]">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">school</span>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest">Mata Pelajaran</span>
                </div>
                <p class="text-[14px] font-extrabold text-slate-800 pl-[26px] mt-0.5">{{ $guruData['subject'] }}</p>
            </div>

            <div class="bg-slate-50 rounded-[1.25rem] border border-slate-200 p-4 flex flex-col gap-1">
                <div class="flex items-center gap-2 text-slate-500">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">mail</span>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest">Email Terdaftar</span>
                </div>
                <div class="flex justify-between items-center pl-[26px] mt-0.5">
                    <p class="text-[14px] font-bold text-slate-600 italic truncate">{{ $guruData['email'] }}</p>
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">lock</span>
                </div>
            </div>

            <div class="bg-white rounded-[1.25rem] shadow-sm border border-slate-100 p-4 flex flex-col gap-3">
                <div class="flex items-center gap-2 text-[#3525cd]">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">draw</span>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest">Tanda Tangan Digital</span>
                </div>

                <div
                    class="h-28 w-full border-2 border-dashed border-slate-200 rounded-[1rem] flex items-center justify-center bg-slate-50 overflow-hidden">
                    @if ($guruData['signature_path'])
                        @if (str_starts_with($guruData['signature_path'], 'data:image'))
                            <img src="{{ $guruData['signature_path'] }}"
                                class="h-full object-contain opacity-80 mix-blend-multiply py-2">
                        @else
                            <img src="{{ asset('storage/' . $guruData['signature_path']) }}"
                                class="h-full object-contain opacity-80 mix-blend-multiply py-2">
                        @endif
                    @else
                        <span class="text-[11px] font-bold text-slate-400">Belum Ada TTD</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-3 mb-2 mx-1">
            <a href="{{ route('pembimbing.profil.edit') }}" wire:navigate
                class="w-full h-[52px] bg-indigo-50 border border-indigo-100 text-[#3525cd] rounded-[1.25rem] font-extrabold text-[14px] active:scale-95 transition-transform flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[20px]">edit_document</span> Edit Data & TTD
            </a>

            <a href="{{ route('pembimbing.profil.password') }}" wire:navigate
                class="w-full h-[52px] bg-white border border-slate-200 text-slate-700 shadow-sm rounded-[1.25rem] font-extrabold text-[14px] active:scale-95 transition-transform flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-orange-500">lock_reset</span> Edit Password
            </a>

            <button wire:click="logout"
                class="w-full h-[52px] bg-red-50 text-red-600 border border-red-100 rounded-[1.25rem] font-extrabold text-[14px] active:scale-95 transition-transform flex items-center justify-center gap-2 mt-2 hover:bg-red-100 shadow-sm">
                <span class="material-symbols-outlined text-[20px]">logout</span> Logout
            </button>

        </div>

        <div class="text-center mt-4 mb-2">
            <p class="text-[11px] font-semibold text-slate-400">GrisaPKL Version 1.1</p>
        </div>

    </div>
</div>
