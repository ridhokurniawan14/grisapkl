<?php

use App\Http\Controllers\PrintController;
use App\Livewire\Auth\LoginUniversal;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Dudika;
use App\Models\Journal;
use Illuminate\Http\Request;
use App\Models\PklPlacement;
use App\Models\SchoolProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

// ==========================================================
// MANTRA SAKTI: Pintu Gerbang Utama yang Cerdas
// ==========================================================
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole(['super_admin', 'humas'])) return redirect('/admin');
        if ($user->hasRole('siswa')) return redirect('/siswa/absen');
        return redirect('/pembimbing/dashboard');
    }
    return redirect('/login');
});

Route::get('/login', LoginUniversal::class)->name('login');

// ==========================================================
// RUTE RAHASIA (WAJIB LOGIN UNTUK MENGAKSES)
// ==========================================================
Route::middleware(['auth'])->group(function () {

    // Route untuk Cetak PDF DUDIKA
    Route::get('/dudika/print', function (Request $request) {
        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $dudikas = Dudika::whereIn('id', $ids)->get();
        } else {
            $dudikas = Dudika::all();
        }
        return view('pdf.dudika', compact('dudikas'));
    })->name('dudika.print');

    Route::get('/journal/download', function (Request $request) {
        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $journals = Journal::with(['pklPlacement.student', 'pklPlacement.dudika'])->whereIn('id', $ids)->get();
        } else {
            $journals = Journal::with(['pklPlacement.student', 'pklPlacement.dudika'])->get();
        }
        return view('pdf.journal', compact('journals'));
    })->name('journal.download');

    Route::get('/journal/pdf', function (Request $request) {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $ids      = array_filter(explode(',', $request->query('ids', '')));
        $start    = $request->query('start');
        $end      = $request->query('end');
        $studentId = $request->query('student_id');

        $journals = Journal::with(['pklPlacement.student', 'pklPlacement.dudika'])
            ->whereIn('id', $ids)
            ->orderBy('date', 'asc')
            ->get();

        if ($start && $end && $studentId) {
            $placement    = PklPlacement::with(['student', 'dudika'])
                ->where('student_id', $studentId)
                ->where('status', 'Aktif')
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

        $pdf = Pdf::loadView('pdf.journal', compact('journalsByStudent'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'     => 'Arial',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'dpi'             => 96,
                'chroot'          => public_path(),
            ]);

        $filename = 'Jurnal_PKL_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->stream($filename);
    })->name('journal.pdf');

    Route::get('/cetak/surat-pengantar/{dudika_id}', [PrintController::class, 'suratPengantar'])->name('cetak.surat-pengantar');
    Route::get('/cetak/laporan-lengkap/{id}', [PrintController::class, 'cetakLaporanLengkap'])->name('cetak.laporan-siswa');
    Route::get('/cetak/rekap-monitoring', [PrintController::class, 'cetakRekapMonitoring'])->name('cetak.rekap-monitoring');
}); // <-- Akhir dari Grup Middleware Auth

// ==========================================================
// RUTE PUBLIK (TIDAK PERLU LOGIN)
// ==========================================================

// Tampilkan Halaman Verifikasi (Pakai Hash URL)
Route::get('/verifikasi/laporan/{hash}', function ($hash) {
    try {
        $id = Crypt::decryptString($hash);
    } catch (DecryptException $e) {
        return abort(404, 'Link verifikasi tidak valid atau telah dimanipulasi.');
    }

    $placement = PklPlacement::with(['student.studentClass', 'dudika', 'teacher'])->findOrFail($id);
    $school = SchoolProfile::first();

    if (!Session::has('verified_laporan_' . $id)) {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        Session::put('captcha_answer_' . $id, $num1 + $num2);
        $captcha_question = "Berapa hasil dari $num1 + $num2 ?";
    } else {
        $captcha_question = "";
    }

    return view('verifikasi-laporan', compact('placement', 'school', 'captcha_question', 'hash'));
});

// Proses Submit Captcha
Route::post('/verifikasi/laporan/{hash}', function (Request $request, $hash) {
    try {
        $id = Crypt::decryptString($hash);
    } catch (DecryptException $e) {
        return abort(404);
    }

    $request->validate(['captcha' => 'required|numeric']);

    if ($request->captcha == Session::get('captcha_answer_' . $id)) {
        Session::put('verified_laporan_' . $id, true);
        return back()->with('success', 'Verifikasi berhasil!');
    }

    return back()->with('error', 'Jawaban matematika salah. Silakan coba lagi!');
});
