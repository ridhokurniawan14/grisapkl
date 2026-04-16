<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Data DUDIKA</title>
    <style>
        /* Mengatur agar otomatis Landscape saat diprint */
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f3f4f6;
            text-transform: uppercase;
            font-size: 12px;
        }

        /* Style untuk teks NIP / Alamat agar lebih kecil di bawah Nama */
        .meta-text {
            font-size: 11px;
            color: #666;
            display: block;
            margin-top: 4px;
        }

        /* Tombol print disembunyikan saat masuk ke kertas */
        @media print {
            .no-print {
                display: none;
            }
        }

        .btn {
            padding: 8px 15px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-red {
            background: #ef4444;
        }

        .btn-blue {
            background: #3b82f6;
            margin-left: 5px;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button class="btn btn-red" onclick="window.history.length > 1 ? window.history.back() : window.close()">Kembali /
            Tutup</button>
        <button class="btn btn-blue" onclick="window.print()">Cetak Ulang / Save PDF</button>
    </div>

    <div class="header">
        <h1>DATA REKAPITULASI DUDIKA<br>

            SMK PGRI 1 Giri - Tahun Ajaran 2025/2026
        </h1>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" style="text-align: center;">No</th>
                <th width="30%" style="text-align: center;">Nama & Alamat DUDIKA</th>
                <th width="25%" style="text-align: center;">Data Pimpinan</th>
                <th width="25%" style="text-align: center;">Pembimbing & Kontak</th>
                <th width="15%" style="text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dudikas as $index => $d)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $d->name }}</strong>
                        <span class="meta-text">{{ $d->address ?? 'Alamat belum diisi' }}</span>
                    </td>
                    <td>
                        <strong>{{ $d->head_name ?? '-' }}</strong>
                        @if (!empty($d->head_nip))
                            <span class="meta-text">NIP/NIK: {{ $d->head_nip }}</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $d->supervisor_name ?? '-' }}</strong>
                        @if (!empty($d->supervisor_nip))
                            <span class="meta-text">NIP/NIK: {{ $d->supervisor_nip }}</span>
                        @endif
                        @if (!empty($d->supervisor_phone))
                            <span class="meta-text">HP: {{ $d->supervisor_phone }}</span>
                        @endif
                    </td>
                    <td
                        style="text-align: center; font-weight: bold; color: {{ $d->is_complete ? '#16a34a' : '#dc2626' }}">
                        {{ $d->is_complete ? 'Lengkap' : 'Belum Lengkap' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
