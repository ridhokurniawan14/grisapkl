<h2 class="text-center font-bold" style="font-size: 14pt; margin-top: 20px; margin-bottom: 30px;">KETERANGAN DATA DIRI
    SISWA</h2>

<table style="width: 100%; font-size: 12pt; line-height: 1.8;">
    <tr>
        <td width="5%" style="vertical-align: top;">1.</td>
        <td width="35%" style="vertical-align: top;">Nama Siswa (Lengkap)</td>
        <td width="3%" style="vertical-align: top;">:</td>
        <td width="57%" style="vertical-align: top;">{{ $placement->student->name }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">2.</td>
        <td style="vertical-align: top;">NIS</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->nis }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">3.</td>
        <td style="vertical-align: top;">Jenis Kelamin</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">4.</td>
        <td style="vertical-align: top;">Tempat, Tanggal Lahir</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->birth_place ?? '-' }},
            {{ \Carbon\Carbon::parse($placement->student->birth_date)->isoFormat('D MMMM Y') }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">5.</td>
        <td style="vertical-align: top;">Agama</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->religion ?? '-' }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">6.</td>
        <td style="vertical-align: top;">Alamat Siswa</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->address ?? '-' }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">7.</td>
        <td colspan="3" style="vertical-align: top;">Nama Orang Tua</td>
    </tr>
    <tr>
        <td></td>
        <td style="vertical-align: top; padding-left: 20px;">a. Ayah</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->father_name ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="vertical-align: top; padding-left: 20px;">b. Ibu</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->mother_name ?? '-' }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">8.</td>
        <td colspan="3" style="vertical-align: top;">Pekerjaan Orang Tua</td>
    </tr>
    <tr>
        <td></td>
        <td style="vertical-align: top; padding-left: 20px;">a. Ayah</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->father_job ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="vertical-align: top; padding-left: 20px;">b. Ibu</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->mother_job ?? '-' }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">9.</td>
        <td style="vertical-align: top;">Alamat Orang Tua</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->parent_address ?? '-' }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">10.</td>
        <td style="vertical-align: top;">No. HP Orang Tua</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;">{{ $placement->student->parent_phone ?? '-' }}</td>
    </tr>
</table>

<!-- Bagian Tanda Tangan Siswa -->
<table style="width: 100%; margin-top: 50px;">
    <tr>
        <td width="55%"></td>
        <td width="45%" class="text-center">
            <!-- Tanggal menggunakan tanggal mulai PKL -->
            {{ $school->city ?? 'Banyuwangi' }},
            {{ \Carbon\Carbon::parse($placement->start_date)->isoFormat('D MMMM Y') }}<br>
            <br>
            Yang Membuat Keterangan
            <br><br><br><br><br>
            .......................................................<br>
            ({{ $placement->student->name }})
        </td>
    </tr>
</table>

<div style="margin-top: 30px; font-size: 11pt; font-style: italic;">
    **Diisi oleh siswa
</div>
