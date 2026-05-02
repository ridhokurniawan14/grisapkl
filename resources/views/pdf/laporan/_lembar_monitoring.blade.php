<h2 class="text-center font-bold" style="font-size: 14pt; margin-top:-25px; text-decoration: underline;">LEMBAR MONITORING
    GURU</h2>

<!-- BIODATA GURU PEMBIMBING -->
<table style="width: 100%; margin-top: 30px; margin-bottom: 20px; font-size: 11pt;">
    <tr>
        <td style="width: 25%; padding: 4px 0;">Nama Guru Pembimbing</td>
        <td style="width: 2%; padding: 4px 0;">:</td>
        <td style="width: 73%; padding: 4px 0; font-weight: bold;">{{ strtoupper($placement->teacher->name ?? '-') }}
        </td>
    </tr>
    <tr>
        <td style="padding: 4px 0;">Total Kunjungan</td>
        <td style="padding: 4px 0;">:</td>
        <td style="padding: 4px 0;">{{ count($placement->monitorings ?? []) }} kali</td>
    </tr>
</table>

<!-- MANTRA SAKTI: table-layout: fixed agar lebar kolom mutlak dan tidak bergeser -->
<table class="jurnal-table" style="width: 100%; table-layout: fixed;">
    <thead>
        <tr>
            <th style="width: 5%;">NO</th>
            <th style="width: 17%;">HARI / TANGGAL</th>
            <th style="width: 35%;">KEGIATAN</th>
            <th style="width: 28%;">PARAF GURU<br>PEMBIMBING<br>SEKOLAH</th>
            <th style="width: 15%;">FOTO</th>
        </tr>
    </thead>
    <tbody>
        @forelse($placement->monitorings ?? [] as $index => $mon)
            @php
                $fotoMon = getBase64Image($mon->photo_path ?? '');
            @endphp
            <tr>
                <td style="vertical-align: middle; text-align: center;">{{ $index + 1 }}</td>
                <td style="vertical-align: middle; text-align: center;">
                    <!-- Memecah Hari dan Tanggal agar estetik atas-bawah -->
                    {{ \Carbon\Carbon::parse($mon->date)->isoFormat('dddd') }},<br>
                    {{ \Carbon\Carbon::parse($mon->date)->isoFormat('D MMMM Y') }}
                </td>
                <td class="text-justify" style="vertical-align: middle; padding: 10px;">
                    {{ $mon->activity ?? '-' }}
                </td>
                <td style="vertical-align: middle; text-align: center;">
                    <!-- left: 50px DIHAPUS, diganti max-width agar aman di dalam border -->
                    @if ($ttdGuruBase64 && ($school->is_teacher_signature_enabled ?? true))
                        <img src="{{ $ttdGuruBase64 }}"
                            style="height: 40px; width: auto; max-width: 90%; display: block; margin: 0 auto;">
                    @endif
                </td>
                <td style="vertical-align: middle;">
                    @if ($fotoMon)
                        <img src="{{ $fotoMon }}" class="img-box" style="display: block; margin: 0 auto;">
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            @for ($i = 1; $i <= 5; $i++)
                <tr>
                    <td style="height: 60px; vertical-align: middle; text-align: center;">{{ $i }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        @endforelse
    </tbody>
</table>
