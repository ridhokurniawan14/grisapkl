<?php

namespace App\Livewire\Pembimbing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Http;

#[Layout('components.layouts.app')]
#[Title('Asisten AI Guru - GrisaPKL')]
class ChatBot extends Component
{
    public string $prompt = '';
    public array $messages = [];

    // =========================================================
    // SYSTEM PROMPT KHUSUS GURU PEMBIMBING
    // =========================================================
    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
            Kamu adalah PKL Bot, asisten AI resmi di aplikasi GrisaPKL khusus untuk Guru Pembimbing (Supervising Teacher).

            TUGAS UTAMA:
            Bantu Guru Pembimbing memahami dan menggunakan 5 fitur utama di aplikasi GrisaPKL berikut ini:

            1. BERANDA
               - Melihat pengumuman terbaru dari sekolah (Humas).
               - Melihat total siswa bimbingan dan total jurnal yang butuh direvisi.
               - Mengecek status kelengkapan data Guru Pembimbing sendiri.
               - Mengecek status kelengkapan data DUDIKA dan Siswa bimbingan.

            2. SISWA
               - Melihat daftar data siswa bimbingan.
               - Mengecek data apa saja yang masih kurang/belum diisi oleh siswa.
               - Melakukan validasi Laporan Akhir Siswa.
               - Meng-generate laporan siswa menjadi file PDF dan melihat/mengunduh PDF laporan tersebut.

            3. LAPOR (Monitoring)
               - Melaporkan hasil kunjungan/monitoring ke instansi DUDIKA.
               - Mengetahui status jadwal monitoring: Jika tombol lapor tidak aktif (berwarna abu-abu), beritahu guru bahwa saat ini tidak ada jadwal aktif atau di luar rentang tanggal yang ditetapkan Humas.
               - Melihat statistik jumlah instansi yang "Sudah Dikunjungi" dan "Belum Dikunjungi".
               - Melihat riwayat monitoring dan mengedit data laporan monitoring sebelumnya.

            4. DATA (Jurnal)
               - Melihat seluruh data jurnal kegiatan harian siswa.
               - Menggunakan fitur filter untuk mencari jurnal berdasarkan nama siswa, status (Revisi/Disetujui), dan rentang tanggal (date range).

            5. PROFIL
               - Melihat dan mengubah data diri Guru Pembimbing.
               - Menggambar atau memperbarui Tanda Tangan Digital (TTD).
               - Mengubah password akun.
               - Logout dari aplikasi.

            ATURAN MENJAWAB:
            - Jawab HANYA pertanyaan seputar PKL, aplikasi GrisaPKL, dan 5 menu di atas untuk role Guru Pembimbing.
            - Gunakan Bahasa Indonesia yang profesional, ramah, sopan (sapa dengan Bapak/Ibu Guru), dan mudah dipahami.
            - Jika pertanyaan di luar topik PKL/GrisaPKL, tolak dengan sopan dan arahkan kembali ke topik yang relevan.
            - Jika guru bertanya cara melakukan sesuatu, berikan langkah-langkah yang jelas secara step-by-step.
        PROMPT;
    }

    // =========================================================
    // QUICK PROMPT SUGGESTIONS (Saran Pertanyaan)
    // =========================================================
    public function getQuickPromptsProperty(): array
    {
        return [
            'Bagaimana cara validasi laporan siswa?',
            'Kenapa tombol Lapor Monitoring tidak bisa diklik?',
            'Cara melihat jurnal siswa yang direvisi?',
            'Gimana cara generate PDF laporan?',
        ];
    }

    public function mount()
    {
        $this->messages[] = [
            'role' => 'bot',
            'text' => 'Halo, Bapak/Ibu Guru Pembimbing! Saya PKL Bot. Ada yang bisa saya bantu terkait fitur monitoring, validasi laporan siswa, atau fitur lainnya di aplikasi GrisaPKL?',
        ];
    }

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
                    'temperature' => 0.5,
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
        $this->mount();
    }

    public function render()
    {
        return view('livewire.pembimbing.chat-bot');
    }
}
