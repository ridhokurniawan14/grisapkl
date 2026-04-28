@if ($school && $school->cover_laporan_path)
    <img src="{{ public_path('storage/' . $school->cover_laporan_path) }}" class="cover-page" style="object-fit: cover;">
@endif

<div class="cover-content">
    <h2 class="font-bold" style="font-size: 18pt; margin-bottom: 5px;">LAPORAN</h2>
    <h2 class="font-bold" style="font-size: 16pt;">PRAKTIK KERJA LAPANGAN (PKL)</h2>

    <p style="margin-top: 30px; font-size: 14pt;">Di:
        ........................................................................</p>

    <div style="margin-top: 50px;">
        <h3 class="font-bold">KOMPETENSI KEAHLIAN:</h3>
        <p style="border-bottom: 1px dotted black; display: inline-block; width: 60%; padding-bottom: 5px;">
            {{ $placement->student->studentClass->major->name ?? '.........................................' }}
        </p>
    </div>

    <div style="margin-top: 250px;">
        <table style="width: 60%; margin: 0 auto; text-align: left; font-size: 14pt; line-height: 2;">
            <tr>
                <td width="30%">NAMA</td>
                <td width="5%">:</td>
                <td width="65%">{{ $placement->student->name }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>:</td>
                <td>{{ $placement->student->nis }}</td>
            </tr>
            <tr>
                <td>KELAS</td>
                <td>:</td>
                <td>{{ $placement->student->studentClass->name ?? '-' }}</td>
            </tr>
        </table>
    </div>
</div>
