<?php

namespace App\Livewire\Pembimbing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\PklPlacement;
use App\Models\SchoolProfile;
use App\Models\PklAssessment;
use App\Jobs\GenerateLaporanPdfJob;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Daftar Siswa - GrisaPKL')]
class Siswa extends Component
{
    public $search = '';
    public $filterDudika = 'Semua Instansi';

    // =========================================================
    // FUNGSI AKSI: VALIDASI & GENERATE PDF
    // =========================================================
    public function validasiLaporan($placementId)
    {
        $placement = PklPlacement::find($placementId);
        if ($placement) {
            $school = SchoolProfile::first();
            $placement->update([
                'pengesah_ks_nama' => $school->headmaster_name ?? 'Kepala Sekolah',
                'pengesah_ks_nip' => $school->headmaster_nip ?? '-'
            ]);
            session()->flash('success', 'Laporan berhasil divalidasi!');
        }
    }

    public function batalValidasi($placementId)
    {
        $placement = PklPlacement::find($placementId);
        if ($placement) {
            $placement->update([
                'pengesah_ks_nama' => null,
                'pengesah_ks_nip' => null
            ]);
            session()->flash('success', 'Validasi laporan dibatalkan.');
        }
    }

    public function generateLaporan($placementId)
    {
        $placement = PklPlacement::find($placementId);
        if ($placement) {
            if ($placement->file_laporan_path && $placement->file_laporan_path !== 'processing') {
                if (Storage::disk('public')->exists($placement->file_laporan_path)) {
                    Storage::disk('public')->delete($placement->file_laporan_path);
                }
            }
            $placement->update(['file_laporan_path' => 'processing']);
            GenerateLaporanPdfJob::dispatch($placement->id, Auth::id());
            session()->flash('success', 'Laporan sedang di-generate di latar belakang! Tunggu beberapa saat.');
        }
    }

    public function downloadLaporan($placementId)
    {
        $placement = PklPlacement::find($placementId);
        if ($placement && $placement->file_laporan_path) {
            if (Storage::disk('public')->exists($placement->file_laporan_path)) {
                return Storage::disk('public')->download($placement->file_laporan_path);
            }
        }

        session()->flash('success', 'File PDF tidak ditemukan atau sedang diproses!');
    }

    // =========================================================
    // HELPER: CEK KELENGKAPAN (Mengembalikan Array yg Kurang)
    // =========================================================
    private function checkCompleteness($placement, $student, $dudika, $teacher)
    {
        if (!$student || !$dudika || !$teacher) return ['Data Master Tidak Ditemukan'];

        $missing = [];

        // 1. Cek Siswa
        if (empty($student->nisn))           $missing[] = 'NISN Siswa';
        if (empty($student->phone))          $missing[] = 'No. HP Siswa';
        if (empty($student->birth_place))    $missing[] = 'Tempat Lahir Siswa';
        if (empty($student->birth_date))     $missing[] = 'Tanggal Lahir Siswa';
        if (empty($student->religion))       $missing[] = 'Agama Siswa';
        if (empty($student->address))        $missing[] = 'Alamat Lengkap Siswa';
        if (empty($student->father_name))    $missing[] = 'Nama Ayah';
        if (empty($student->mother_name))    $missing[] = 'Nama Ibu';
        if (empty($student->father_job))     $missing[] = 'Pekerjaan Ayah';
        if (empty($student->mother_job))     $missing[] = 'Pekerjaan Ibu';
        if (empty($student->parent_address)) $missing[] = 'Alamat Orang Tua';
        if (empty($student->parent_phone))   $missing[] = 'No. HP Orang Tua';
        if (empty($placement->pkl_field))    $missing[] = 'Bidang Keahlian / Pekerjaan Siswa';

        // 2. Cek DUDIKA
        if (empty($dudika->address))          $missing[] = 'Alamat DUDIKA';
        if (empty($dudika->head_name))        $missing[] = 'Nama Pimpinan DUDIKA';
        if (empty($dudika->supervisor_name))  $missing[] = 'Nama Pembimbing DUDIKA';
        if (empty($dudika->supervisor_phone)) $missing[] = 'No HP Pembimbing DUDIKA';

        if (!PklAssessment::where('pkl_placement_id', $placement->id)->exists()) {
            $missing[] = 'Nilai PKL belum diisi pihak DUDIKA';
        }

        // 3. Cek Guru
        if (empty($teacher->phone))          $missing[] = 'No HP Guru Pembimbing';
        if (empty($teacher->signature_path)) $missing[] = 'TTD Guru Pembimbing';

        return $missing;
    }

    public function render()
    {
        $user    = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return view('livewire.pembimbing.siswa', [
                'siswaList'  => collect(),
                'dudikaList' => [],
                'totalSiswa' => 0,
            ]);
        }

        $studentsQuery = Student::with([
            'user',
            'pklPlacements' => function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)
                    ->with(['dudika', 'journals']);
            },
        ])
            ->whereHas('pklPlacements', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->get();

        $allSiswa = $studentsQuery->map(function ($student) use ($teacher) {

            $placement = $student->pklPlacements->first();

            // ── Logika Tanggal PKL ──────────────────────────────────────
            $startDate = $placement?->start_date
                ? Carbon::parse($placement->start_date)->startOfDay()
                : Carbon::now()->startOfDay();

            $endDate = $placement?->end_date
                ? Carbon::parse($placement->end_date)->endOfDay()
                : Carbon::now()->addMonths(6)->endOfDay();

            $today     = Carbon::now()->endOfDay();
            $limitDate = $today->lessThan($endDate) ? $today->copy() : $endDate->copy();

            // ── Jurnal & Filter Rentang Tanggal PKL ────────────────────
            $journals = $placement?->journals ?? collect();

            $journalsInRange = $journals->filter(function ($j) use ($startDate, $limitDate) {
                $jDate = Carbon::parse($j->date);
                return $jDate->greaterThanOrEqualTo($startDate)
                    && $jDate->lessThanOrEqualTo($limitDate);
            });

            // ── Rekapan Hadir / Izin / Sakit / Libur ───────────────────
            $h = $journalsInRange->where('attend_status', 'Hadir')->where('is_valid', true)->count();
            $i = $journalsInRange->where('attend_status', 'Izin')->count();
            $s = $journalsInRange->where('attend_status', 'Sakit')->count();
            $l = $journalsInRange->where('attend_status', 'Libur')->count();

            // ── Hitung Alpha: SEMUA hari kalender (Senin s/d Minggu) ───
            // Siswa PKL wajib absen setiap hari, termasuk hari libur
            // (status Libur/Sakit/Izin pun wajib tetap diinput).
            // Alpha dihitung hanya dari hari yang SUDAH LEWAT (s/d hari ini),
            // karena hari yang belum terjadi belum bisa dianggap "bolong".
            $daysPassed = $startDate->lessThanOrEqualTo($limitDate)
                ? (int) $startDate->diffInDays($limitDate) + 1
                : 0;

            // Hitung tanggal unik yang sudah ada jurnalnya (semua hari, dalam rentang PKL saja)
            $loggedDays = $journalsInRange
                ->pluck('date')
                ->map(fn($d) => Carbon::parse($d)->toDateString())
                ->unique()
                ->count();

            $alpha = max(0, $daysPassed - $loggedDays);

            // log_count = hari yang sudah diisi jurnal (s/d hari ini)
            $submittedLogs = $loggedDays;

            // log_total = TOTAL keseluruhan hari PKL (dari start_date s/d end_date, full periode)
            // Ini dipakai untuk progress "X dari Y hari PKL", bukan cuma s/d hari ini.
            $totalPklDays = $startDate->lessThanOrEqualTo($endDate)
                ? (int) $startDate->diffInDays($endDate) + 1
                : 0;

            // ── Persentase Kehadiran ────────────────────────────────────
            // Dibagi $daysPassed (hari yang sudah lewat, termasuk yang Alpha/bolong),
            // bukan $submittedLogs, supaya Alpha ikut menurunkan persentase kehadiran.
            $attendancePercent       = $daysPassed > 0 ? round(($h / $daysPassed) * 100) : 0;
            $unapprovedJournalsCount = $journalsInRange->where('is_valid', false)->count();
            $isAllJournalsApproved   = ($submittedLogs > 0) && ($unapprovedJournalsCount === 0);

            // ── Logika Avatar ───────────────────────────────────────────
            $avatarPath = null;
            if (!empty($student->user->avatar)) {
                $avatarPath = asset('storage/' . $student->user->avatar);
            } elseif (!empty($student->avatar)) {
                $avatarPath = asset('storage/' . $student->avatar);
            }

            // ── Cek Kelengkapan Data Laporan ────────────────────────────
            $placementId     = $placement?->id;
            $missingFields   = [];
            $isComplete      = false;
            $isValidated     = false;
            $fileLaporanPath = null;
            $fileLaporanUrl  = null;

            if ($placement) {
                $missingFields   = $this->checkCompleteness($placement, $student, $placement->dudika, $teacher);
                $isComplete      = empty($missingFields);
                $isValidated     = !empty($placement->pengesah_ks_nama);
                $fileLaporanPath = $placement->file_laporan_path;
                if ($fileLaporanPath && $fileLaporanPath !== 'processing') {
                    $fileLaporanUrl = Storage::url($fileLaporanPath);
                }
            }

            return [
                'id'                       => $student->id,
                'name'                     => $student->user?->name ?? 'Siswa',
                'dudika_name'              => $placement?->dudika?->name ?? 'Belum Ada Instansi',
                'pkl_field'                => $placement?->pkl_field ?? null,
                'avatar'                   => $avatarPath,
                'attendance_percent'       => $attendancePercent,
                'log_count'                => $submittedLogs,
                'log_total'                => $totalPklDays, // total keseluruhan hari PKL (start_date s/d end_date)
                'recap'                    => ['H' => $h, 'I' => $i, 'S' => $s, 'L' => $l, 'A' => $alpha],
                'phone'                    => $this->formatPhone($student->phone),
                'placement_id'             => $placementId,
                'is_complete'              => $isComplete,
                'missing_fields'           => $missingFields,
                'is_all_journals_approved' => $isAllJournalsApproved,
                'is_validated'             => $isValidated,
                'file_laporan_path'        => $fileLaporanPath,
                'file_laporan_url'         => $fileLaporanUrl,
            ];
        });

        $dudikaList = $allSiswa->pluck('dudika_name')->filter()->unique()->values()->toArray();

        $filteredSiswa = $allSiswa->filter(function ($item) {
            $matchSearch = empty($this->search)
                || stripos($item['name'], $this->search) !== false;
            $matchDudika = $this->filterDudika === 'Semua Instansi'
                || $item['dudika_name'] === $this->filterDudika;
            return $matchSearch && $matchDudika;
        });

        return view('livewire.pembimbing.siswa', [
            'siswaList'  => $filteredSiswa,
            'dudikaList' => $dudikaList,
            'totalSiswa' => $allSiswa->count(),
        ]);
    }

    private function formatPhone(?string $phone): ?string
    {
        if (empty($phone)) return null;
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        if (strlen($phone) < 10) return null;
        return $phone;
    }
}
