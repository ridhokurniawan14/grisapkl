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
    public $prompt = '';
    public $messages = [];

    public function mount()
    {
        $this->messages[] = [
            'role' => 'bot',
            'text' => 'Halo! Ada yang bisa saya bantu terkait pengisian jurnal atau penggunaan aplikasi GrisaPKL hari ini?'
        ];
    }

    public function setPrompt($text)
    {
        $this->prompt = $text;
        $this->sendMessage();
    }

    public function sendMessage()
    {
        if (empty(trim($this->prompt))) return;

        $userMessage = $this->prompt;
        $this->messages[] = ['role' => 'user', 'text' => $userMessage];
        $this->prompt = '';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=AIzaSyASOiPsO001CCTCbP6pBazRfPQxC7hA6D0', [
                'contents' => [
                    ['parts' => [['text' => "Kamu adalah PKL Bot, asisten AI ramah untuk siswa SMK aplikasi GrisaPKL. Jawab singkat padat jelas. Pertanyaan siswa: " . $userMessage]]]
                ]
            ]);

            if ($response->successful()) {
                $botReply = $response->json('candidates.0.content.parts.0.text');
                $this->messages[] = ['role' => 'bot', 'text' => $botReply];
            } else {
                $this->messages[] = ['role' => 'bot', 'text' => 'Maaf, sistem AI sedang sibuk. Coba lagi nanti.'];
            }
        } catch (\Exception $e) {
            $this->messages[] = ['role' => 'bot', 'text' => 'Terjadi kesalahan koneksi jaringan.'];
        }
    }

    public function render()
    {
        return view('livewire.student.chat-bot');
    }
}
