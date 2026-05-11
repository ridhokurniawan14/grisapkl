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

    // =========================================================
    // HELPER: CEK KELENGKAPAN (Mengembalikan Array yg Kurang)
    // =========================================================
    private function checkCompleteness($placement, $student, $dudika, $teacher)
    {
        if (!$student || !$dudika || !$teacher) return ['Data Master Tidak Ditemukan'];

        $missing = [];

        // 1. Cek Siswa
        if (empty($student->nisn)) $missing[] = 'NISN Siswa';
        if (empty($student->phone)) $missing[] = 'No. HP Siswa';
        if (empty($student->birth_place)) $missing[] = 'Tempat Lahir Siswa';
        if (empty($student->birth_date)) $missing[] = 'Tanggal Lahir Siswa';
        if (empty($student->religion)) $missing[] = 'Agama Siswa';
        if (empty($student->address)) $missing[] = 'Alamat Lengkap Siswa';
        if (empty($student->father_name)) $missing[] = 'Nama Ayah';
        if (empty($student->mother_name)) $missing[] = 'Nama Ibu';
        if (empty($student->father_job)) $missing[] = 'Pekerjaan Ayah';
        if (empty($student->mother_job)) $missing[] = 'Pekerjaan Ibu';
        if (empty($student->parent_address)) $missing[] = 'Alamat Orang Tua';
        if (empty($student->parent_phone)) $missing[] = 'No. HP Orang Tua';
        if (empty($placement->pkl_field)) $missing[] = 'Bidang Keahlian / Pekerjaan Siswa';

        // 2. Cek DUDIKA
        if (empty($dudika->address)) $missing[] = 'Alamat DUDIKA';
        if (empty($dudika->head_name)) $missing[] = 'Nama Pimpinan DUDIKA';
        if (empty($dudika->supervisor_name)) $missing[] = 'Nama Pembimbing DUDIKA';
        if (empty($dudika->supervisor_phone)) $missing[] = 'No HP Pembimbing DUDIKA';

        if (!PklAssessment::where('pkl_placement_id', $placement->id)->exists()) {
            $missing[] = 'Nilai PKL belum diisi pihak DUDIKA';
        }

        // 3. Cek Guru
        if (empty($teacher->phone)) $missing[] = 'No HP Guru Pembimbing';
        if (empty($teacher->signature_path)) $missing[] = 'TTD Guru Pembimbing';

        return $missing; // Mengembalikan array teks yang kosong
    }

    public function render()
    {
        $user = Auth::user();
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

            $startDate = $placement?->start_date
                ? Carbon::parse($placement->start_date)
                : Carbon::now()->subMonths(1);

            $endDate = $placement?->end_date
                ? Carbon::parse($placement->end_date)
                : Carbon::now()->addMonths(2);

            $totalExpectedLogs = max($startDate->diffInWeekdays($endDate), 1);

            $journals = $placement?->journals ?? collect();
            $submittedLogs = $journals->count();
            $hadirCount = $journals->where('attend_status', 'Hadir')->count();
            $attendancePercent = $submittedLogs > 0
                ? round(($hadirCount / $submittedLogs) * 100)
                : 0;

            $unapprovedJournalsCount = $journals->where('is_valid', false)->count();
            $isAllJournalsApproved = ($submittedLogs > 0) && ($unapprovedJournalsCount === 0);

            $avatarPath = null;
            if (!empty($student->user->avatar)) {
                $avatarPath = asset('storage/' . $student->user->avatar);
            } elseif (!empty($student->avatar)) {
                $avatarPath = asset('storage/' . $student->avatar);
            }

            // CEK KELENGKAPAN MENDETAIL
            $placementId = $placement?->id;
            $missingFields = [];
            $isComplete = false;
            $isValidated = false;
            $fileLaporanPath = null;
            $fileLaporanUrl = null;

            if ($placement) {
                $missingFields = $this->checkCompleteness($placement, $student, $placement->dudika, $teacher);
                $isComplete = empty($missingFields); // True jika array kosong
                $isValidated = !empty($placement->pengesah_ks_nama);
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
                'log_total'                => $totalExpectedLogs,
                'phone'                    => $this->formatPhone($student->phone),
                'placement_id'             => $placementId,
                'is_complete'              => $isComplete,
                'missing_fields'           => $missingFields, // Data list kekurangannya
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
