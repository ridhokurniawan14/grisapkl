<?php

namespace App\Livewire\Pembimbing;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Journal;
use App\Models\Teacher;
use App\Models\PklPlacement;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Data Jurnal - GrisaPKL')]
class Data extends Component
{
    use WithPagination;

    public $search = '';
    public $filterSiswa = 'Semua Siswa';
    public $filterStatus = 'Semua Status';
    public $startDate = '';
    public $endDate = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterSiswa()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    public function updatingStartDate()
    {
        $this->resetPage();
    }
    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $isFiltered = !empty($this->search) ||
            $this->filterSiswa !== 'Semua Siswa' ||
            $this->filterStatus !== 'Semua Status' ||
            !empty($this->startDate) ||
            !empty($this->endDate);

        $siswaList = [];
        $filteredJournals = null;

        $formatPhone = function ($phone) {
            $phone = preg_replace('/[^0-9+]/', '', (string)$phone);
            if (str_starts_with($phone, '0')) {
                return '+62' . substr($phone, 1);
            } elseif (str_starts_with($phone, '62')) {
                return '+' . $phone;
            } elseif (!str_starts_with($phone, '+') && !empty($phone)) {
                return '+62' . $phone;
            }
            return $phone;
        };

        if ($teacher) {
            $allPlacements = PklPlacement::with('student.user')
                ->where('teacher_id', $teacher->id)
                ->get();
            $siswaList = $allPlacements->map(function ($p) {
                return $p->student->user->name ?? 'Siswa';
            })->unique()->filter()->values()->toArray();

            if ($isFiltered) {
                // BUG FIX: Hilangkan kondisi whereNotNull('revision_note')
                // Sekarang semua jurnal ditarik tanpa batasan note, lalu difilter murni dari is_valid!
                $query = Journal::with(['pklPlacement.student.user', 'pklPlacement.dudika'])
                    ->whereHas('pklPlacement', function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id);
                    })
                    ->orderBy('date', 'desc')->orderBy('time', 'desc');

                if (!empty($this->startDate) && !empty($this->endDate)) {
                    $query->whereBetween('date', [$this->startDate, $this->endDate]);
                } elseif (!empty($this->startDate)) {
                    $query->where('date', '>=', $this->startDate);
                } elseif (!empty($this->endDate)) {
                    $query->where('date', '<=', $this->endDate);
                }

                // LOGIKA FILTER STATUS (1 = Disetujui, 0 = Revisi/Menunggu)
                if ($this->filterStatus === 'Disetujui') {
                    $query->where('is_valid', 1);
                } elseif ($this->filterStatus === 'Revisi') {
                    $query->where('is_valid', 0);
                }

                if ($this->filterSiswa !== 'Semua Siswa') {
                    $query->whereHas('pklPlacement.student.user', function ($q) {
                        $q->where('name', $this->filterSiswa);
                    });
                }

                if (!empty($this->search)) {
                    $searchTerm = '%' . $this->search . '%';
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('activity', 'like', $searchTerm)
                            ->orWhereHas('pklPlacement.student.user', function ($sq) use ($searchTerm) {
                                $sq->where('name', 'like', $searchTerm);
                            });
                    });
                }

                // Gunakan simplePaginate 15 per halaman biar enteng
                $results = $query->simplePaginate(15);

                $results->getCollection()->transform(function ($j) use ($formatPhone) {
                    $student = $j->pklPlacement->student;
                    $studentName = $student->user->name ?? 'Siswa';
                    $rawPhone = $student->phone ?? ($student->user->phone ?? '');

                    // Pokoknya kalau is_valid 0 (false), statusnya langsung REVISI
                    $status = $j->is_valid ? 'Disetujui' : 'Revisi';

                    $avatarPath = null;
                    if (!empty($student->user->avatar)) {
                        $avatarPath = asset('storage/' . $student->user->avatar);
                    } elseif (!empty($student->user->avatar_path)) {
                        $avatarPath = asset('storage/' . $student->user->avatar_path);
                    } elseif (!empty($student->avatar)) {
                        $avatarPath = asset('storage/' . $student->avatar);
                    } elseif (!empty($student->avatar_path)) {
                        $avatarPath = asset('storage/' . $student->avatar_path);
                    }

                    $dudikaName = $j->pklPlacement->dudika->name ?? 'DUDIKA Belum Diatur';
                    $dateRaw = Carbon::parse($j->date)->isoFormat('D MMMM YYYY');
                    $dateStr = $dateRaw . ', ' . Carbon::parse($j->time)->format('H:i') . ' WIB';

                    $waMessage = '';
                    if ($status === 'Revisi') {
                        $waMessage = urlencode("Halo {$studentName}, jurnal PKL kamu pada tanggal {$dateRaw} saat ini belum divalidasi atau perlu direvisi. Mohon cek kembali dan hubungi pembimbing DUDIKA kamu ya. Terima kasih!");
                    }

                    $images = [];
                    if ($j->photo_path) {
                        $decoded = json_decode($j->photo_path, true);
                        if (is_array($decoded)) {
                            foreach ($decoded as $img) {
                                $images[] = asset('storage/' . $img);
                            }
                        } else {
                            $images[] = asset('storage/' . $j->photo_path);
                        }
                    }

                    return [
                        'id' => $j->id,
                        'student_name' => $studentName,
                        'student_phone' => $formatPhone($rawPhone),
                        'avatar' => $avatarPath,
                        'dudika_name' => $dudikaName,
                        'date_str' => $dateStr,
                        'date_raw' => $dateRaw,
                        'attend_status' => $j->attend_status,
                        'attendance_photo' => $j->attendance_photo_path ? asset('storage/' . $j->attendance_photo_path) : null,
                        'content' => $j->activity,
                        'images' => $images,
                        'status' => $status,
                        'wa_message' => $waMessage,
                        'revision_note' => $j->revision_note,
                    ];
                });

                $filteredJournals = $results;
            }
        }

        return view('livewire.pembimbing.data', [
            'journals' => $filteredJournals,
            'siswaList' => $siswaList,
            'isFiltered' => $isFiltered,
        ]);
    }
}
