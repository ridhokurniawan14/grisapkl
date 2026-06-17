<?php

use App\Http\Controllers\PrintController;
use App\Livewire\Auth\ForgotPasswordRequest;
use App\Livewire\Auth\LoginUniversal;
use App\Livewire\Dudika\Beranda as DudikaBeranda;
use App\Livewire\Dudika\Profil as DudikaProfil;
use App\Livewire\Dudika\ProfilEdit as DudikaProfilEdit;
use App\Livewire\Dudika\UbahPassword as DudikaUbahPassword;
use App\Livewire\Dudika\Jurnal;
use App\Livewire\Dudika\Nilai;
use App\Livewire\Pembimbing\Beranda;
use App\Livewire\Pembimbing\Profil;
use App\Livewire\Pembimbing\ProfilEdit;
use App\Livewire\Pembimbing\Siswa;
use App\Livewire\Pembimbing\UbahPassword;
use App\Livewire\Pembimbing\Data;
use App\Livewire\Pembimbing\Lapor;
use App\Livewire\Pembimbing\LaporEdit;
use App\Http\Middleware\CheckUserActive;
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
        return redirect('/pembimbing/beranda');
    }
    return redirect('/login');
});

Route::view('/offline', 'offline')->name('offline');


Route::middleware('guest')->group(function () {
    Route::get('/login', LoginUniversal::class)->name('login');
    Route::get('/lupa-kata-sandi', ForgotPasswordRequest::class)->name('auth.forgot-password');
});

// ==========================================================
// RUTE PWA SISWA
// ==========================================================
Route::middleware(['auth', CheckUserActive::class, 'role:siswa'])->group(function () {
    Route::get('/siswa/absen', \App\Livewire\Student\Absensi::class)->name('siswa.absen');
    Route::get('/siswa/profil', \App\Livewire\Student\Profil::class)->name('siswa.profil');
    Route::get('/siswa/profil/edit', \App\Livewire\Student\ProfilEdit::class)->name('siswa.profil.edit');
    Route::get('/siswa/dudika', \App\Livewire\Student\Dudika::class)->name('siswa.dudika');
    Route::get('/siswa/jurnal', \App\Livewire\Student\Jurnal::class)->name('siswa.jurnal');
    Route::get('/siswa/jurnal/{id}/edit', \App\Livewire\Student\JurnalEdit::class)->name('siswa.jurnal.edit');
    Route::get('/siswa/beranda', \App\Livewire\Student\Beranda::class)->name('siswa.beranda');
    Route::get('/siswa/bot', \App\Livewire\Student\ChatBot::class)->name('siswa.bot');
    Route::get('/siswa/profil/password', \App\Livewire\Student\UbahPassword::class)->name('siswa.profil.password');
    Route::get('/siswa/laporan/download', function () {
        $student = \App\Models\Student::where('user_id', auth()->id())->first();
        abort_if(!$student, 403, 'Data siswa tidak ditemukan.');

        $pklPlacement = \App\Models\PklPlacement::where('student_id', $student->id)->firstOrFail();
        abort_if(empty($pklPlacement->file_laporan_path), 404, 'File laporan belum tersedia.');

        $path = storage_path('app/public/' . $pklPlacement->file_laporan_path);
        $fileName = 'Laporan_PKL_' . str_replace(' ', '_', $student->name) . '.pdf';

        // PERBAIKAN: Paksa header Content-Disposition menjadi attachment
        return response()->download($path, $fileName, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    })->middleware(['auth'])->name('siswa.laporan.download');
});

// ==========================================================
// RUTE PWA PEMBIMBING (GURU)
// ==========================================================
Route::middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/pembimbing/beranda', Beranda::class)->name('pembimbing.beranda');
    Route::get('/pembimbing/siswa', Siswa::class)->name('pembimbing.siswa');
    Route::get('/pembimbing/profil', Profil::class)->name('pembimbing.profil');
    Route::get('/pembimbing/profil/edit', ProfilEdit::class)->name('pembimbing.profil.edit');
    Route::get('/pembimbing/profil/password', UbahPassword::class)->name('pembimbing.profil.password');
    Route::get('/pembimbing/data', Data::class)->name('pembimbing.data');
    Route::get('/pembimbing/lapor', Lapor::class)->name('pembimbing.lapor');
    Route::get('/pembimbing/lapor/edit', LaporEdit::class)->name('pembimbing.lapor.edit');
    Route::get('/pembimbing/bot', \App\Livewire\Pembimbing\ChatBot::class)->name('pembimbing.bot');
});

// ==========================================================
// RUTE PWA DUDIKA
// ==========================================================
Route::middleware(['auth', 'role:dudika'])->group(function () {
    Route::get('/dudika/beranda', DudikaBeranda::class)->name('dudika.beranda');
    Route::get('/dudika/profil', DudikaProfil::class)->name('dudika.profil');
    Route::get('/dudika/profil/edit', DudikaProfilEdit::class)->name('dudika.profil.edit');
    Route::get('/dudika/profil/password', DudikaUbahPassword::class)->name('dudika.profil.password');
    Route::get('/dudika/jurnal', Jurnal::class)->name('dudika.jurnal');
    Route::get('/dudika/nilai', Nilai::class)->name('dudika.nilai');
    Route::get('/dudika/nilai/{placement_id}/edit', \App\Livewire\Dudika\NilaiForm::class)->name('dudika.nilai.form');
    Route::get('/dudika/bot', \App\Livewire\Dudika\ChatBot::class)->name('dudika.bot');
});
// ==========================================================
// RUTE RAHASIA (WAJIB LOGIN UNTUK MENGAKSES)
// ==========================================================
Route::middleware(['auth'])->group(function () {

    // ===== TAMBAHKAN RUTE FCM INI =====
    Route::post('/save-fcm-token', function (\Illuminate\Http\Request $request) {
        $user = $request->user();

        if ($user) {
            $user->fcm_token = $request->token;
            $user->save();

            return response()->json(['success' => true, 'message' => 'Token saved']);
        }

        return response()->json(['success' => false, 'message' => 'User not found'], 401);
    });

    // Route untuk Cetak PDF DUDIKA
    Route::get('/dudika/print', [PrintController::class, 'dudikaPrint'])->name('dudika.print');

    Route::get('/journal/download', [PrintController::class, 'journalDownload'])->name('journal.download');

    Route::get('/journal/pdf', [PrintController::class, 'journalPdf'])->name('journal.pdf');

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
