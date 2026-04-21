<?php

use Illuminate\Support\Facades\Route;
use App\Models\Dudika;
use App\Models\Journal;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk Cetak PDF DUDIKA
Route::get('/dudika/print', function (Request $request) {
    // Mengecek apakah ada ID khusus yang dikirim (Cetak Massal/Terpilih)
    if ($request->has('ids')) {
        $ids = explode(',', $request->ids);
        $dudikas = Dudika::whereIn('id', $ids)->get();
    } else {
        // Jika tidak ada ID, berarti Cetak Semua
        $dudikas = Dudika::all();
    }

    return view('pdf.dudika', compact('dudikas'));
})->name('dudika.print');

Route::get('/journal/download', function (Request $request) {
    // Kita ambil relasinya sekalian biar pemanggilan nama siswa/dudika di PDF gampang
    if ($request->has('ids')) {
        $ids = explode(',', $request->ids);
        $journals = Journal::with(['pklPlacement.student', 'pklPlacement.dudika'])->whereIn('id', $ids)->get();
    } else {
        $journals = Journal::with(['pklPlacement.student', 'pklPlacement.dudika'])->get();
    }

    // Nanti pastikan kamu membuat file view 'pdf.journal' ya bro!
    return view('pdf.journal', compact('journals'));
})->name('journal.download');

Route::get('/journal/pdf', function (Illuminate\Http\Request $request) {
    $ids = explode(',', $request->query('ids', ''));
    $start = $request->query('start');
    $end = $request->query('end');
    $studentId = $request->query('student_id');

    $journals = App\Models\Journal::with(['pklPlacement.student', 'pklPlacement.dudika'])
        ->whereIn('id', $ids)->orderBy('date', 'asc')->get();

    // JIKA USER MEMFILTER 1 SISWA SPESIFIK & RANGE TANGGAL
    if ($start && $end && $studentId) {
        $placement = App\Models\PklPlacement::with(['student', 'dudika'])->where('student_id', $studentId)->first();
        $startDate = \Carbon\Carbon::parse($start);
        $endDate = \Carbon\Carbon::parse($end);

        $journalsKeyed = $journals->keyBy('date');
        $studentJournals = collect();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            if ($journalsKeyed->has($dateStr)) {
                $studentJournals->push($journalsKeyed->get($dateStr));
            } else {
                // REVISI DATA ALPHA UNTUK PDF
                $dummy = new App\Models\Journal([
                    'date' => $dateStr,
                    'time' => null,
                    'attend_status' => 'Alpha',
                    'activity' => 'Tanpa Keterangan / Alpha', // REVISI TEKS
                    'is_valid' => true, // REVISI: Dianggap Valid Default
                ]);
                $dummy->setRelation('pklPlacement', $placement);
                $studentJournals->push($dummy);
            }
        }
        $journalsByStudent = collect([$placement->id => $studentJournals]);
    } else {
        $journalsByStudent = $journals->groupBy('pkl_placement_id');
    }

    return view('pdf.journal', compact('journalsByStudent'));
})->name('journal.pdf');
