<h2 class="text-center font-bold" style="font-size: 14pt; margin-top:-35px; line-height: 1.2;">
    {{ strtoupper($school->name ?? 'SMKS PGRI 1 GIRI') }}<br>
    <span style="font-size: 11pt;">Tahun Ajaran {{ $placement->academicYear->name ?? '2025/2026' }}</span>
</h2>

<table style="width: 100%; margin-top: 10px; margin-bottom: 10px; font-size: 9.5pt; line-height: 1.4;">
    <tr>
        <td style="width: 25%;">Nama Murid</td>
        <td style="width: 2%;">:</td>
        <td style="width: 73%;"><strong>{{ strtoupper($placement->student->name) }}</strong></td>
    </tr>
    <tr>
        <td>NISN</td>
        <td>:</td>
        <td>{{ $placement->student->nisn ?? '-' }}</td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td>:</td>
        <td>{{ $placement->student->studentClass->name ?? '-' }}</td>
    </tr>
    <tr>
        <td>Program Keahlian</td>
        <td>:</td>
        <td>{{ strtoupper($placement->student->studentClass->major->program_keahlian ?? '-') }}</td>
    </tr>
    <tr>
        <td>Konsentrasi Keahlian</td>
        <td>:</td>
        <td>{{ strtoupper($placement->student->studentClass->major->name ?? '-') }}</td>
    </tr>
    <tr>
        <td>Tempat PKL</td>
        <td>:</td>
        <td>{{ strtoupper($placement->dudika->name) }}</td>
    </tr>
    <tr>
        <td>Tanggal PKL</td>
        <td>:</td>
        <td>
            Mulai : {{ \Carbon\Carbon::parse($placement->start_date)->isoFormat('D MMMM Y') }} &nbsp;&nbsp;&nbsp;&nbsp;
            Selesai : {{ \Carbon\Carbon::parse($placement->end_date)->isoFormat('D MMMM Y') }}
        </td>
    </tr>
    <tr>
        <td>Nama Instruktur</td>
        <td>:</td>
        <td>{{ strtoupper($placement->dudika->supervisor_name ?? '-') }}</td>
    </tr>
    <tr>
        <td>Nama Pembimbing</td>
        <td>:</td>
        <td>{{ strtoupper($placement->teacher->name ?? '-') }}{{ !empty($placement->teacher->title) ? ', ' . $placement->teacher->title : '' }}
        </td>
    </tr>
</table>

<!-- TABEL NILAI -->
<table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;" border="1">
    <thead>
        <tr>
            <th style="padding: 4px; width: 45%;">Tujuan Pembelajaran</th>
            <th style="padding: 4px; width: 10%; text-align: center;">Skor</th>
            <th style="padding: 4px; width: 45%;">Deskripsi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $raportData = collect();
            if ($placement->pklAssessment && $placement->pklAssessment->scores) {
                $raportData = $placement->pklAssessment->scores
                    ->groupBy(function ($item) {
                        return $item->assessmentIndicator->assessmentElement->tp_name ?? 'Tujuan Pembelajaran Lainnya';
                    })
                    ->map(function ($scores, $tpName) {
                        $deskripsi = match ($tpName) {
                            'Menerapkan Soft Skills yang Dibutuhkan dalam Dunia Kerja'
                                => 'Peserta didik sudah memiliki softskills sesuai harapan dalam dunia kerja',
                            'Menerapkan Norma, POS, dan K3LH yang ada pada Dunia Kerja'
                                => 'Peserta didik menerapkan norma, POS, dan K3LH yang ada di tempat kerja dengan baik',
                            'Menerapkan Kompetensi Teknis yang Sudah Dipelajari di Sekolah dan / atau Baru Belajar pada Dunia Kerja'
                                => 'Peserta didik mampu menerapkan kompetensinya di tempat kerja dengan baik',
                            'Memahami Alur Bisnis Dunia Kerja Tempat PKL dan Wawasan Wirausaha'
                                => 'Peserta didik mampu membekali kemandiriannya setelah memahami alur bisnis dunia kerja',
                            default => 'Peserta didik menguasai tujuan pembelajaran ini dengan baik.',
                        };
                        return (object) [
                            'tp_name' => $tpName,
                            'skor_rata_rata' => $scores->avg('score'),
                            'deskripsi' => $deskripsi,
                        ];
                    })
                    ->values();
            }
        @endphp

        @forelse($raportData as $index => $data)
            <tr>
                <td style="padding: 4px; vertical-align: top;">
                    {{ $index + 1 }}. {{ $data->tp_name }}
                </td>
                <td style="padding: 4px; text-align: center; vertical-align: top;">
                    {{ number_format($data->skor_rata_rata, 2) }}
                </td>
                <td style="padding: 4px; vertical-align: top; text-align: justify;">
                    {{ $data->deskripsi }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="padding: 8px; text-align: center; font-style: italic;">
                    Data nilai belum diinput.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- KOTAK CATATAN -->
<div style="border: 1px solid black; padding: 6px; margin-top: 8px; font-size: 9.5pt; min-height: 40px;">
    <strong>Catatan :</strong><br>
    @if (!empty($placement->pklAssessment->assessment_notes))
        {!! nl2br(e($placement->pklAssessment->assessment_notes)) !!}
    @else
        -
    @endif
</div>

@php
    $sakit = $placement->journals->where('attend_status', 'Sakit')->count();
    $izin = $placement->journals->where('attend_status', 'Izin')->count();
    $alfa = $placement->journals->where('attend_status', 'Alpha')->count();
@endphp

<!-- KOTAK KEHADIRAN -->
<table style="width: 45%; border-collapse: collapse; font-size: 9.5pt; margin-top: 8px;" border="1">
    <thead>
        <tr>
            <th colspan="3" style="padding: 3px; text-align: left;">Kehadiran</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 3px; width: 60%;">Sakit</td>
            <td style="padding: 3px; width: 10%; border-right: none;">:</td>
            <td style="padding: 3px; width: 30%; border-left: none;">{{ $sakit }} Hari</td>
        </tr>
        <tr>
            <td style="padding: 3px;">Ijin</td>
            <td style="padding: 3px; border-right: none;">:</td>
            <td style="padding: 3px; border-left: none;">{{ $izin }} Hari</td>
        </tr>
        <tr>
            <td style="padding: 3px;">Tanpa Keterangan</td>
            <td style="padding: 3px; border-right: none;">:</td>
            <td style="padding: 3px; border-left: none;">{{ $alfa }} Hari</td>
        </tr>
    </tbody>
</table>

<!-- AREA TANDA TANGAN -->
<table style="width: 100%; margin-top: 10px; font-size: 9.5pt; text-align: center;">
    <tr>
        <td style="width: 50%;"></td>
        <td style="width: 50%;">
            {{ $school->city ?? 'Banyuwangi' }},
            {{ \Carbon\Carbon::parse($placement->end_date)->isoFormat('D MMMM Y') }}
        </td>
    </tr>
    <tr>
        <td style="padding-top: 3px;">Guru Pembimbing</td>
        <td style="padding-top: 3px;">Pembimbing Dunia Kerja</td>
    </tr>
    <tr>
        <td style="height: 55px; vertical-align: middle;">
            @if ($ttdGuruBase64 && ($school->is_teacher_signature_enabled ?? true))
                <img src="{{ $ttdGuruBase64 }}" style="height: 40px; display: block; margin: 0 auto;">
            @endif
        </td>
        <td></td>
    </tr>
    <tr>
        <td>
            <strong
                style="text-decoration: underline;">{{ strtoupper($placement->teacher->name ?? '.....................................') }}{{ !empty($placement->teacher->title) ? ', ' . $placement->teacher->title : '' }}</strong>
        </td>
        <td>
            <strong
                style="text-decoration: underline;">{{ strtoupper($placement->dudika->supervisor_name ?? '.....................................') }}</strong>
        </td>
    </tr>
</table>
