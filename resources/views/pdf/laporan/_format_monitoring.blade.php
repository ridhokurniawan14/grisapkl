<style>
    .check-icon-green {
        color: green;
        font-weight: bold;
        font-size: 11pt;
        /* samain sama font tabel */
        font-family: "DejaVu Sans", sans-serif;
        line-height: 1;
        vertical-align: middle;
    }
</style>
<h2 class="text-center font-bold" style="font-size: 14pt; margin-top:-25px; text-decoration: underline;">FORMAT MONITORING
    PKL</h2>

<!-- BIODATA FORMAT MONITORING -->
<table style="width: 100%; margin-top: 30px; font-size: 11pt; line-height: 1.5;">
    <tr>
        <td style="width: 25%; vertical-align: top;">Nama Siswa</td>
        <td style="width: 3%; vertical-align: top;">:</td>
        <td style="width: 72%; vertical-align: top; font-weight: bold;">{{ strtoupper($placement->student->name) }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Kelas</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->studentClass->name ?? '-' }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Kompetensi Keahlian</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->studentClass->major->name ?? '-' }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Nama DUDIKA</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ strtoupper($placement->dudika->name) }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Alamat DUDIKA</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->dudika->address ?? '-' }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Waktu PKL</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">
            {{ \Carbon\Carbon::parse($placement->start_date)->isoFormat('D MMMM Y') }} s.d.
            {{ \Carbon\Carbon::parse($placement->end_date)->isoFormat('D MMMM Y') }}
        </td>
    </tr>
</table>

<!-- TABEL INDIKATOR MONITORING -->
<table class="jurnal-table" style="margin-top: 15px;">
    <thead>
        <tr>
            <th rowspan="2" style="width: 5%;">NO</th>
            <th rowspan="2" style="width: 65%;">URAIAN</th>
            <th colspan="2" style="width: 30%; vertical-align: middle; padding: 4px 2px;">
                CHECK (<span class="check-icon-green">&#10004;</span>)
            </th>
        </tr>
        <tr>
            <th style="width: 15%;">YA</th>
            <th style="width: 15%;">TIDAK</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Array Uraian Indikator
            $uraianMonitoring = [
                'Peserta didik dan pembimbing DUDIKA menyepakati program PKL',
                'Peserta didik mengisi agenda PKL secara lengkap',
                'Peserta didik mendokumentasikan proses/prosedur/data sebagai bagian dari dokumen portofolio sesuai dengan agenda kegiatan',
                'Pembelajaran PKL di DUDIKA menambah wawasan dan pengalaman nyata peserta didik dalam dunia kerja',
                'Pembelajaran PKL di DUDIKA menambah keterampilan peserta didik sesuai kompetensi keahlian',
                'Pembelajaran PKL di DUDIKA menambah pengetahuan peserta didik sesuai kompetensi keahlian',
                'Pembelajaran PKL di DUDIKA menanamkan nilai-nilai karakter budaya industri seperti disiplin, kerja keras, peduli lingkungan, peduli sosial, gotong royong, tanggung jawab, dan karakter lainnya yang relevan',
                'Pembimbingan selama pembelajaran di DUDIKA berjalan dengan baik',
                'Selama pembelajaran di DUDIKA peserta didik tidak mengalami hambatan-hambatan yang sangat berarti',
            ];
        @endphp

        @foreach ($uraianMonitoring as $idx => $teks)
            <tr>
                <td style="text-align: center; vertical-align: middle;">{{ $idx + 1 }}</td>
                <td class="text-justify" style="padding-left: 10px; padding-right: 10px; vertical-align: middle;">
                    {{ $teks }}
                </td>

                <td style="text-align: center; vertical-align: middle;">
                    <span class="check-icon">&#10004;</span>
                </td>

                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
