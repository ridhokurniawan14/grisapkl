<h2 class="text-center font-bold" style="margin-top: 30px;">LEMBAR PENGESAHAN</h2>

<div class="text-center font-bold mt-4" style="margin-top: 40px; line-height: 1.8;">
    Laporan Praktik Kerja Lapangan<br>
    Siswa {{ $school->name ?? 'SMK PGRI 1 GIRI BANYUWANGI' }}<br>
    Tahun Pelajaran {{ $placement->academicYear->name ?? '2025/2026' }}
</div>

<div style="margin-top: 50px;">
    Pada Tanggal ........................................ s.d ........................................ 20....
</div>

<table style="width: 100%; margin-top: 30px;">
    <tr>
        <td width="15%"></td>
        <td width="30%">Ditetapkan di</td>
        <td width="5%">:</td>
        <td width="50%">Banyuwangi</td>
    </tr>
    <tr>
        <td></td>
        <td>Pada Tanggal</td>
        <td>:</td>
        <td>........................................ 20....</td>
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
            .......................................................<br>
            NIP. {{ $placement->dudika->head_nip ?? '-' }}
        </td>
        <td>
            <strong
                style="text-decoration: underline;">{{ $placement->dudika->supervisor_name ?? '.....................................' }}</strong><br>
            NIP. {{ $placement->dudika->supervisor_nip ?? '-' }}
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
        <td style="height: 120px; vertical-align: middle; text-align: center;">
            <div style="width: 90px; height: 90px; margin: 0 auto;">

                <img src="data:image/svg+xml;base64,{{ $qrCode }}"
                    style="width: 90px; height: 90px; display: block;">

                @if ($school && $school->logo_path)
                    <img src="{{ public_path('storage/' . $school->logo_path) }}"
                        style="width: 24px; height: 24px; margin-top: -57px; background-color: white; padding: 2px; border-radius: 4px; display: inline-block;">
                @endif

            </div>
        </td>
        <td style="height: 120px; vertical-align: bottom;">
            @if ($placement->teacher && $placement->teacher->signature_path)
                <img src="{{ public_path('storage/' . $placement->teacher->signature_path) }}"
                    style="height: 80px; margin-bottom: 10px;">
            @else
                <br><br><br>
            @endif
        </td>
    </tr>
    <tr>
        <td>
            <strong
                style="text-decoration: underline;">{{ $placement->pengesah_ks_nama ?? $school->headmaster_name }}</strong><br>
            @if (!empty($placement->pengesah_ks_nip) && trim($placement->pengesah_ks_nip) !== '-')
                NIP. {{ $placement->pengesah_ks_nip }}
            @endif
        </td>
        <td>
            <strong
                style="text-decoration: underline;">{{ $placement->teacher->name ?? '.....................................' }}</strong><br>
            @if (!empty($placement->teacher->nip) && trim($placement->teacher->nip) !== '-')
                NIP. {{ $placement->teacher->nip }}
            @endif
        </td>
    </tr>
</table>

<div style="position: absolute; bottom: 0; right: 0; font-size: 10pt; font-style: italic; color: gray;">
    Praktik Kerja Lapangan (PKL) {{ $placement->academicYear->name ?? '2025/2026' }} | 3
</div>
