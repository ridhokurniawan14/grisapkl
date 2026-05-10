@php
    $user = auth()->user();
@endphp

<nav
    class="bg-white absolute bottom-0 w-full z-50 rounded-t-2xl border-t border-slate-100 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] flex justify-around items-center px-2 py-3 pb-safe">

    {{-- ========================================== --}}
    {{-- MENU KHUSUS SISWA                          --}}
    {{-- ========================================== --}}
    @if ($user && $user->hasRole('siswa'))
        <!-- Beranda -->
        @php $isBeranda = request()->routeIs('siswa.beranda'); @endphp
        <a href="{{ route('siswa.beranda') }}"
            class="flex flex-col items-center justify-center {{ $isBeranda ? 'text-primary font-bold bg-primary/10' : 'text-outline hover:text-primary bg-transparent' }} w-14 rounded-xl py-1 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]"
                style="font-variation-settings: 'FILL' {{ $isBeranda ? '1' : '0' }};">home</span>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>
        <!-- 2. Jurnal -->
        @php $isJurnal = request()->routeIs('siswa.jurnal'); @endphp
        <a href="{{ route('siswa.jurnal') }}"
            class="flex flex-col items-center justify-center {{ $isJurnal ? 'text-primary font-bold bg-primary/10' : 'text-outline hover:text-primary bg-transparent' }} w-14 rounded-xl py-1 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]"
                style="font-variation-settings: 'FILL' {{ $isJurnal ? '1' : '0' }};">description</span>
            <span class="text-[10px] font-medium">Jurnal</span>
        </a>

        <!-- 3. Absen -->
        @php $isAbsen = request()->routeIs('siswa.absen'); @endphp
        <a href="{{ route('siswa.absen') }}"
            class="flex flex-col items-center justify-center {{ $isAbsen ? 'text-primary font-bold bg-primary/10' : 'text-outline hover:text-primary bg-transparent' }} rounded-xl px-4 py-1.5 -mt-3 shadow-sm transition-all">
            <span class="material-symbols-outlined mb-0.5 text-[24px]"
                style="font-variation-settings: 'FILL' {{ $isAbsen ? '1' : '0' }};">fingerprint</span>
            <span class="text-[11px] font-medium">Absen</span>
        </a>
        <!-- 4. Dudika -->
        @php $isDudika = request()->routeIs('siswa.dudika'); @endphp
        <a href="{{ route('siswa.dudika') }}"
            class="flex flex-col items-center justify-center {{ $isDudika ? 'text-primary font-bold bg-primary/10' : 'text-outline hover:text-primary bg-transparent' }} w-14 rounded-xl py-1 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]"
                style="font-variation-settings: 'FILL' {{ $isDudika ? '1' : '0' }};">business</span>
            <span class="text-[10px] font-medium">DUDIKA</span>
        </a>
        <!-- 5. Profile -->
        @php $isProfile = request()->routeIs('siswa.profil'); @endphp
        <a href="{{ route('siswa.profil') }}"
            class="flex flex-col items-center justify-center {{ $isProfile ? 'text-primary font-bold bg-primary/10' : 'text-outline hover:text-primary bg-transparent' }} w-14 rounded-xl py-1 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]"
                style="font-variation-settings: 'FILL' {{ $isProfile ? '1' : '0' }};">person</span>
            <span class="text-[10px] font-medium">Profil</span>
        </a>

        {{-- ========================================== --}}
        {{-- MENU KHUSUS GURU PEMBIMBING / GURU           --}}
        {{-- ========================================== --}}
    @elseif($user && $user->hasRole('guru'))
        @php $isBerandaGuru = request()->routeIs('pembimbing.beranda'); @endphp
        <a href="{{ route('pembimbing.beranda') }}"
            class="flex flex-col items-center justify-center {{ $isBerandaGuru ? 'text-[#3525cd] font-bold bg-[#3525cd]/10' : 'text-slate-400 hover:text-[#3525cd] bg-transparent' }} w-14 rounded-xl py-1 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]"
                style="font-variation-settings: 'FILL' {{ $isBerandaGuru ? '1' : '0' }};">home</span>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>

        @php $isSiswaGuru = request()->routeIs('pembimbing.siswa'); @endphp
        <a href="{{ route('pembimbing.siswa') }}"
            class="flex flex-col items-center justify-center {{ $isSiswaGuru ? 'text-[#3525cd] font-bold bg-[#3525cd]/10' : 'text-slate-400 hover:text-[#3525cd] bg-transparent' }} w-14 rounded-xl py-1 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]"
                style="font-variation-settings: 'FILL' {{ $isSiswaGuru ? '1' : '0' }};">groups</span>
            <span class="text-[10px] font-medium">Siswa</span>
        </a>

        @php $isLaporGuru = request()->routeIs('pembimbing.lapor'); @endphp
        <a href="{{ route('pembimbing.lapor') }}"
            class="flex flex-col items-center justify-center {{ $isLaporGuru ? 'text-[#3525cd] font-bold bg-[#e2dfff]' : 'text-slate-400 hover:text-[#3525cd] bg-transparent' }} rounded-xl px-4 py-1.5 -mt-3 shadow-sm border border-white transition-transform active:scale-95">
            <span class="material-symbols-outlined mb-0.5 text-[24px]"
                style="font-variation-settings: 'FILL' {{ $isLaporGuru ? '1' : '0' }};">assignment_turned_in</span>
            <span class="text-[11px] font-medium">Lapor</span>
        </a>

        @php $isDataGuru = request()->routeIs('pembimbing.data'); @endphp
        <a href="{{ route('pembimbing.data') }}"
            class="flex flex-col items-center justify-center {{ $isDataGuru ? 'text-[#3525cd] font-bold bg-[#3525cd]/10' : 'text-slate-400 hover:text-[#3525cd] bg-transparent' }} w-14 rounded-xl py-1 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]"
                style="font-variation-settings: 'FILL' {{ $isDataGuru ? '1' : '0' }};">folder_shared</span>
            <span class="text-[10px] font-medium">Data</span>
        </a>

        @php $isProfileGuru = request()->routeIs('pembimbing.profil'); @endphp
        <a href="{{ route('pembimbing.profil') }}"
            class="flex flex-col items-center justify-center {{ $isProfileGuru ? 'text-[#3525cd] font-bold bg-[#3525cd]/10' : 'text-slate-400 hover:text-[#3525cd] bg-transparent' }} w-14 rounded-xl py-1 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]"
                style="font-variation-settings: 'FILL' {{ $isProfileGuru ? '1' : '0' }};">person</span>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
        {{-- ========================================== --}}
        {{-- MENU KHUSUS DUDIKA                         --}}
        {{-- ========================================== --}}
        {{-- ========================================== --}}
    @elseif($user && $user->hasRole('dudika'))
        {{-- Pastikan route 'dudika.beranda' sudah di-uncomment di web.php ya bro! --}}
        @php $isBerandaDudika = request()->routeIs('dudika.beranda'); @endphp
        <a href="{{ Route::has('dudika.beranda') ? route('dudika.beranda') : '#' }}"
            class="flex flex-col items-center justify-center {{ $isBerandaDudika ? 'text-[#3525cd] font-bold bg-[#3525cd]/10' : 'text-slate-400 hover:text-[#3525cd] bg-transparent' }} w-14 rounded-xl py-1 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]"
                style="font-variation-settings: 'FILL' {{ $isBerandaDudika ? '1' : '0' }};">home</span>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>

        <a href="#"
            class="flex flex-col items-center justify-center text-slate-400 hover:text-[#3525cd] w-14 transition-colors bg-transparent py-1 rounded-xl">
            <span class="material-symbols-outlined mb-1 text-[24px]">fact_check</span>
            <span class="text-[10px] font-medium">Nilai</span>
        </a>

        <a href="#"
            class="flex flex-col items-center justify-center text-[#3525cd] font-bold bg-[#e2dfff] rounded-xl px-4 py-1.5 -mt-3 shadow-sm border border-white">
            <span class="material-symbols-outlined mb-0.5 text-[24px]"
                style="font-variation-settings: 'FILL' 1;">menu_book</span>
            <span class="text-[11px] font-medium">Jurnal</span>
        </a>

        <a href="#"
            class="flex flex-col items-center justify-center text-slate-400 hover:text-[#3525cd] w-14 transition-colors bg-transparent py-1 rounded-xl">
            <span class="material-symbols-outlined mb-1 text-[24px]">domain</span>
            <span class="text-[10px] font-medium">Instansi</span>
        </a>

        <a href="#"
            class="flex flex-col items-center justify-center text-slate-400 hover:text-[#3525cd] w-14 transition-colors bg-transparent py-1 rounded-xl">
            <span class="material-symbols-outlined mb-1 text-[24px]">person</span>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
    @endif

</nav>
