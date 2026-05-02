<h2 style="font-size:22pt; margin-top:-20px;" class="text-center font-bold">LEMBAR PENGESAHAN</h2>

<div class="text-center font-bold mt-4" style="font-size:18pt; margin-top: 30px; line-height: 1.8;">
    Laporan Praktik Kerja Lapangan<br>
    Siswa {{ $school->name ?? 'SMK PGRI 1 GIRI BANYUWANGI' }}<br>
    Tahun Pelajaran {{ $placement->academicYear->name ?? '2025/2026' }}
</div>

<div class="text-center" style="font-size:14pt; margin-top: 40px;">
    Pada tanggal {{ \Carbon\Carbon::parse($placement->start_date)->isoFormat('D MMMM Y') }} s.d
    {{ \Carbon\Carbon::parse($placement->end_date)->isoFormat('D MMMM Y') }}
</div>

<table style="width: 100%; font-size:14pt; margin-top: 30px;">
    <tr>
        <td width="20%"></td>
        <td style="text-align: right" width="25%">Ditetapkan di</td>
        <td width="5%">:</td>
        <td width="50%">{{ $school->city ?? 'Banyuwangi' }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align: right">Pada Tanggal</td>
        <td>:</td>
        <td>{{ \Carbon\Carbon::parse($placement->end_date)->isoFormat('D MMMM Y') }}</td>
    </tr>
</table>

<!-- TABEL PENGESAHAN DUDIKA -->
<table
    style="width: 100%; margin-top: 30px; font-size:14pt; text-align: center; table-layout: fixed; border-collapse: collapse;">
    <tr>
        <td class="font-bold" style="vertical-align: middle; padding: 5px;">Kepala DUDIKA</td>
        <td class="font-bold" style="vertical-align: middle; padding: 5px;">Pembimbing PKL DUDIKA</td>
    </tr>
    <tr>
        <td style="height: 60px;"></td>
        <td></td>
    </tr>
    <tr>
        <td style="vertical-align: top; padding: 5px;">
            <strong
                style="text-decoration: underline;">{{ $placement->dudika->head_name ?? '.....................................' }}</strong>
            @if (!empty($placement->dudika->head_nip) && trim($placement->dudika->head_nip) !== '-')
                <br>NIP. {{ $placement->dudika->head_nip }}
            @endif
        </td>
        <td style="vertical-align: top; padding: 5px;">
            <strong
                style="text-decoration: underline;">{{ $placement->dudika->supervisor_name ?? '.....................................' }}</strong>
            @if (!empty($placement->dudika->supervisor_nip) && trim($placement->dudika->supervisor_nip) !== '-')
                <br>NIP. {{ $placement->dudika->supervisor_nip }}
            @endif
        </td>
    </tr>
</table>

<!-- TABEL PENGESAHAN SEKOLAH -->
<table
    style="width: 100%; margin-top: 20px; font-size:14pt; text-align: center; table-layout: fixed; border-collapse: collapse;">
    <tr>
        <td class="font-bold" style="vertical-align: middle; padding: 5px; height: 60px;">
            Kepala<br>
            {{ $school->name ?? 'SMK PGRI 1 Giri Banyuwangi' }}
        </td>
        <td class="font-bold" style="vertical-align: middle; padding: 5px; height: 60px;"><br>Pembimbing PKL SMK </td>
    </tr>
    <tr>
        <!-- Area Tanda Tangan & QR Code -->
        <td style="height: 100px; vertical-align: middle;">
            <div style="width: 125px; height: 125px; margin: 0 auto; text-align: center;">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}"
                    style="width: 125px; height: 125px; display: block; margin: 0 auto;">
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}"
                        style="width: 26px; height: 26px; margin-top: -75px; background-color: white; padding: 2px; border-radius: 4px; display: inline-block;">
                @endif
            </div>
        </td>
        <td style="height: 100px; vertical-align: middle;">
            @if ($ttdGuruBase64 && ($school->is_teacher_signature_enabled ?? true))
                <img src="{{ $ttdGuruBase64 }}" style="height: 80px; display: block; margin: 0 auto;">
            @else
                <br><br><br>
            @endif
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top; padding: 5px;">
            <strong
                style="text-decoration: underline;">{{ $placement->pengesah_ks_nama ?? $school->headmaster_name }}</strong>
            @if (!empty($placement->pengesah_ks_nip) && trim($placement->pengesah_ks_nip) !== '-')
                <br>NIP. {{ $placement->pengesah_ks_nip }}
            @endif
        </td>
        <td style="vertical-align: top; padding: 5px;">
            <strong
                style="text-decoration: underline;">{{ $placement->teacher->name . ', ' . $placement->teacher->title ?? '.....................................' }}</strong>
            @if (!empty($placement->teacher->nip) && trim($placement->teacher->nip) !== '-')
                <br>NIP. {{ $placement->teacher->nip }}
            @endif
        </td>
    </tr>
</table>
