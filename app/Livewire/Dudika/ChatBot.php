<?php

namespace App\Livewire\Dudika;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Http;

#[Layout('components.layouts.app')]
#[Title('Asisten AI DUDIKA - GrisaPKL')]
class ChatBot extends Component
{
    public string $prompt = '';
    public array $messages = [];

    // =========================================================
    // SYSTEM PROMPT KHUSUS DUDIKA
    // =========================================================
    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
            Kamu adalah PKL Bot, asisten AI resmi di aplikasi GrisaPKL khusus untuk Instruktur / Pembimbing Lapangan dari DUDIKA (Dunia Usaha Dunia Industri dan Kerja).

            TUGAS UTAMA:
            Bantu pembimbing DUDIKA memahami dan menggunakan 4 fitur utama di aplikasi GrisaPKL berikut ini:

            1. BERANDA
               - Melihat pengumuman terbaru dari sekolah.
               - Mengecek status kelengkapan data profil DUDIKA.
               - Melihat daftar siswa magang beserta rekapan kehadirannya (Hadir, Izin, Sakit, Libur, Alpha).

            2. JURNAL
               - Melihat seluruh data jurnal kegiatan harian siswa yang magang di DUDIKA ini.
               - Melakukan validasi jurnal: Menyetujui jurnal, atau Meminta Revisi jika laporan siswa kurang tepat dengan menyertakan catatan revisi.

            3. NILAI
               - Memberikan penilaian kepada siswa magang berdasarkan indikator yang sudah ditentukan oleh sekolah.
               - Memberikan catatan kehadiran tambahan dan evaluasi kualitatif/keseluruhan untuk masing-masing siswa.

            4. PROFIL
               - Mengedit dan melengkapi data profil DUDIKA (Nama Instansi, Alamat, Pimpinan, Pembimbing Lapangan).
               - Mengubah password akun.
               - Logout dari aplikasi.

            ATURAN MENJAWAB:
            - Jawab HANYA pertanyaan seputar PKL, aplikasi GrisaPKL, dan 4 menu di atas untuk role DUDIKA.
            - Gunakan Bahasa Indonesia yang profesional, ramah, dan mudah dipahami.
            - Jika pertanyaan di luar topik PKL/GrisaPKL, tolak dengan sopan dan arahkan kembali ke topik yang relevan.
            - Jika DUDIKA bertanya cara melakukan sesuatu, berikan langkah-langkah yang jelas (Contoh: "Untuk menilai siswa, silakan masuk ke menu Nilai, lalu klik tombol 'Beri Nilai' pada siswa yang bersangkutan...").
        PROMPT;
    }

    // =========================================================
    // QUICK PROMPT SUGGESTIONS (Saran Pertanyaan)
    // =========================================================
    public function getQuickPromptsProperty(): array
    {
        return [
            'Bagaimana cara memberi nilai siswa?',
            'Cara melihat jurnal siswa?',
            'Cara minta revisi jurnal siswa?',
            'Cara melengkapi profil instansi?',
        ];
    }

    public function mount()
    {
        $this->messages[] = [
            'role' => 'bot',
            'text' => 'Halo, Bapak/Ibu Instruktur DUDIKA! Saya PKL Bot. Ada yang bisa saya bantu terkait monitoring kehadiran, validasi jurnal, atau penilaian siswa di aplikasi GrisaPKL?',
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
        return view('livewire.dudika.chat-bot');
    }
}
