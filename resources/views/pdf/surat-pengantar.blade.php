<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar PKL - {{ $dudika->name }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }

        .page-break {
            page-break-after: always;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid black;
            padding: 5px;
        }

        .kop-surat {
            width: 100%;
            max-height: 150px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        /* ========================================================= */
        /* CSS KHUSUS UNTUK TTD REALISTIS (MENIMPA TEKS)             */
        /* ========================================================= */
        .ttd-container {
            position: relative;
            /* Menjadi jangkar untuk elemen absolut di dalamnya */
            width: 100%;
            height: 150px;
            /* Beri ruang tinggi agar tidak tumpang tindih dengan paragraf atas */
        }

        .ttd-text-top {
            position: absolute;
            top: 0;
            right: 0;
            width: 45%;
            /* Sesuaikan lebar area TTD */
            text-align: center;
            z-index: 1;
            /* Teks berada di lapisan bawah */
        }

        .ttd-image {
            position: absolute;
            top: 15px;
            /* Geser sedikit ke bawah dari teks "Kepala..." */
            right: 50px;
            /* Geser ke kiri agar menimpa teks */
            height: 160px;
            /* Ukuran TTD JAUH LEBIH BESAR */
            z-index: 10;
            /* TTD berada di lapisan ATAS menimpa teks */
            opacity: 0.9;
            /* Sedikit transparan agar teks di bawahnya samar terlihat (realistis) */
        }

        .ttd-text-bottom {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 45%;
            text-align: center;
            z-index: 1;
            line-height: 1.2;
        }
    </style>
</head>

<body>

    @if ($school && $school->kop_surat_path)
        <img src="{{ public_path('storage/' . $school->kop_surat_path) }}" class="kop-surat">
    @else
        <h2 class="text-center" style="border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px;">
            KOP SURAT BELUM DIUPLOAD
        </h2>
    @endif

    <table>
        <tr>
            <td width="15%">Nomor</td>
            <td width="2%">:</td>
            <td width="48%">{{ $school->surat_pengantar_nomor ?? '.../M.3/SMK/.../2026' }}</td>
            <td width="35%" style="text-align: right;">Banyuwangi, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
            </td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td colspan="2">1 Bendel</td>
        </tr>
        <tr>
            <td>Hal</td>
            <td>:</td>
            <td colspan="2" class="font-bold">Pengantar Siswa Praktik Kerja Lapangan (PKL)</td>
        </tr>
    </table>

    <div class="mt-4">
        Yth. Kepala <strong>{{ $dudika->name }}</strong><br>
        {{ $dudika->address }}<br>
        di tempat
    </div>

    <div class="mt-4">
        <p>Dengan hormat,</p>
        <p style="text-align: justify;">
            Berdasarkan surat permohonan kegiatan Praktik Kerja Lapangan (PKL) yg kami ajukan, dan selanjutnya
            ditindaklanjuti dengan balasan kesediaan untuk menerima siswa/siswi dari
            <strong>{{ $school->name ?? 'Sekolah' }}</strong> melaksanakan Praktik Kerja Lapangan (PKL) di tempat
            Bapak/Ibu.
        </p>
        <p style="text-align: justify;">
            Kami menyampaikan terima kasih atas kesempatan yang diberikan kepada siswa/siswi kami, dan secara resmi
            dengan melalui surat ini kami memberangkatkan siswa/siswi sejumlah <strong>{{ $placements->count() }}
                siswa</strong> untuk melaksanakan Praktik Kerja Lapangan (PKL) mulai tanggal
            <strong>{{ \Carbon\Carbon::parse($placements->first()->start_date)->isoFormat('D MMMM') }} -
                {{ \Carbon\Carbon::parse($placements->first()->end_date)->isoFormat('D MMMM Y') }}</strong>.
        </p>
        <p style="text-align: justify;">
            Besar harapan bagi siswa kami mendapatkan pengetahuan dan pengalaman yang nantinya dapat membantu
            menumbuhkembangkan karakter dan budaya kerja, meningkatkan kompetensi, dan kemandirian serta kesiapan siswa
            kami untuk memasuki dunia kerja.
        </p>
        <p>Demikian surat pengantar dari kami, atas ijin dan kerjasama Bapak/Ibu disampaikan terima kasih.</p>
    </div>

    <div class="ttd-container mt-4">
        <div class="ttd-text-top">
            Kepala {{ $school->name ?? 'Sekolah' }}
        </div>

        @if ($school && $school->signature_path)
            <img src="{{ public_path('storage/' . $school->signature_path) }}" class="ttd-image">
        @endif

        <div class="ttd-text-bottom">
            <strong
                style="text-decoration: underline;">{{ $school->headmaster_name ?? 'NAMA KEPALA SEKOLAH' }}</strong>
            @if (!empty($school->headmaster_nip) && trim($school->headmaster_nip) !== '-' && trim($school->headmaster_nip) !== '')
                <br>NIP. {{ $school->headmaster_nip }}
            @endif
        </div>
    </div>

    <div class="page-break"></div>

    @if ($school && $school->kop_surat_path)
        <img src="{{ public_path('storage/' . $school->kop_surat_path) }}" class="kop-surat">
    @endif

    <h3 class="text-center font-bold" style="text-decoration: underline; margin-bottom: 0;">SURAT PENGANTAR SISWA</h3>
    <p class="text-center" style="margin-top: 5px; margin-bottom: 20px;">Nomor:
        {{ $school->surat_pengantar_nomor ?? '...' }}</p>

    <p>Yth. Kepala <strong>{{ $dudika->name }}</strong><br>
        {{ $dudika->address }}<br>
        Di tempat
    </p>

    <p style="text-align: justify;">Untuk kelancaran dan ketertiban pelaksanaan Praktik Kerja Lapangan (PKL), bersama
        ini kami lampirkan berkas dan format kerja siswa, sesuai daftar berikut:</p>

    <table class="table-bordered mt-4">
        <thead>
            <tr style="background-color: #cbd5e1; text-align: center;">
                <th width="5%">NO</th>
                <th width="45%">PERIHAL</th>
                <th width="20%">JUMLAH</th>
                <th width="30%">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Data Siswa PKL:<br>- Daftar nama kelompok<br>- Surat keterangan siswa / Biodata</td>
                <td class="text-center">1 Lembar<br>1 Lembar tiap siswa</td>
                <td rowspan="3" style="vertical-align: top; text-align: justify;">Dikirim dengan hormat untuk
                    diterima dan dipergunakan sebagaimana mestinya. Atas kerjasama Bapak/Ibu disampaikan terima kasih.
                </td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Format Lembar Kerja Siswa:<br>- Daftar Hadir Peserta PKL</td>
                <td class="text-center">1 Berkas di tiap-tiap siswa</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Penilaian:<br>- Format Penilaian PKL<br>- Rekapitulasi Kehadiran Siswa<br>- Lembar Observasi
                    Penilaian</td>
                <td class="text-center">1 Berkas di tiap-tiap siswa</td>
            </tr>
        </tbody>
    </table>

    <div class="ttd-container mt-4">
        <div class="ttd-text-top">
            Banyuwangi, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
            Kepala {{ $school->name ?? 'Sekolah' }}
        </div>

        @if ($school && $school->signature_path)
            <img src="{{ public_path('storage/' . $school->signature_path) }}" class="ttd-image"
                style="right: 60px; top: 30px;">
        @endif

        <div class="ttd-text-bottom">
            <strong
                style="text-decoration: underline;">{{ $school->headmaster_name ?? 'NAMA KEPALA SEKOLAH' }}</strong>
            @if (!empty($school->headmaster_nip) && trim($school->headmaster_nip) !== '-' && trim($school->headmaster_nip) !== '')
                <br>NIP. {{ $school->headmaster_nip }}
            @endif
        </div>
    </div>

    <div class="page-break"></div>

    @foreach ($placements as $placement)
        @if ($school && $school->kop_surat_path)
            <img src="{{ public_path('storage/' . $school->kop_surat_path) }}" class="kop-surat">
        @endif

        <h3 class="text-center font-bold">BIODATA SISWA PKL</h3>

        <table style="margin-top: 20px; line-height: 2;">
            <tr>
                <td width="5%">1.</td>
                <td width="30%">NAMA</td>
                <td width="2%">:</td>
                <td width="63%" class="font-bold">{{ $placement->student->name }}</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>JENIS KELAMIN</td>
                <td>:</td>
                <td>{{ $placement->student->gender == 'Laki-laki' ? 'L' : 'P' }}</td>
            </tr>
            <tr>
                <td>3.</td>
                <td>NISN</td>
                <td>:</td>
                <td>{{ $placement->student->nisn }}</td>
            </tr>
            <tr>
                <td>4.</td>
                <td>TEMPAT, TGL. LAHIR</td>
                <td>:</td>
                <td>{{ $placement->student->birth_place }},
                    {{ \Carbon\Carbon::parse($placement->student->birth_date)->isoFormat('D MMMM Y') }}</td>
            </tr>
            <tr>
                <td>5.</td>
                <td>AGAMA</td>
                <td>:</td>
                <td>{{ $placement->student->religion }}</td>
            </tr>
            <tr>
                <td>6.</td>
                <td>ALAMAT SISWA</td>
                <td>:</td>
                <td>{{ $placement->student->address }}</td>
            </tr>
            <tr>
                <td>7.</td>
                <td>KELAS/KOMP. KEAHLIAN</td>
                <td>:</td>
                <td>{{ $placement->student->studentClass->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>8.</td>
                <td>TEMPAT PKL</td>
                <td>:</td>
                <td class="font-bold">{{ $dudika->name }}</td>
            </tr>
            <tr>
                <td>9.</td>
                <td>ALAMAT TEMPAT PKL</td>
                <td>:</td>
                <td>{{ $dudika->address }}</td>
            </tr>
            <tr>
                <td>10.</td>
                <td>LAMA PKL</td>
                <td>:</td>
                <td>
                    {{ \Carbon\Carbon::parse($placement->start_date)->diffInMonths(\Carbon\Carbon::parse($placement->end_date)) }}
                    BULAN
                </td>
            </tr>
            <tr>
                <td>11.</td>
                <td>PERIODE</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($placement->start_date)->isoFormat('D MMMM') }} -
                    {{ \Carbon\Carbon::parse($placement->end_date)->isoFormat('D MMMM Y') }}</td>
            </tr>
            <tr>
                <td>12.</td>
                <td>ASAL SEKOLAH</td>
                <td>:</td>
                <td>{{ $school->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>13.</td>
                <td>ALAMAT SEKOLAH</td>
                <td>:</td>
                <td>{{ $school->address ?? '-' }} - {{ $school->city ?? '-' }}</td>
            </tr>
        </table>

        <div class="ttd-container mt-4">
            <div class="ttd-text-top">
                Banyuwangi, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                Kepala {{ $school->name ?? 'Sekolah' }}
            </div>

            @if ($school && $school->signature_path)
                <img src="{{ public_path('storage/' . $school->signature_path) }}" class="ttd-image"
                    style="right: 60px; top: 30px;">
            @endif

            <div class="ttd-text-bottom">
                <strong
                    style="text-decoration: underline;">{{ $school->headmaster_name ?? 'NAMA KEPALA SEKOLAH' }}</strong>
                @if (!empty($school->headmaster_nip) && trim($school->headmaster_nip) !== '-' && trim($school->headmaster_nip) !== '')
                    <br>NIP. {{ $school->headmaster_nip }}
                @endif
            </div>
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>

</html>
