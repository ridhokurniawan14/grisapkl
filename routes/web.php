<?php

use Illuminate\Support\Facades\Route;
use App\Models\Dudika;
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
