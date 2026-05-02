@if ($coverBase64)
    <img src="{{ $coverBase64 }}" class="cover-bg">
@endif

<div class="cover-content">
    <h2 class="font-bold" style="font-size: 25pt; margin-bottom: 0;">LAPORAN</h2>
    <h2 class="font-bold" style="font-size: 22pt; margin-top: 5px;">PRAKTIK KERJA LAPANGAN (PKL)</h2>

    <div style="margin-top: 20px;">
        <span style="font-size: 16pt;">Di:</span>
        <strong style="font-size: 16pt; text-decoration: underline; word-break: break-word;">
            {{ strtoupper($placement->dudika->name) }}
        </strong>
    </div>

    <div style="margin-top: 30px;">
        <h3 class="font-bold" style="font-size: 18pt; margin-bottom: 5px;">KOMPETENSI KEAHLIAN:</h3>
        <span style="font-size: 18pt; word-break: break-word;">
            {{ $placement->student->studentClass->major->name ?? '-' }}
        </span>
    </div>

    <div style="margin-top: 50px;">
        @if ($logoBase64)
            <img src="{{ $logoBase64 }}" style="width: 150px; height: auto;">
        @else
            <div style="height: 80px;"></div>
        @endif
    </div>

    <div style="margin-top: 50px;">
        <table
            style="
        width: 75%;
        margin: 0 auto;
        text-align: left;
        font-size: 16pt;
        font-weight: bold;
        border-collapse: collapse;
    ">
            <tr>
                <td style="width: 120px; vertical-align: top; padding: 2px 0; white-space: nowrap;">NAMA</td>
                <td style="width: 20px; vertical-align: top; padding: 2px 0; white-space: nowrap;">:</td>
                <td style="vertical-align: top; padding: 2px 0; word-break: break-word; overflow-wrap: break-word;">
                    {{ strtoupper(formatStudentName($placement->student->name)) }}
                </td>
            </tr>
            <tr>
                <td style="width: 120px; vertical-align: top; padding: 2px 0; white-space: nowrap;">NIS</td>
                <td style="width: 20px; vertical-align: top; padding: 2px 0; white-space: nowrap;">:</td>
                <td style="vertical-align: top; padding: 2px 0;">{{ $placement->student->nis }}</td>
            </tr>
            <tr>
                <td style="width: 120px; vertical-align: top; padding: 2px 0; white-space: nowrap;">KELAS</td>
                <td style="width: 20px; vertical-align: top; padding: 2px 0; white-space: nowrap;">:</td>
                <td style="vertical-align: top; padding: 2px 0; word-break: break-word; overflow-wrap: break-word;">
                    {{ $placement->student->studentClass->name ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 35px; width: 100%; text-align: center; line-height: 1.2;">
        <strong style="font-size: 20pt; word-break: break-word; display: block;">
            {{ strtoupper($school->name ?? 'SMK PGRI 1 GIRI') }}
        </strong>
        <span style="font-size: 16pt; font-weight: normal;">
            {{ $school->address ?? '-' }} Telp. {{ $school->phone ?? '-' }}<br>
            {{ strtoupper($school->city ?? 'BANYUWANGI') }}<br>
            TAHUN PELAJARAN {{ $placement->academicYear->name ?? '2025/2026' }}
        </span>
    </div>
</div>
