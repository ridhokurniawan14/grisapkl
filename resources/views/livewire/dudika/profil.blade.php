<div class="relative w-full pb-3">

    <div
        class="absolute top-[-80px] -left-4 -right-4 h-[240px] bg-[#3525cd] z-0 rounded-b-[2.5rem] shadow-sm overflow-hidden">
        <div
            class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl -translate-y-1/4 translate-x-1/4">
        </div>
        <div
            class="absolute bottom-0 left-0 w-48 h-48 bg-indigo-400 opacity-20 rounded-full blur-2xl translate-y-1/4 -translate-x-1/4">
        </div>
    </div>

    <div class="flex flex-col relative z-10 w-full pt-[130px] px-1 pb-4">

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
                            class="text-emerald-500 hover:text-emerald-700 active:scale-95 transition-transform">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                </template>
            </div>
        @endif

        <div
            class="bg-white rounded-[2rem] shadow-lg border border-slate-100 p-6 flex flex-col items-center text-center relative mb-6 mx-1 transition-all">
            <div
                class="w-24 h-24 rounded-full overflow-hidden shadow-md flex items-center justify-center text-4xl font-bold mb-3 border-[5px] border-white -mt-16 bg-white relative z-20">
                <span class="text-[#3525cd] bg-[#e2dfff] w-full h-full flex items-center justify-center">
                    {{ strtoupper(substr($dudikaData['name'], 0, 1)) }}
                </span>
            </div>

            <h2 class="text-[20px] font-extrabold text-slate-800 leading-tight">{{ $dudikaData['name'] }}</h2>
            <p class="text-[12px] font-semibold text-slate-500 mt-1">Instansi PKL / DUDIKA</p>
        </div>

        <div class="flex flex-col gap-3 mx-1">

            <div
                class="bg-white rounded-[1.25rem] shadow-sm border border-slate-100 p-4 flex flex-col gap-3 transition-all hover:border-indigo-100 hover:shadow-md">
                <div class="flex items-center gap-2 text-[#3525cd] border-b border-slate-50 pb-2">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">domain</span>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest">Informasi Instansi</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-bold text-slate-400">Nama Instansi</span>
                    <p class="text-[13px] font-extrabold text-slate-800">{{ $dudikaData['name'] }}</p>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-bold text-slate-400">Alamat Lengkap</span>
                    <p class="text-[13px] font-bold text-slate-700 leading-relaxed">{{ $dudikaData['address'] }}</p>
                </div>
            </div>

            <div
                class="bg-white rounded-[1.25rem] shadow-sm border border-slate-100 p-4 flex flex-col gap-3 transition-all hover:border-emerald-100 hover:shadow-md">
                <div class="flex items-center gap-2 text-emerald-600 border-b border-slate-50 pb-2">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">manage_accounts</span>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest">Pimpinan / Direktur</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-bold text-slate-400">Nama Lengkap</span>
                        <p class="text-[13px] font-extrabold text-slate-800">{{ $dudikaData['head_name'] }}</p>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-bold text-slate-400">NIP / NIK</span>
                        <p class="text-[13px] font-extrabold text-slate-800">{{ $dudikaData['head_nip'] }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-[1.25rem] shadow-sm border border-slate-100 p-4 flex flex-col gap-3 transition-all hover:border-amber-100 hover:shadow-md">
                <div class="flex items-center gap-2 text-amber-500 border-b border-slate-50 pb-2">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">support_agent</span>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest">Pembimbing Lapangan</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-bold text-slate-400">Nama Lengkap</span>
                        <p class="text-[13px] font-extrabold text-slate-800">{{ $dudikaData['supervisor_name'] }}</p>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-bold text-slate-400">NIP / NIK</span>
                        <p class="text-[13px] font-extrabold text-slate-800">{{ $dudikaData['supervisor_nip'] }}</p>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-bold text-slate-400">Nomor WhatsApp</span>
                    <p class="text-[13px] font-extrabold text-slate-800">{{ $dudikaData['supervisor_phone'] }}</p>
                </div>
            </div>

            <div class="bg-slate-50 rounded-[1.25rem] border border-slate-200 p-4 flex flex-col gap-1">
                <div class="flex items-center gap-2 text-slate-500">
                    <span class="material-symbols-outlined text-[18px]"
                        style="font-variation-settings: 'FILL' 1;">mail</span>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest">Email Instansi</span>
                </div>
                <div class="flex justify-between items-center pl-[26px] mt-0.5">
                    <p class="text-[14px] font-bold text-slate-600 italic truncate">{{ $dudikaData['email'] }}</p>
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">lock</span>
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-3 mb-2 mx-1">
            <a href="{{ route('dudika.profil.edit') ?? '#' }}" wire:navigate
                class="w-full h-[52px] bg-indigo-50 border border-indigo-100 text-[#3525cd] rounded-[1.25rem] font-extrabold text-[14px] active:scale-95 transition-transform flex items-center justify-center gap-2 shadow-sm hover:bg-indigo-100">
                <span class="material-symbols-outlined text-[20px]">edit_document</span> Lengkapi Profil DUDIKA
            </a>

            <a href="{{ route('dudika.profil.password') ?? '#' }}" wire:navigate
                class="w-full h-[52px] bg-white border border-slate-200 text-slate-700 shadow-sm rounded-[1.25rem] font-extrabold text-[14px] active:scale-95 transition-transform flex items-center justify-center gap-2 hover:bg-slate-50">
                <span class="material-symbols-outlined text-[20px] text-orange-500">lock_reset</span> Edit Password
            </a>

            <button wire:click="logout"
                class="w-full h-[52px] bg-red-50 text-red-600 border border-red-100 rounded-[1.25rem] font-extrabold text-[14px] active:scale-95 transition-transform flex items-center justify-center gap-2 mt-2 hover:bg-red-100 shadow-sm">
                <span class="material-symbols-outlined text-[20px]">logout</span> Logout
            </button>
        </div>

        <div class="text-center mt-6 mb-2">
            <p class="text-[11px] font-semibold text-slate-400 opacity-70">GrisaPKL Version 1.1</p>
        </div>

    </div>
</div>
