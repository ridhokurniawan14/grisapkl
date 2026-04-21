<!DOCTYPE html>
<html>

<head>
    <title>Cetak Jurnal PKL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .page-break {
            page-break-after: always;
        }

        .header-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        .bio-table {
            margin-bottom: 20px;
            font-weight: bold;
        }

        .bio-table td {
            padding: 3px 5px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: top;
        }

        .data-table th {
            background-color: #f2f2f2;
        }

        /* MANTRA SAKTI 1: Mencegah baris tabel terpotong di beda halaman */
        .data-table tr {
            page-break-inside: avoid;
        }

        /* MANTRA SAKTI 2: Mengunci lebar kolom Hari/Tanggal biar konsisten */
        .data-table th:first-child,
        .data-table td:first-child {
            width: 18%;
        }

        .img-box {
            width: 80px;
            height: auto;
            border-radius: 5px;
        }

        .img-kegiatan {
            width: 120px;
            height: auto;
            border-radius: 5px;
        }

        .text-left {
            text-align: left !important;
        }

        .check-icon {
            color: green;
            font-weight: bold;
            font-size: 16px;
        }

        /* CSS untuk menyembunyikan elemen tertentu saat di-print (jika butuh) */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    @foreach ($journalsByStudent as $placementId => $journals)
        @php
            $student = $journals->first()->pklPlacement->student->name ?? '-';
            $dudika = $journals->first()->pklPlacement->dudika->name ?? '-';
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
                <td>:
                    {{ $journals->first()->pklPlacement->department ?? '..........................................................' }}
                </td>
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
                    <tr>
                        <td>
                            {{ \Carbon\Carbon::parse($j->date)->translatedFormat('l, d') }}<br>
                            {{ \Carbon\Carbon::parse($j->date)->translatedFormat('F Y') }}<br>
                            {{ \Carbon\Carbon::parse($j->time)->format('H.i') }} WIB
                        </td>
                        <td>
                            @if ($j->attendance_photo_path)
                                <img src="{{ asset('storage/' . $j->attendance_photo_path) }}" class="img-box">
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-left">{{ $j->activity ?? 'Hanya Absen' }}</td>
                        <td>
                            @if ($j->photo_path)
                                <img src="{{ asset('storage/' . $j->photo_path) }}" class="img-kegiatan">
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($j->is_valid)
                                <span class="check-icon">✔</span>
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

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
