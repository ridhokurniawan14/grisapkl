<?php

namespace App\Livewire\Pembimbing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Teacher;
use App\Models\PklPlacement;
use App\Models\Journal;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Beranda Guru - GrisaPKL')]
class Beranda extends Component
{
    public function render()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        // 1. Sapaan Berdasarkan Jam Lokal (WIB)
        $hour = Carbon::now('Asia/Jakarta')->format('H');
        if ($hour >= 4 && $hour < 11) {
            $greeting = 'Selamat Pagi,';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang,';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore,';
        } else {
            $greeting = 'Selamat Malam,';
        }

        // ==========================================
        // FUNGSI HELPER: FORMAT NO HP KE +62
        // ==========================================
        $formatPhone = function ($phone) {
            // Bersihkan semua karakter selain angka dan plus
            $phone = preg_replace('/[^0-9+]/', '', (string)$phone);

            if (str_starts_with($phone, '0')) {
                return '+62' . substr($phone, 1);
            } elseif (str_starts_with($phone, '62')) {
                return '+' . $phone;
            } elseif (!str_starts_with($phone, '+') && !empty($phone)) {
                return '+62' . $phone; // Jaga-jaga jika depannya 812 langsung
            }
            return $phone; // Kalau sudah +62 dibiarkan
        };

        $totalSiswa = 0;
        $jurnalMenunggu = 0;
        $isTeacherComplete = false;
        $dudikaList = collect();
        $studentList = collect();
        $dudikaPendingCount = 0;
        $studentPendingCount = 0;

        if ($teacher) {
            // A. CEK KELENGKAPAN PROFIL GURU
            $isTeacherComplete = !empty($teacher->subject) && !empty($teacher->signature_path);

            // B. AMBIL DATA PENEMPATAN SISWA
            $placements = PklPlacement::with(['student.user', 'dudika'])
                ->where('teacher_id', $teacher->id)
                ->where('status', 'Aktif')
                ->get();

            $totalSiswa = $placements->count();

            // C. HITUNG JURNAL BUTUH VALIDASI
            $jurnalMenunggu = Journal::whereHas('pklPlacement', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->where('status', 'Aktif');
            })->where('is_valid', false)->count();

            // D. CEK KELENGKAPAN DUDIKA & BUAT PESAN WA
            $dudikaList = $placements->pluck('dudika')->filter()->unique('id')->map(function ($d) use ($formatPhone) {
                $isComplete = !empty($d->head_name);

                // 1. Format Nomor WA
                $rawPhone = $d->supervisor_phone ?? '';
                $formattedPhone = $formatPhone($rawPhone);

                // 2. Ambil 5 Digit Terakhir untuk Password & Username
                $onlyNumbers = preg_replace('/[^0-9]/', '', $rawPhone);
                $last5 = strlen($onlyNumbers) >= 5 ? substr($onlyNumbers, -5) : '12345'; // Fallback jika no HP ga logis

                // 3. Logika Sapaan Gender
                // (Sesuaikan "supervisor_gender" dengan nama field di DB-mu jika ada)
                $gender = $d->supervisor_gender ?? null;
                $sapaan = 'Bapak/Ibu';
                if (in_array(strtoupper($gender), ['L', 'LAKI-LAKI', 'PRIA'])) {
                    $sapaan = 'Bapak';
                } elseif (in_array(strtoupper($gender), ['P', 'W', 'PEREMPUAN', 'WANITA'])) {
                    $sapaan = 'Ibu';
                }

                // 4. Susun Pesan WA DUDIKA
                $waText = "Halo {$sapaan} Pimpinan dari {$d->name},\n\nMohon kesediaannya untuk melengkapi data profil instansi di aplikasi GrisaPKL (grisapkl.smkpgri1giri.sch.id), khususnya data Nama Pimpinan Instansi.\n\nUntuk mempermudah proses login, silakan gunakan detail berikut:\nUsername: {$last5}@smkpgri1giri.sch.id\nPassword: {$last5}\n\nTerima kasih atas kerjasamanya!";

                return [
                    'name' => $d->name,
                    'is_complete' => $isComplete,
                    'phone' => $formattedPhone,
                    'wa_message' => urlencode($waText)
                ];
            });
            $dudikaPendingCount = $dudikaList->where('is_complete', false)->count();

            // E. CEK KELENGKAPAN SISWA & BUAT PESAN WA
            $studentList = $placements->filter(function ($placement) {
                // Pastikan placement punya relasi student agar tidak error
                return $placement->student !== null;
            })->map(function ($placement) use ($formatPhone) {
                $student = $placement->student;
                $missing = [];

                // Data Pribadi & Ortu
                if (empty($student->nisn)) $missing[] = 'NISN';
                if (empty($student->nis)) $missing[] = 'NIS';
                if (empty($student->birth_place)) $missing[] = 'Tempat Lahir';
                if (empty($student->birth_date)) $missing[] = 'Tanggal Lahir';
                if (empty($student->religion)) $missing[] = 'Agama';
                if (empty($student->gender)) $missing[] = 'Jenis Kelamin';
                if (empty($student->phone)) $missing[] = 'No. HP';
                if (empty($student->address)) $missing[] = 'Alamat Pribadi';
                if (empty($student->father_name)) $missing[] = 'Nama Ayah';
                if (empty($student->father_job)) $missing[] = 'Pekerjaan Ayah';
                if (empty($student->mother_name)) $missing[] = 'Nama Ibu';
                if (empty($student->mother_job)) $missing[] = 'Pekerjaan Ibu';
                if (empty($student->parent_phone)) $missing[] = 'No. HP Ortu';
                if (empty($student->parent_address) && empty($student->address)) $missing[] = 'Alamat Ortu';

                // Nah, sekarang $placement bisa terbaca dengan aman!
                if (empty($placement->pkl_field)) $missing[] = 'Bidang Keahlian / Pekerjaan Siswa';

                $isComplete = count($missing) === 0;

                // Format Nomor WA Siswa
                $rawPhone = $student->phone ?? ($student->user->phone ?? '');
                $formattedPhone = $formatPhone($rawPhone);

                $studentName = $student->user->name ?? 'Siswa';
                $missingStr = implode(', ', $missing);

                // Susun Pesan WA Siswa
                $waText = "Halo {$studentName}, mohon segera lengkapi data profil kamu di aplikasi GrisaPKL ya.\n\nData yang saat ini masih kosong: *{$missingStr}*.\n\nSegera dilengkapi agar tidak menghambat penulisan laporan jurnal. Terima kasih!";

                return [
                    'name' => $studentName,
                    'is_complete' => $isComplete,
                    'phone' => $formattedPhone,
                    'wa_message' => urlencode($waText)
                ];
            })->values(); // Reset array keys

            $studentPendingCount = $studentList->where('is_complete', false)->count();
        }

        // 3. Tarik Data Pengumuman
        $announcements = Announcement::where('is_active', 1)
            ->whereIn('target_audience', ['Umum', 'Guru'])
            ->latest()
            ->get();

        return view('livewire.pembimbing.beranda', [
            'user' => $user,
            'teacher' => $teacher,
            'greeting' => $greeting,
            'totalSiswa' => $totalSiswa,
            'jurnalMenunggu' => $jurnalMenunggu,
            'announcements' => $announcements,
            'isTeacherComplete' => $isTeacherComplete,
            'dudikaList' => $dudikaList,
            'dudikaPendingCount' => $dudikaPendingCount,
            'studentList' => $studentList,
            'studentPendingCount' => $studentPendingCount,
        ]);
    }
}
