<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;
use App\Models\Dudika;
use App\Models\Journal;
use Illuminate\Http\Request;
use App\Models\PklPlacement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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

Route::get('/journal/pdf', function (Request $request) {
    // Naikkan memory & waktu eksekusi untuk data besar
    ini_set('memory_limit', '512M');
    set_time_limit(300);

    $ids      = array_filter(explode(',', $request->query('ids', '')));
    $start    = $request->query('start');
    $end      = $request->query('end');
    $studentId = $request->query('student_id');

    // ✅ Eager load lengkap + chunk-friendly query
    $journals = Journal::with(['pklPlacement.student', 'pklPlacement.dudika'])
        ->whereIn('id', $ids)
        ->orderBy('date', 'asc')
        ->get();

    if ($start && $end && $studentId) {
        $placement    = PklPlacement::with(['student', 'dudika'])
            ->where('student_id', $studentId)
            ->first();
        $startDate    = Carbon::parse($start);
        $endDate      = Carbon::parse($end);
        $journalsKeyed = $journals->keyBy('date');
        $studentJournals = collect();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            if ($journalsKeyed->has($dateStr)) {
                $studentJournals->push($journalsKeyed->get($dateStr));
            } else {
                $dummy = new Journal([
                    'date'         => $dateStr,
                    'time'         => null,
                    'attend_status' => 'Alpha',
                    'activity'     => 'Tanpa Keterangan / Alpha',
                    'is_valid'     => true,
                ]);
                $dummy->setRelation('pklPlacement', $placement);
                $studentJournals->push($dummy);
            }
        }

        $journalsByStudent = collect([$placement->id => $studentJournals]);
    } else {
        $journalsByStudent = $journals->groupBy('pkl_placement_id');
    }

    // ✅ Generate PDF server-side — tidak perlu browser render
    $pdf = Pdf::loadView('pdf.journal', compact('journalsByStudent'))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'     => 'Arial',
            'isRemoteEnabled' => true,   // izinkan gambar lokal
            'isHtml5ParserEnabled' => true,
            'dpi'             => 96,
            'chroot'          => public_path(), // keamanan path lokal
        ]);

    $filename = 'Jurnal_PKL_' . now()->format('Ymd_His') . '.pdf';

    // ✅ Stream langsung sebagai download — tidak perlu tab baru
    return $pdf->stream($filename);
    // Atau paksa download: return $pdf->download($filename);

})->name('journal.pdf');

Route::get('/cetak/surat-pengantar/{dudika_id}', [PrintController::class, 'suratPengantar'])->name('cetak.surat-pengantar');
