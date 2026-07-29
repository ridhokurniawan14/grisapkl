<?php

namespace App\Livewire\Dudika;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Journal;
use App\Models\Dudika;
use App\Models\PklPlacement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Data Jurnal Siswa - GrisaPKL')]
class Jurnal extends Component
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

    // =======================================================
    // AKSI DUDIKA: SETUJUI JURNAL
    // =======================================================
    public function approveJournal($id)
    {
        $journal = Journal::find($id);
        if ($journal) {
            $journal->update([
                'is_valid' => 1,
                'revision_note' => null
            ]);
            session()->flash('success', 'Jurnal berhasil disetujui!');
        }
    }

    // =======================================================
    // AKSI DUDIKA: MINTA REVISI DENGAN CATATAN
    // =======================================================
    public function requestRevision($id, $note)
    {
        $journal = Journal::find($id);
        if ($journal) {
            $journal->update([
                'is_valid' => 0,
                'revision_note' => $note
            ]);
            session()->flash('success', 'Berhasil meminta revisi ke siswa!');
        }
    }

    /**
     * Generate entri "Alpha" (virtual, bukan record di DB) untuk siswa-siswa
     * yang magang di DUDIKA ini, khusus ketika filter status = "Alpha" dipilih.
     * Dibatasi rentang tanggal (default: bulan berjalan) biar nggak berat.
     */
    private function getAlphaJournals(Dudika $dudika)
    {
        $rangeStart = !empty($this->startDate)
            ? Carbon::parse($this->startDate)->startOfDay()
            : Carbon::now()->startOfMonth();

        $rangeEnd = !empty($this->endDate)
            ? Carbon::parse($this->endDate)->endOfDay()
            : Carbon::now()->endOfDay();

        $perPage = 15;
        $page = Paginator::resolveCurrentPage('page') ?: 1;

        if ($rangeStart->greaterThan($rangeEnd)) {
            return new LengthAwarePaginator([], 0, $perPage, $page, [
                'path' => Paginator::resolveCurrentPath(),
            ]);
        }

        $placementsQuery = PklPlacement::with('student.user')
            ->where('dudika_id', $dudika->id)
            ->where('status', 'Aktif');

        if ($this->filterSiswa !== 'Semua Siswa') {
            $placementsQuery->whereHas('student.user', function ($q) {
                $q->where('name', $this->filterSiswa);
            });
        }

        $placements = $placementsQuery->get();
        $alphaList = [];

        foreach ($placements as $placement) {
            $student = $placement->student;
            if (!$student) continue;

            $studentName = $student->user->name ?? 'Siswa';

            if (!$placement->start_date) continue;

            $placementStart = Carbon::parse($placement->start_date)->startOfDay();
            $todayLimit = Carbon::now()->endOfDay();
            $placementEnd = $placement->end_date
                ? Carbon::parse($placement->end_date)->endOfDay()
                : $todayLimit;
            $placementEnd = $todayLimit->lessThan($placementEnd) ? $todayLimit : $placementEnd;

            $effectiveStart = $placementStart->greaterThan($rangeStart) ? $placementStart : $rangeStart;
            $effectiveEnd = $placementEnd->lessThan($rangeEnd) ? $placementEnd : $rangeEnd;

            if ($effectiveStart->greaterThan($effectiveEnd)) continue;

            $existingDates = Journal::where('pkl_placement_id', $placement->id)
                ->whereBetween('date', [$effectiveStart->toDateString(), $effectiveEnd->toDateString()])
                ->pluck('date')
                ->map(fn($d) => Carbon::parse($d)->toDateString())
                ->unique()
                ->flip()
                ->all();

            $avatarPath = null;
            if (!empty($student->user->avatar)) {
                $avatarPath = asset('storage/' . $student->user->avatar);
            } elseif (!empty($student->avatar)) {
                $avatarPath = asset('storage/' . $student->avatar);
            }

            $period = \Carbon\CarbonPeriod::create($effectiveStart, $effectiveEnd);

            foreach ($period as $date) {
                $dateStr = $date->toDateString();
                if (isset($existingDates[$dateStr])) continue;

                $dateRaw = $date->isoFormat('D MMMM YYYY');

                $alphaList[] = [
                    'id'               => 'alpha-' . $placement->id . '-' . $dateStr,
                    'student_name'     => $studentName,
                    'avatar'           => $avatarPath,
                    'date_str'         => $dateRaw,
                    'attend_status'    => 'Alpha',
                    'attendance_photo' => null,
                    'content'          => 'Siswa tidak mengisi jurnal / absensi pada tanggal ini.',
                    'images'           => [],
                    'status'           => 'Alpha',
                    'revision_note'    => null,
                    'sort_key'         => $dateStr,
                ];
            }
        }

        usort($alphaList, fn($a, $b) => strcmp($b['sort_key'], $a['sort_key']));

        $total = count($alphaList);
        $items = array_slice($alphaList, ($page - 1) * $perPage, $perPage);

        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
        ]);
    }

    public function render()
    {
        $user = Auth::user();
        $dudika = Dudika::where('user_id', $user->id)->first();

        $isFiltered = !empty($this->search) ||
            $this->filterSiswa !== 'Semua Siswa' ||
            $this->filterStatus !== 'Semua Status' ||
            !empty($this->startDate) ||
            !empty($this->endDate);

        $siswaList = [];
        $filteredJournals = null;

        if ($dudika) {
            // Ambil Siswa yang magang di DUDIKA ini saja
            $allPlacements = PklPlacement::with('student.user')
                ->where('dudika_id', $dudika->id)
                ->where('status', 'Aktif')
                ->get();

            $siswaList = $allPlacements->map(function ($p) {
                return $p->student->user->name ?? 'Siswa';
            })->unique()->filter()->values()->toArray();

            if ($isFiltered) {
                if ($this->filterStatus === 'Alpha') {
                    $filteredJournals = $this->getAlphaJournals($dudika);
                } else {
                    $query = Journal::with(['pklPlacement.student.user'])
                        ->whereHas('pklPlacement', function ($q) use ($dudika) {
                            $q->where('dudika_id', $dudika->id);
                        })
                        ->orderBy('date', 'desc')->orderBy('time', 'desc');

                    if (!empty($this->startDate) && !empty($this->endDate)) {
                        $query->whereBetween('date', [$this->startDate, $this->endDate]);
                    } elseif (!empty($this->startDate)) {
                        $query->where('date', '>=', $this->startDate);
                    } elseif (!empty($this->endDate)) {
                        $query->where('date', '<=', $this->endDate);
                    }

                    // KHUSUS DUDIKA, "Menunggu" harus dipisah dari "Revisi" agar tau mana yang belum disentuh sama sekali
                    if ($this->filterStatus === 'Disetujui') {
                        $query->where('is_valid', 1);
                    } elseif ($this->filterStatus === 'Revisi') {
                        $query->where('is_valid', 0)->whereNotNull('revision_note');
                    } elseif ($this->filterStatus === 'Menunggu') {
                        $query->where('is_valid', 0)->whereNull('revision_note');
                    }

                    if ($this->filterSiswa !== 'Semua Siswa') {
                        $query->whereHas('pklPlacement.student.user', function ($q) {
                            $q->where('name', $this->filterSiswa);
                        });
                    }

                    if (!empty($this->search)) {
                        $searchTerm = '%' . $this->search . '%';
                        $query->where('activity', 'like', $searchTerm);
                    }

                    $results = $query->simplePaginate(15);

                    $results->getCollection()->transform(function ($j) {
                        $student = $j->pklPlacement->student;
                        $studentName = $student->user->name ?? 'Siswa';

                        if ($j->is_valid) {
                            $status = 'Disetujui';
                        } elseif (!$j->is_valid && !empty($j->revision_note)) {
                            $status = 'Revisi';
                        } else {
                            $status = 'Menunggu';
                        }

                        $avatarPath = null;
                        if (!empty($student->user->avatar)) {
                            $avatarPath = asset('storage/' . $student->user->avatar);
                        } elseif (!empty($student->avatar)) {
                            $avatarPath = asset('storage/' . $student->avatar);
                        }

                        $dateRaw = Carbon::parse($j->date)->isoFormat('D MMMM YYYY');
                        $dateStr = $dateRaw . ', ' . Carbon::parse($j->time)->format('H:i') . ' WIB';

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
                            'avatar' => $avatarPath,
                            'date_str' => $dateStr,
                            'attend_status' => $j->attend_status,
                            'attendance_photo' => $j->attendance_photo_path ? asset('storage/' . $j->attendance_photo_path) : null,
                            'content' => $j->activity,
                            'images' => $images,
                            'status' => $status,
                            'revision_note' => $j->revision_note, // Tambahan field catatan revisi
                        ];
                    });

                    $filteredJournals = $results;
                }
            }
        }

        return view('livewire.dudika.jurnal', [
            'journals' => $filteredJournals,
            'siswaList' => $siswaList,
            'isFiltered' => $isFiltered,
        ]);
    }
}
