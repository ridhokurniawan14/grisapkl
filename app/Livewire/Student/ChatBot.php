<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Http;

#[Layout('components.layouts.app')]
#[Title('Asisten AI - GrisaPKL')]
class ChatBot extends Component
{
    public string $prompt = '';
    public array $messages = [];

    // Bisa diisi dari Auth::user()->role atau hardcode per Livewire class
    // 'siswa' | 'guru' | 'pembimbing_dudika'
    public string $role = 'siswa';

    // =========================================================
    // SYSTEM PROMPTS — tambah role baru di sini nanti
    // =========================================================
    protected function getSystemPrompt(): string
    {
        $prompts = [

            'siswa' => <<<PROMPT
                Kamu adalah PKL Bot, asisten AI resmi di aplikasi GrisaPKL untuk siswa SMK yang sedang menjalani Praktik Kerja Lapangan (PKL).

                TUGAS UTAMA:
                Bantu siswa memahami dan menggunakan fitur-fitur aplikasi GrisaPKL berikut ini:

                1. BERANDA
                   - Melihat pengumuman terbaru dari sekolah
                   - Mengecek status kelengkapan data PKL
                   - Melihat rekap absensi 1 bulan terakhir

                2. JURNAL
                   - Melihat seluruh daftar jurnal kegiatan PKL
                   - Filter jurnal berdasarkan bulan, status (disetujui/revisi), dan pencarian kata kunci
                   - Edit data jurnal dan memperbaikinya

                3. ABSENSI
                   - Alur absen harian yang BENAR (wajib urut):
                     Langkah 1 → Buka menu Absensi
                     Langkah 2 → Tap tombol "Absen"
                     Langkah 3 → Ambil foto selfie (kamera depan, wajah terlihat jelas)
                     Langkah 4 → Upload foto kegiatan PKL hari ini
                     Langkah 5 → Isi deskripsi kegiatan yang dilakukan hari ini
                     Langkah 6 → Kirim/Submit
                   - Jenis kehadiran yang tersedia: Hadir, Ijin, Sakit, Libur
                   - Melihat rekap kehadiran selama PKL berlangsung
                   - Melihat history absen 1 minggu terakhir
                   - Aturan radius absensi (harus berada di lokasi DUDIKA)

                4. DUDIKA (Dunia Usaha Dunia Industri dan Kerja)
                   - Melihat data tempat PKL (DUDIKA)
                   - Informasi pimpinan dan pembimbing dari DUDIKA beserta nomor HP
                   - Informasi guru pembimbing dari sekolah beserta nomor HP

                5. PROFIL
                   - Melihat dan mengedit data pribadi, akademik, dan data orang tua
                   - Mencetak laporan PKL lengkap (tombol aktif setelah diverifikasi guru pembimbing)
                   - Melihat versi aplikasi
                   - Logout dari aplikasi

                ATURAN MENJAWAB:
                - Jawab HANYA pertanyaan seputar PKL, aplikasi GrisaPKL, dan 5 menu di atas
                - Gunakan Bahasa Indonesia yang ramah, singkat, dan mudah dipahami siswa SMK
                - Jika pertanyaan di luar topik PKL/GrisaPKL, tolak dengan sopan dan arahkan kembali ke topik yang relevan
                - Jangan menjawab pertanyaan tentang topik umum, pelajaran sekolah, hiburan, atau hal lain yang tidak berkaitan dengan PKL
                - Jika siswa bertanya tentang nomor HP pembimbing, arahkan ke menu DUDIKA
                - Berikan langkah-langkah yang jelas jika siswa kesulitan menggunakan fitur tertentu
            PROMPT,

            // =========================================================
            // GURU — aktifkan nanti ketika fitur guru siap
            // =========================================================
            'guru' => <<<PROMPT
                Kamu adalah PKL Bot, asisten AI resmi di aplikasi GrisaPKL untuk Guru Pembimbing PKL.

                TUGAS UTAMA:
                Bantu guru pembimbing memahami dan menggunakan fitur-fitur yang tersedia untuk guru di aplikasi GrisaPKL.

                ATURAN MENJAWAB:
                - Jawab HANYA pertanyaan seputar PKL dan fitur aplikasi GrisaPKL untuk guru
                - Gunakan Bahasa Indonesia yang profesional dan ringkas
                - Jika pertanyaan di luar topik, tolak dengan sopan dan arahkan kembali ke topik PKL
            PROMPT,

            // =========================================================
            // PEMBIMBING DUDIKA — aktifkan nanti
            // =========================================================
            'pembimbing_dudika' => <<<PROMPT
                Kamu adalah PKL Bot, asisten AI resmi di aplikasi GrisaPKL untuk Pembimbing dari DUDIKA.

                TUGAS UTAMA:
                Bantu pembimbing DUDIKA memahami dan menggunakan fitur monitoring siswa PKL di aplikasi GrisaPKL.

                ATURAN MENJAWAB:
                - Jawab HANYA pertanyaan seputar PKL dan fitur aplikasi GrisaPKL untuk pembimbing DUDIKA
                - Gunakan Bahasa Indonesia yang profesional dan ringkas
                - Jika pertanyaan di luar topik, tolak dengan sopan
            PROMPT,
        ];

        return trim($prompts[$this->role] ?? $prompts['siswa']);
    }

    // =========================================================
    // QUICK PROMPT SUGGESTIONS — per role
    // =========================================================
    public function getQuickPromptsProperty(): array
    {
        return match ($this->role) {
            'siswa' => [
                'Bagaimana cara mengisi jurnal harian?',
                'Cara absen di aplikasi GrisaPKL?',
                'Jurnal saya kena revisi, harus ngapain?',
                'Cara lihat nomor HP pembimbing DUDIKA?',
                'Kapan tombol cetak laporan aktif?',
            ],
            'guru' => [
                'Cara verifikasi jurnal siswa?',
                'Cara melihat rekap absensi siswa?',
            ],
            'pembimbing_dudika' => [
                'Cara memantau kehadiran siswa?',
                'Cara memberikan catatan untuk siswa?',
            ],
            default => [],
        };
    }

    // =========================================================
    // LIFECYCLE
    // =========================================================
    public function mount(string $role = 'siswa')
    {
        // Ambil dari Auth jika perlu:
        // $this->role = Auth::user()->role ?? 'siswa';
        $this->role = $role;

        $greetings = [
            'siswa'              => 'Halo! Saya PKL Bot, siap membantu kamu seputar penggunaan aplikasi GrisaPKL. Mau tanya tentang jurnal, absensi, atau fitur lainnya? 😊',
            'guru'               => 'Halo, Bapak/Ibu Guru! Ada yang bisa saya bantu terkait fitur GrisaPKL untuk pembimbingan PKL?',
            'pembimbing_dudika'  => 'Halo! Saya PKL Bot. Ada yang bisa saya bantu terkait monitoring siswa PKL di aplikasi GrisaPKL?',
        ];

        $this->messages[] = [
            'role' => 'bot',
            'text' => $greetings[$this->role] ?? $greetings['siswa'],
        ];
    }

    // =========================================================
    // ACTIONS
    // =========================================================
    public function setPrompt(string $text): void
    {
        $this->prompt = $text;
        $this->sendMessage();
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->prompt))) return;

        $userMessage = trim($this->prompt);
        $this->messages[] = ['role' => 'user', 'text' => $userMessage];
        $this->prompt = '';

        // Build conversation history (skip pesan sambutan index 0)
        $chatHistory = [];
        foreach (array_slice($this->messages, 1) as $msg) {
            $chatHistory[] = [
                'role'    => $msg['role'] === 'user' ? 'user' : 'assistant',
                'content' => $msg['text'],
            ];
        }

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . config('services.groq.key'),
                ])
                ->timeout(30)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'llama-3.1-8b-instant',
                    'messages'    => array_merge(
                        [['role' => 'system', 'content' => $this->getSystemPrompt()]],
                        $chatHistory
                    ),
                    'max_tokens'  => 512,
                    'temperature' => 0.5, // Lebih rendah = lebih konsisten & on-topic
                ]);

            if ($response->successful()) {
                $botReply = $response->json('choices.0.message.content')
                    ?? 'Maaf, saya tidak bisa memproses jawaban saat ini. Silakan coba lagi.';
                $this->messages[] = ['role' => 'bot', 'text' => $botReply];
            } else {
                $errorMsg = $response->json('error.message') ?? 'HTTP Status: ' . $response->status();
                $this->messages[] = ['role' => 'bot', 'text' => 'Gagal terhubung ke AI. Alasan: ' . $errorMsg];
            }
        } catch (\Exception $e) {
            $this->messages[] = ['role' => 'bot', 'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function clearChat(): void
    {
        $this->messages = [];
        $this->mount($this->role);
    }

    public function render()
    {
        return view('livewire.student.chat-bot');
    }
}
