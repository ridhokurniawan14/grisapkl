<h2 class="text-center font-bold" style="font-size: 14pt; margin-top:-25px; line-height: 1.3;">
    REKAPITULASI KEHADIRAN<br>SISWA PKL
</h2>

<table style="width: 100%; margin-top: 30px; margin-bottom: 20px; font-size: 11pt; line-height: 1.6;">
    <tr>
        <td style="width: 30%;">NAMA SISWA</td>
        <td style="width: 3%;">:</td>
        <td style="width: 67%; font-weight: bold;">{{ strtoupper($placement->student->name) }}</td>
    </tr>
    <tr>
        <td>NOMOR INDUK</td>
        <td>:</td>
        <td>{{ $placement->student->nis ?? '-' }}</td>
    </tr>
    <tr>
        <td>KOMPETENSI KEAHLIAN</td>
        <td>:</td>
        <td>{{ strtoupper($placement->student->studentClass->major->name ?? '-') }}</td>
    </tr>
    <tr>
        <td>LAMA PKL</td>
        <td>:</td>
        <td>{{ \Carbon\Carbon::parse($placement->start_date)->diffInMonths(\Carbon\Carbon::parse($placement->end_date)) }}
            Bulan</td>
    </tr>
    <tr>
        <td>TEMPAT PKL</td>
        <td>:</td>
        <td>{{ strtoupper($placement->dudika->name) }}</td>
    </tr>
</table>

@php
    $hadir = $placement->journals->where('attend_status', 'Hadir')->count();
    $sakit = $placement->journals->where('attend_status', 'Sakit')->count();
    $izin = $placement->journals->where('attend_status', 'Izin')->count();
    $alfa = $placement->journals->where('attend_status', 'Alpha')->count();
    $total = $hadir + $sakit + $izin + $alfa;
@endphp

<table style="width: 100%; border-collapse: collapse; font-size: 11pt; font-weight: bold;" border="1">
    <thead>
        <tr>
            <th style="padding: 8px; width: 8%;">NO</th>
            <th style="padding: 8px; width: 42%;">URAIAN</th>
            <th style="padding: 8px; width: 25%;">BANYAKNYA</th>
            <th style="padding: 8px; width: 25%;">KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 8px; text-align: center;">1</td>
            <td style="padding: 8px;">HADIR</td>
            <td style="padding: 8px; text-align: center;">{{ $hadir }}</td>
            <td style="padding: 8px;"></td>
        </tr>
        <tr>
            <td style="padding: 8px; text-align: center;">2</td>
            <td style="padding: 8px;">SAKIT</td>
            <td style="padding: 8px; text-align: center;">{{ $sakit }}</td>
            <td style="padding: 8px;"></td>
        </tr>
        <tr>
            <td style="padding: 8px; text-align: center;">3</td>
            <td style="padding: 8px;">IJIN TERTULIS</td>
            <td style="padding: 8px; text-align: center;">{{ $izin }}</td>
            <td style="padding: 8px;"></td>
        </tr>
        <tr>
            <td style="padding: 8px; text-align: center;">4</td>
            <td style="padding: 8px;">TANPA IJIN (ALFA)</td>
            <td style="padding: 8px; text-align: center;">{{ $alfa }}</td>
            <td style="padding: 8px;"></td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 8px; text-align: right;">TOTAL &nbsp;</td>
            <td style="padding: 8px; text-align: center;">{{ $total }} Hari Kerja</td>
            <td style="padding: 8px;"></td>
        </tr>
    </tbody>
</table>

<!-- MANTRA SAKTI: page-break-inside: avoid agar Catatan & TTD selalu nempel 1 halaman -->
<div style="page-break-inside: avoid;">
    <div
        style="border: 1px solid black; width: 60%; padding: 10px; margin-top: 20px; font-weight: bold; text-align: center; font-size: 10pt;">
        CATATAN KEHADIRAN SISWA<br>DARI PEMBIMBING PKL/LAPANGAN
    </div>

    <div style="margin-top: 15px; line-height: 1; min-height: 60px; text-align: justify;">
        @if (!empty($placement->pklAssessment->attendance_notes))
            {!! nl2br(e($placement->pklAssessment->attendance_notes)) !!}
            <br>
            <span
                style="color: #666;">...................................................................................................................................................................</span>
        @else
            <span style="color: #666;">
                ...................................................................................................................................................................<br>
                ...................................................................................................................................................................<br>
                ...................................................................................................................................................................
            </span>
        @endif
    </div>

    <table style="width: 100%; margin-top: 30px; font-size: 11pt;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center;">
                {{ $school->city ?? 'Banyuwangi' }},
                {{ \Carbon\Carbon::parse($placement->end_date)->isoFormat('D MMMM Y') }}<br>
                Pembimbing PKL DUDIKA
                <br><br><br><br><br>
                <strong
                    style="text-decoration: underline;">{{ $placement->dudika->supervisor_name ?? '............................................' }}</strong>
            </td>
        </tr>
    </table>
</div>
