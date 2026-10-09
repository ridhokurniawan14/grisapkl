<?php

namespace App\Livewire\Pembimbing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app')]
#[Title('Asisten AI Guru - GrisaPKL')]
class ChatBot extends Component
{
    public string $prompt = '';
    public array $messages = [];

    // Jumlah pesan terakhir yang dikirim ke AI (hemat token & biaya)
    private const MAX_HISTORY = 10;

    // =========================================================
    // KONFIGURASI PROVIDER (xai / groq) -> atur AI_PROVIDER di .env
    // =========================================================
    protected function getProviderConfig(): array
    {
        $provider = config('services.ai_provider', 'xai');

        return match ($provider) {
            'groq' => $this->groqConfig(),
            default => [
                'name'  => 'xai',
                'url'   => 'https://api.x.ai/v1/chat/completions',
                'key'   => config('services.xai.key'),
                'model' => config('services.xai.model', 'grok-4.7'),
                'extra' => [
                    'max_tokens' => 1024,
                ],
            ],
        };
    }

    protected function groqConfig(): array
    {
        $model = config('services.groq.model') ?: 'llama-3.3-70b-versatile';

        // Parameter reasoning HANYA untuk model gpt-oss (llama akan menolaknya)
        $extra = str_starts_with($model, 'openai/gpt-oss')
            ? ['max_completion_tokens' => 2048, 'reasoning_effort' => 'low', 'include_reasoning' => false]
            : ['max_tokens' => 1024];

        return [
            'name'  => 'groq',
            'url'   => 'https://api.groq.com/openai/v1/chat/completions',
            'key'   => config('services.groq.key'),
            'model' => $model,
            'extra' => $extra,
        ];
    }

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

        $provider = $this->getProviderConfig();

        if (empty($provider['key'])) {
            Log::error('ChatBot: API key kosong', ['provider' => $provider['name']]);
            $this->messages[] = [
                'role' => 'bot',
                'text' => 'Layanan AI belum dikonfigurasi. Mohon hubungi admin.',
            ];
            return;
        }

        // Bangun riwayat chat (lewati sapaan awal, ambil N pesan terakhir)
        $chatHistory = [];
        foreach (array_slice($this->messages, 1) as $msg) {
            $chatHistory[] = [
                'role'    => $msg['role'] === 'user' ? 'user' : 'assistant',
                'content' => $msg['text'],
            ];
        }
        $chatHistory = array_slice($chatHistory, -self::MAX_HISTORY);

        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $provider['key'],
            ])
                ->timeout(60)
                ->retry(2, 1500, function ($exception) {
                    // Ulangi hanya untuk gangguan sementara
                    if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
                        return true;
                    }
                    return $exception instanceof \Illuminate\Http\Client\RequestException
                        && in_array($exception->response->status(), [429, 500, 502, 503], true);
                }, throw: false)
                ->post($provider['url'], array_merge([
                    'model'       => $provider['model'],
                    'messages'    => array_merge(
                        [['role' => 'system', 'content' => $this->getSystemPrompt()]],
                        $chatHistory
                    ),
                    'temperature' => 0.5,
                ], $provider['extra'] ?? []));

            if ($response->successful()) {
                $botReply = $response->json('choices.0.message.content')
                    ?: 'Maaf, saya tidak bisa memproses jawaban saat ini. Silakan coba lagi.';
                $this->messages[] = ['role' => 'bot', 'text' => $botReply];
                return;
            }

            // Detail teknis disimpan di log, bukan ditampilkan ke guru
            Log::warning('ChatBot: respons error dari AI', [
                'provider' => $provider['name'],
                'model'    => $provider['model'],
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);

            $text = $this->friendlyError($response->status());

            // Sementara APP_DEBUG=true: tampilkan detail agar mudah dilacak
            if (config('app.debug')) {
                $text .= "\n\n[DEBUG] provider={$provider['name']} | model={$provider['model']} | status={$response->status()} | " . $response->body();
            }

            $this->messages[] = ['role' => 'bot', 'text' => $text];
        } catch (\Throwable $e) {
            Log::error('ChatBot: exception', ['message' => $e->getMessage()]);
            $this->messages[] = [
                'role' => 'bot',
                'text' => 'Maaf, koneksi ke layanan AI sedang bermasalah. Silakan coba lagi beberapa saat lagi.',
            ];
        }
    }

    protected function friendlyError(int $status): string
    {
        return match (true) {
            in_array($status, [401, 403], true) => 'Layanan AI belum bisa diakses (autentikasi bermasalah). Mohon hubungi admin.',
            $status === 402                     => 'Layanan AI sedang tidak tersedia karena kuota/kredit habis. Mohon hubungi admin.',
            $status === 404                     => 'Model AI tidak ditemukan. Mohon hubungi admin.',
            $status === 429                     => 'Terlalu banyak permintaan. Silakan coba lagi sebentar lagi, Bapak/Ibu.',
            $status >= 500                      => 'Layanan AI sedang sibuk. Silakan coba lagi beberapa saat lagi, Bapak/Ibu.',
            default                             => 'Maaf, terjadi gangguan saat menghubungi AI. Silakan coba lagi.',
        };
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
