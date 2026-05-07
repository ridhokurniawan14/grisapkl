@php
    $user = auth()->user();
@endphp

<nav
    class="bg-white absolute bottom-0 w-full z-50 rounded-t-2xl border-t border-slate-100 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] flex justify-around items-center px-2 py-3 pb-safe">

    {{-- ========================================== --}}
    {{-- MENU KHUSUS SISWA                          --}}
    {{-- ========================================== --}}
    @if ($user && $user->hasRole('siswa'))
        <!-- 1. Beranda -->
        <a href="#"
            class="flex flex-col items-center justify-center text-outline hover:text-primary w-14 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]">home</span>
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
        {{-- MENU KHUSUS DUDIKA                         --}}
        {{-- ========================================== --}}
    @elseif($user && $user->hasRole('dudika'))
        <!-- 1. Beranda -->
        <a href="#"
            class="flex flex-col items-center justify-center text-outline hover:text-primary w-14 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]">home</span>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>
        <!-- 2. Nilai -->
        <a href="#"
            class="flex flex-col items-center justify-center text-outline hover:text-primary w-14 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]">fact_check</span>
            <span class="text-[10px] font-medium">Nilai</span>
        </a>
        <!-- 3. Jurnal Siswa (Center Active) -->
        <a href="#"
            class="flex flex-col items-center justify-center text-primary font-bold bg-primary/10 rounded-xl px-4 py-1.5 -mt-3 shadow-sm">
            <span class="material-symbols-outlined mb-0.5 text-[24px]"
                style="font-variation-settings: 'FILL' 1;">menu_book</span>
            <span class="text-[11px] font-medium">Jurnal</span>
        </a>
        <!-- 4. Profil Dudika -->
        <a href="#"
            class="flex flex-col items-center justify-center text-outline hover:text-primary w-14 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]">domain</span>
            <span class="text-[10px] font-medium">Instansi</span>
        </a>
        <!-- 5. Profile Akun -->
        <a href="#"
            class="flex flex-col items-center justify-center text-outline hover:text-primary w-14 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]">person</span>
            <span class="text-[10px] font-medium">Profil</span>
        </a>

        {{-- ========================================== --}}
        {{-- MENU KHUSUS GURU PEMBIMBING                --}}
        {{-- ========================================== --}}
    @elseif($user && $user->hasRole('pembimbing'))
        {{-- Pastikan nama role guru di DB-mu apa, misal 'pembimbing' atau 'guru' --}}
        <!-- 1. Beranda -->
        <a href="#"
            class="flex flex-col items-center justify-center text-outline hover:text-primary w-14 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]">home</span>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>
        <!-- 2. Siswa Bimbingan -->
        <a href="#"
            class="flex flex-col items-center justify-center text-outline hover:text-primary w-14 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]">groups</span>
            <span class="text-[10px] font-medium">Siswa</span>
        </a>
        <!-- 3. Laporan Monitoring (Center Active) -->
        <a href="#"
            class="flex flex-col items-center justify-center text-primary font-bold bg-primary/10 rounded-xl px-4 py-1.5 -mt-3 shadow-sm">
            <span class="material-symbols-outlined mb-0.5 text-[24px]"
                style="font-variation-settings: 'FILL' 1;">assignment_turned_in</span>
            <span class="text-[11px] font-medium">Lapor</span>
        </a>
        <!-- 4. Data Kelengkapan -->
        <a href="#"
            class="flex flex-col items-center justify-center text-outline hover:text-primary w-14 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]">folder_shared</span>
            <span class="text-[10px] font-medium">Data</span>
        </a>
        <!-- 5. Profile -->
        <a href="#"
            class="flex flex-col items-center justify-center text-outline hover:text-primary w-14 transition-colors">
            <span class="material-symbols-outlined mb-1 text-[24px]">person</span>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
    @endif
</nav>
