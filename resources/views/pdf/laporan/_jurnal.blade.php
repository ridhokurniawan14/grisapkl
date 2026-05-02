<style>
    /* CSS Khusus untuk Jurnal */
    .jurnal-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 10pt;
    }

    .jurnal-table th,
    .jurnal-table td {
        border: 1px solid #000;
        padding: 6px;
    }

    .jurnal-table th {
        background-color: #e8e8e8;
        /* Rata tengah horizontal & vertikal sempurna untuk Header */
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
    }

    .jurnal-table td {
        text-align: center;
        vertical-align: top;
    }

    .jurnal-table tr {
        page-break-inside: avoid;
    }

    .img-box {
        width: 60px;
        height: auto;
        border-radius: 4px;
    }

    .img-kegiatan {
        width: 90px;
        height: auto;
        border-radius: 4px;
    }

    .text-justify {
        text-align: justify !important;
    }

    .check-icon {
        color: green;
        font-weight: bold;
        font-size: 18pt;
        font-family: "DejaVu Sans", sans-serif;
    }

    .alpha-row td {
        background-color: #fff3f3;
    }
</style>

<h2 class="text-center font-bold" style="font-size: 14pt; margin-top:-25px; text-decoration: underline;">JURNAL KEGIATAN
    PKL</h2>

<!-- INFO BIODATA SISWA DI ATAS JURNAL -->
<table style="width: 100%; margin-bottom: 15px; margin-top: 20px;  font-size: 11pt;">
    <tr>
        <td style="width: 18%; vertical-align: top;">NAMA SISWA</td>
        <td style="width: 2%; vertical-align: top;">:</td>
        <td style="width: 80%; vertical-align: top; font-weight: bold;">{{ strtoupper($placement->student->name) }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">TEMPAT PKL</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ strtoupper($placement->dudika->name) }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">BIDANG/BAGIAN</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">
            {{ strtoupper($placement->pkl_field ?? '..............................................') }}</td>
    </tr>
</table>

<table class="jurnal-table">
    <thead>
        <tr>
            <th style="width: 15%;">HARI,<br>TANGGAL</th>
            <th style="width: 15%;">FOTO<br>ABSENSI</th>
            <th>URAIAN KEGIATAN</th>
            <th style="width: 20%;">FOTO KEGIATAN</th>
            <th style="width: 12%;">VALIDASI</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($placement->journals as $j)
            @php
                $isAlpha = ($j->attend_status ?? '') === 'Alpha';
                $absenImg = getBase64Image($j->attendance_photo_path ?? '');
                $kegiatanImg = getBase64Image($j->photo_path ?? '');
            @endphp
            <tr class="{{ $isAlpha ? 'alpha-row' : '' }}">
                <td>
                    {{ \Carbon\Carbon::parse($j->date)->isoFormat('dddd, D') }}<br>
                    {{ \Carbon\Carbon::parse($j->date)->isoFormat('MMMM Y') }}<br>
                    @if ($j->time)
                        <strong
                            style="margin-top: 5px; display:inline-block;">{{ \Carbon\Carbon::parse($j->time)->format('H.i') }}
                            WIB</strong>
                    @else
                        -
                    @endif
                </td>

                <td style="vertical-align: middle;">
                    @if ($absenImg)
                        <img src="{{ $absenImg }}" class="img-box">
                    @else
                        -
                    @endif
                </td>

                <td class="text-justify" style="vertical-align: middle;">
                    {{ $j->activity ?? 'Hanya Absen' }}

                    @if (!empty($j->is_valid == 0))
                        <span style="display:block; margin-top: 5px; font-size: 10pt; color: #ff0000;">
                            (Catatan Revisi : {{ $j->revision_note }})
                        </span>
                    @endif
                </td>

                <td style="vertical-align: middle;">
                    @if ($kegiatanImg)
                        <img src="{{ $kegiatanImg }}" class="img-kegiatan">
                    @else
                        -
                    @endif
                </td>

                <td style="vertical-align: middle;">
                    @if ($j->is_valid == 1)
                        <span class="check-icon">&#10004;</span>
                    @elseif ($isAlpha)
                        <span style="color:#b91c1c; font-weight:bold; font-size: 10pt;">Alpha</span>
                    @else
                        <span style="color:#b91c1c; font-weight:bold; font-size: 10pt;">Revisi</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
