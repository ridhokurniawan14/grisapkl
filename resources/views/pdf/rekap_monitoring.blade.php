<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Monitoring Guru PKL</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid black;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background-color: #FFFF00;
            text-align: center;
        }

        .foto-kunjungan {
            max-width: 100%;
            height: 60px;
            object-fit: cover;
            display: block;
            margin-bottom: 5px;
            border-radius: 4px;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Style baru untuk DUDIKA */
        .nama-dudika {
            font-size: 8pt;
            font-weight: bold;
            color: #111;
            margin-bottom: 3px;
            line-height: 1.2;
        }

        .tgl-kunjungan {
            font-size: 8pt;
            color: #333;
        }
    </style>
</head>

<body>

    <h2 class="text-center font-bold" style="margin-bottom: 5px;">LAPORAN MONITORING GURU PKL</h2>
    <h3 class="text-center font-bold" style="margin-top: 0;">TAHUN AJARAN
        {{ strtoupper($activeYear->name ?? '2025/2026') }}</h3>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Nama Guru<br>Pembimbing</th>
                @foreach ($schedules as $sched)
                    <th>{{ $sched->name ?? 'Kunjungan' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($dataGuru as $guru)
                <tr>
                    <!-- Kolom Pertama: Nama Guru & Nama DUDIKA -->
                    <td>
                        <div style="font-weight: bold;">{{ $guru['nama_guru'] }}</div>
                        <div
                            style="margin-top: 8px; font-size: 8pt; color: #444; border-top: 1px dashed #ccc; padding-top: 4px;">
                            Penempatan:<br>
                            <span style="color: #000; font-weight: bold;">{{ $guru['nama_dudika_utama'] }}</span>
                        </div>
                    </td>
                    @foreach ($schedules as $sched)
                        <td>
                            @if (isset($guru['kunjungan'][$sched->id]))
                                <!-- Foto -->
                                @if ($guru['kunjungan'][$sched->id]['foto'])
                                    <img src="{{ $guru['kunjungan'][$sched->id]['foto'] }}" class="foto-kunjungan"><br>
                                @else
                                    <div style="font-size: 8pt; color: #666; font-style: italic; margin-bottom: 5px;">
                                        (Tidak ada foto)
                                    </div>
                                @endif

                                <!-- TAMPILAN BARU: Nama DUDIKA -->
                                <div class="nama-dudika">
                                    {{ $guru['kunjungan'][$sched->id]['nama_dudika'] ?? 'Nama DUDIKA' }}
                                </div>

                                <!-- Tanggal Kunjungan -->
                                <div class="tgl-kunjungan">
                                    Tgl : {{ $guru['kunjungan'][$sched->id]['tanggal'] }}
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($schedules) + 1 }}" class="text-center" style="padding: 20px;">
                        <em>Belum ada data monitoring guru di tahun ajaran ini.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
