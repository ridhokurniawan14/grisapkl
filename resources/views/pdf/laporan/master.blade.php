<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan PKL - {{ $placement->student->name }}</title>
    <style>
        /* Ukuran Kertas A4 & Margin Standar Laporan */
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

        /* Sihir Page Number Dinamis DOMPDF */
        .pagenum:before {
            content: counter(page);
        }

        /* Footer Posisi Fixed (Otomatis muncul di bawah setiap halaman yang diberi class ini) */
        .footer {
            position: fixed;
            bottom: -1cm;
            right: 0;
            font-size: 10pt;
            font-style: italic;
            color: gray;
        }

        /* Cover Image Absolut (Tidak memakan spasi dokumen) */
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
            padding-top: 2cm;
        }
    </style>
</head>

<body>

    @php
        // =========================================================================
        // MANTRA SAKTI: CONVERTER GAMBAR KE BASE64 (Anti-Bocor DOMPDF)
        // =========================================================================
        function getBase64Image($path)
        {
            $fullPath = public_path('storage/' . $path);
            if ($path && file_exists($fullPath) && !is_dir($fullPath)) {
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

    @include('pdf.laporan._cover')
    <div class="page-break"></div>

    @include('pdf.laporan._pengesahan')
    <div class="page-break"></div>

</body>

</html>
