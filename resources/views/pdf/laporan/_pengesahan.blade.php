<div class="footer">
    Praktik Kerja Lapangan (PKL) {{ $placement->academicYear->name ?? '2025/2026' }} | <span class="pagenum"></span>
</div>

<h2 class="text-center font-bold" style="margin-top: 10px;">LEMBAR PENGESAHAN</h2>

<div class="text-center font-bold mt-4" style="margin-top: 30px; line-height: 1.8;">
    Laporan Praktik Kerja Lapangan<br>
    Siswa {{ $school->name ?? 'SMK PGRI 1 GIRI BANYUWANGI' }}<br>
    Tahun Pelajaran {{ $placement->academicYear->name ?? '2025/2026' }}
</div>

<div style="margin-top: 40px; font-weight: bold;">
    Pada Tanggal {{ \Carbon\Carbon::parse($placement->start_date)->isoFormat('D MMMM Y') }} s.d
    {{ \Carbon\Carbon::parse($placement->end_date)->isoFormat('D MMMM Y') }}
</div>

<table style="width: 100%; margin-top: 30px; font-weight: bold;">
    <tr>
        <td width="20%"></td>
        <td width="25%">Ditetapkan di</td>
        <td width="5%">:</td>
        <td width="50%">{{ $school->city ?? 'Banyuwangi' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Pada Tanggal</td>
        <td>:</td>
        <td>{{ \Carbon\Carbon::parse($placement->end_date)->isoFormat('D MMMM Y') }}</td>
    </tr>
</table>

<table style="width: 100%; margin-top: 50px; text-align: center;">
    <tr>
        <td width="50%" class="font-bold">Kepala DUDIKA</td>
        <td width="50%" class="font-bold">Pembimbing PKL DUDIKA</td>
    </tr>
    <tr>
        <td style="height: 100px;"></td>
        <td></td>
    </tr>
    <tr>
        <td>
            <strong
                style="text-decoration: underline;">{{ $placement->dudika->head_name ?? '.....................................' }}</strong>
            @if (!empty($placement->dudika->head_nip) && trim($placement->dudika->head_nip) !== '-')
                <br>NIP. {{ $placement->dudika->head_nip }}
            @endif
        </td>
        <td>
            <strong
                style="text-decoration: underline;">{{ $placement->dudika->supervisor_name ?? '.....................................' }}</strong>
            @if (!empty($placement->dudika->supervisor_nip) && trim($placement->dudika->supervisor_nip) !== '-')
                <br>NIP. {{ $placement->dudika->supervisor_nip }}
            @endif
        </td>
    </tr>
</table>

<table style="width: 100%; margin-top: 50px; text-align: center;">
    <tr>
        <td width="50%" class="font-bold">
            Kepala<br>
            {{ $school->name ?? 'SMK PGRI 1 Giri Banyuwangi' }}
        </td>
        <td width="50%" class="font-bold">
            Pembimbing PKL SMK<br>
            <br>
        </td>
    </tr>
    <tr>
        <td style="height: 120px; vertical-align: middle;">
            <div style="width: 100px; height: 100px; margin: 0 auto;">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}"
                    style="width: 100px; height: 100px; display: block;">
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}"
                        style="width: 26px; height: 26px; margin-top: -63px; background-color: white; padding: 2px; border-radius: 4px; display: inline-block;">
                @endif
            </div>
        </td>
        <td style="height: 120px; vertical-align: bottom;">
            @if ($ttdGuruBase64)
                <img src="{{ $ttdGuruBase64 }}" style="height: 80px; margin-bottom: 10px;">
            @else
                <br><br><br>
            @endif
        </td>
    </tr>
    <tr>
        <td>
            <strong
                style="text-decoration: underline;">{{ $placement->pengesah_ks_nama ?? $school->headmaster_name }}</strong>
            @if (!empty($placement->pengesah_ks_nip) && trim($placement->pengesah_ks_nip) !== '-')
                <br>NIP. {{ $placement->pengesah_ks_nip }}
            @endif
        </td>
        <td>
            <strong
                style="text-decoration: underline;">{{ $placement->teacher->name ?? '.....................................' }}</strong>
            @if (!empty($placement->teacher->nip) && trim($placement->teacher->nip) !== '-')
                <br>NIP. {{ $placement->teacher->nip }}
            @endif
        </td>
    </tr>
</table>
