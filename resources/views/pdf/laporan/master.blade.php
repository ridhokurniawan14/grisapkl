<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan PKL - {{ $placement->student->name }}</title>
    <style>
        @page {
            margin: 2cm;
        }

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

        /* Hilangkan margin untuk cover agar full 1 halaman */
        .cover-page {
            position: absolute;
            top: -2cm;
            left: -2cm;
            right: -2cm;
            bottom: -2cm;
            width: 100vw;
            height: 100vh;
            z-index: -1;
        }

        .cover-content {
            position: relative;
            z-index: 10;
            padding: 4cm 2cm;
            text-align: center;
        }
    </style>
</head>

<body>

    @include('pdf.laporan._cover')
    <div class="page-break"></div>

    @include('pdf.laporan._pengesahan')
    <div class="page-break"></div>

    {{-- 
    @include('pdf.laporan._biodata')
    <div class="page-break"></div>
    @include('pdf.laporan._tatatertib')
    <div class="page-break"></div>
    --}}

</body>

</html>
