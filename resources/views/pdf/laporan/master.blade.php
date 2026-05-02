<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan PKL - {{ $placement->student->name }}</title>
    <style>
        @page {
            margin: 2.5cm 2cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
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

        .cover-bg {
            position: absolute;
            top: -2.5cm;
            left: -2cm;
            width: 21cm;
            height: 29.7cm;
            z-index: -10;
        }

        .cover-content {
            position: relative;
            width: 100%;
            text-align: center;
            padding-top: 0px;
            /* Batasi tinggi cover agar tidak overflow ke halaman berikut */
            max-height: 24.7cm;
            /* 29.7cm - margin 2.5cm top - 2.5cm bottom */
            overflow: hidden;
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    @php
        function getBase64Image($path)
        {
            if (!$path) {
                return null;
            }
            if (str_starts_with($path, 'data:image')) {
                return $path;
            }
            $fullPath = public_path('storage/' . $path);
            if (file_exists($fullPath) && !is_dir($fullPath)) {
                $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                $data = file_get_contents($fullPath);
                return 'data:image/' . $ext . ';base64,' . base64_encode($data);
            }
            return null;
        }

        $coverBase64 = getBase64Image($school->cover_laporan_path ?? '');
        $logoBase64 = getBase64Image($school->logo_path ?? '');
        $ttdGuruBase64 = getBase64Image($placement->teacher->signature_path ?? '');
    @endphp

    <!-- 1. COVER -->
    @include('pdf.laporan._cover')
    <div class="page-break"></div>

    <!-- 2. LEMBAR PENGESAHAN -->
    @include('pdf.laporan._pengesahan')
    <div class="page-break"></div>

    <!-- 3. KETERANGAN DATA DIRI SISWA -->
    @include('pdf.laporan._biodata')
    <div class="page-break"></div>

    <!-- 4. TATA TERTIB PKL -->
    @include('pdf.laporan._tatatertib')
    <div class="page-break"></div>

    <!-- 5. TUJUAN KHUSUS PKL -->
    @include('pdf.laporan._tujuan')
    <div class="page-break"></div>

    <!-- 6. JURNAL KEGIATAN -->
    @include('pdf.laporan._jurnal')
    <div class="page-break"></div>

    <!-- 7. LEMBAR MONITORING -->
    @include('pdf.laporan._lembar_monitoring')
    <div class="page-break"></div>

    <!-- 8. FORMAT MONITORING -->
    @include('pdf.laporan._format_monitoring')
    <div class="page-break"></div>

    <!-- 9. REKAPITULASI KEHADIRAN -->
    @include('pdf.laporan._rekap_kehadiran')
    <div class="page-break"></div>

    <!-- 10. RAPORT NILAI SISWA -->
    @include('pdf.laporan._raport')

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                if ($PAGE_NUM > 1) {
                    $font = $fontMetrics->get_font("serif", "italic");
                    $size = 12;
                    $text = "Praktik Kerja Lapangan (PKL) {{ $placement->academicYear->name ?? '2025/2026' }} | " . $PAGE_NUM;
                    $width = $fontMetrics->get_text_width($text, $font, $size);
                    
                    // Koordinat X (kanan) dan Y (bawah)
                    $x = $pdf->get_width() - $width - 50;
                    $y = $pdf->get_height() - 35;
                    
                    // Cetak teks ke PDF
                    $pdf->text($x, $y, $text, $font, $size, array(0.5, 0.5, 0.5));
                }
            ');
        }
    </script>
</body>

</html>
