<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\SchoolProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

#[Layout('components.layouts.guest')]
#[Title('Lupa Kata Sandi - Grisa PKL')]
class ForgotPasswordRequest extends Component
{
    public string $email = '';
    public string $phone = '';

    // State flow: 'form' | 'success'
    public string $step = 'form';

    public bool $loading = false;

    public function sendNewPassword(): void
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
            'phone' => 'required|min:9|max:15',
        ], [
            'email.required'  => 'Email wajib diisi.',
            'email.email'     => 'Format email tidak valid.',
            'email.exists'    => 'Email tidak terdaftar di sistem kami.',
            'phone.required'  => 'Nomor HP wajib diisi.',
            'phone.min'       => 'Nomor HP minimal 9 digit.',
        ]);

        $user = User::where('email', $this->email)->first();

        // Validasi nomor HP cocok dengan user
        $normalizedInput = $this->normalizePhone($this->phone);
        $normalizedStored = $this->normalizePhone($user->phone ?? '');

        if ($normalizedStored === '' || $normalizedInput !== $normalizedStored) {
            $this->addError('phone', 'Nomor HP tidak sesuai dengan akun ini.');
            return;
        }

        // Generate password baru 8 karakter (mixed)
        $newPassword = $this->generatePassword();

        // Simpan ke database
        $user->update(['password' => Hash::make($newPassword)]);

        // Kirim via Fontee
        $sent = $this->sendViaFontee($normalizedStored, $user->name, $newPassword);

        if (! $sent) {
            // Rollback atau tetap tampilkan success tapi log error
            Log::error('Fontee API failed for user: ' . $user->id);
        }

        $this->step = 'success';
    }

    private function normalizePhone(string $phone): string
    {
        // Bersihkan karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '+')) {
            return substr($phone, 1);
        }

        return $phone;
    }

    private function generatePassword(): string
    {
        // 8 karakter: huruf besar, kecil, angka — mudah dibaca
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
        return substr(str_shuffle($chars), 0, 8);
    }

    private function sendViaFontee(string $phone, string $name, string $password): bool
    {
        try {
            $apiKey  = config('services.fontee.api_key');
            $sender  = config('services.fontee.sender');  // nama pengirim / device ID

            $message = "Halo {$name}!\n\n"
                . "Kata sandi baru Grisa PKL Anda:\n"
                . "🔑 *{$password}*\n\n"
                . "Segera masuk dan ganti kata sandi Anda.\n"
                . "Jangan bagikan ke siapapun.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept'        => 'application/json',
            ])->post('https://api.fontee.id/v1/message/send', [
                'to'      => $phone,
                'message' => $message,
                'sender'  => $sender,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Fontee sendViaFontee Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function backToLogin(): void
    {
        $this->redirect(route('login'));
    }

    public function render()
    {
        $school  = SchoolProfile::first();
        $logoUrl = ($school && $school->logo_path)
            ? asset('storage/' . $school->logo_path)
            : null;

        return view('livewire.auth.forgot-password-request', compact('logoUrl'));
    }
}
