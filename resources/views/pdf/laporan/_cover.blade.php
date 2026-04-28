@if ($coverBase64)
    <img src="{{ $coverBase64 }}" class="cover-bg">
@endif

<div class="cover-content">
    <h2 class="font-bold" style="font-size: 16pt; margin-bottom: 0;">LAPORAN</h2>
    <h2 class="font-bold" style="font-size: 16pt; margin-top: 5px;">PRAKTIK KERJA LAPANGAN (PKL)</h2>

    <div style="margin-top: 20px;">
        <span style="font-size: 14pt;">Di:</span><br>
        <strong style="font-size: 16pt; text-decoration: underline;">{{ strtoupper($placement->dudika->name) }}</strong>
    </div>

    <div style="margin-top: 40px;">
        <h3 class="font-bold" style="font-size: 14pt; margin-bottom: 5px;">KOMPETENSI KEAHLIAN:</h3>
        <span style="font-size: 14pt;">{{ $placement->student->studentClass->major->name ?? '-' }}</span>
    </div>

    <div style="margin-top: 60px;">
        @if ($logoBase64)
            <img src="{{ $logoBase64 }}" style="width: 150px; height: auto;">
        @else
            <br><br><br><br><br>
        @endif
    </div>

    <div style="margin-top: 70px;">
        <table
            style="width: 65%; margin: 0 auto; text-align: left; font-size: 14pt; line-height: 2; font-weight: bold;">
            <tr>
                <td width="30%">NAMA</td>
                <td width="5%">:</td>
                <td width="65%">{{ strtoupper($placement->student->name) }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>:</td>
                <td>{{ $placement->student->nis }}</td>
            </tr>
            <tr>
                <td>KELAS</td>
                <td>:</td>
                <td>{{ $placement->student->studentClass->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div style="position: absolute; bottom: -2cm; width: 100%; text-align: center; font-size: 14pt; line-height: 1.2;">
        <strong style="font-size: 16pt;">{{ strtoupper($school->name ?? 'SMK PGRI 1 GIRI BANYUWANGI') }}</strong><br>
        <span style="font-size: 11pt; font-weight: normal;">
            {{ $school->address ?? '-' }} Telp. {{ $school->phone ?? '-' }}<br>
            {{ strtoupper($school->city ?? 'BANYUWANGI') }}<br>
            TAHUN PELAJARAN {{ $placement->academicYear->name ?? '2025/2026' }}
        </span>
    </div>
</div>
