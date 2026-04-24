<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Cetak Jurnal PKL</title>
    <style>
        body {
            font-family: Arial, "DejaVu Sans", sans-serif;
            font-size: 11px;
        }

        .page-break {
            page-break-after: always;
        }

        .header-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 16px;
            text-decoration: underline;
        }

        .bio-table {
            margin-bottom: 16px;
            font-weight: bold;
        }

        .bio-table td {
            padding: 2px 4px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: top;
        }

        .data-table th {
            background-color: #e8e8e8;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .data-table th:first-child,
        .data-table td:first-child {
            width: 18%;
        }

        /* ✅ Fixed width untuk foto — cegah DomPDF overflow */
        .img-box {
            width: 70px;
            height: auto;
        }

        .img-kegiatan {
            width: 100px;
            height: auto;
        }

        .text-left {
            text-align: left !important;
        }

        .check-icon {
            color: green;
            font-weight: bold;
            font-size: 14px;
        }

        .alpha-row td {
            background-color: #fff3f3;
        }
    </style>
</head>

<body>

    @foreach ($journalsByStudent as $placementId => $journals)
        @php
            $first = $journals->first();
            $student = $first->pklPlacement->student->name ?? '-';
            $dudika = $first->pklPlacement->dudika->name ?? '-';
            $dept = $first->pklPlacement->pkl_field ?? '..............................................';
        @endphp

        <div class="header-title">JURNAL KEGIATAN PKL</div>

        <table class="bio-table">
            <tr>
                <td>NAMA SISWA</td>
                <td>: {{ $student }}</td>
            </tr>
            <tr>
                <td>TEMPAT PKL</td>
                <td>: {{ $dudika }}</td>
            </tr>
            <tr>
                <td>BIDANG/BAGIAN</td>
                <td>: {{ $dept }}</td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th>HARI, TANGGAL</th>
                    <th>Foto Absensi</th>
                    <th>URAIAN KEGIATAN</th>
                    <th>Foto Kegiatan</th>
                    <th>Validasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($journals as $j)
                    @php
                        $isAlpha = ($j->attend_status ?? '') === 'Alpha';
                    @endphp
                    <tr class="{{ $isAlpha ? 'alpha-row' : '' }}">
                        <td>
                            {{ \Carbon\Carbon::parse($j->date)->translatedFormat('l, d') }}<br>
                            {{ \Carbon\Carbon::parse($j->date)->translatedFormat('F Y') }}<br>
                            @if ($j->time)
                                {{ \Carbon\Carbon::parse($j->time)->format('H.i') }} WIB
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            {{-- ✅ Gunakan path lokal, bukan URL HTTP --}}
                            @if ($j->attendance_photo_path && file_exists(storage_path('app/public/' . $j->attendance_photo_path)))
                                <img src="{{ storage_path('app/public/' . $j->attendance_photo_path) }}" class="img-box">
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-left">{{ $j->activity ?? 'Hanya Absen' }}</td>
                        <td>
                            @if ($j->photo_path && file_exists(storage_path('app/public/' . $j->photo_path)))
                                <img src="{{ storage_path('app/public/' . $j->photo_path) }}" class="img-kegiatan">
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($j->is_valid == 1)
                                <span class="check-icon" style="font-size: 30px">✓</span>
                            @else
                                <span style="color:red">Revisi</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>

</html>
