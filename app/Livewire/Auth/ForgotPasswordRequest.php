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
use Illuminate\Support\Facades\Cache;

#[Layout('components.layouts.guest')]
#[Title('Lupa Kata Sandi - Grisa PKL')]
class ForgotPasswordRequest extends Component
{
    public string $email = '';
    public string $phone = '';
    public string $otp = '';
    public string $password = '';
    public string $password_confirmation = '';

    // State flow: 'request' | 'otp' | 'reset' | 'success'
    public string $step = 'request';

    public function requestOtp(): void
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

        // Generate 6 Digit OTP
        $otpCode = (string) rand(100000, 999999);

        // Simpan OTP di Cache selama 5 Menit
        Cache::put('otp_reset_' . $user->id, $otpCode, now()->addMinutes(5));

        // Kirim OTP via Fontee
        $sent = $this->sendOtpViaFontee($normalizedStored, $user->name, $otpCode);

        if (!$sent) {
            Log::error('Fontee API failed to send OTP for user: ' . $user->id);
            $this->addError('phone', 'Gagal mengirim OTP ke WhatsApp. Coba lagi nanti.');
            return;
        }

        $this->step = 'otp';
    }

    public function verifyOtp(): void
    {
        $this->validate([
            'otp' => 'required|numeric|digits:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits'   => 'Kode OTP harus 6 angka.',
        ]);

        $user = User::where('email', $this->email)->first();
        $cachedOtp = Cache::get('otp_reset_' . $user->id);

        if (!$cachedOtp || $cachedOtp !== $this->otp) {
            $this->addError('otp', 'Kode OTP salah atau sudah kedaluwarsa.');
            return;
        }

        $this->step = 'reset';
    }

    public function resetPassword(): void
    {
        $this->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::where('email', $this->email)->first();

        // Update Password
        $user->update(['password' => Hash::make($this->password)]);

        // Hapus OTP dari cache
        Cache::forget('otp_reset_' . $user->id);

        $this->step = 'success';
    }

    public function goBack(): void
    {
        if ($this->step === 'otp') {
            $this->step = 'request';
            $this->otp = '';
        } elseif ($this->step === 'reset') {
            $this->step = 'otp';
        } else {
            $this->redirect(route('login'));
        }
    }

    public function backToLogin(): void
    {
        $this->redirect(route('login'));
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) return '62' . substr($phone, 1);
        if (str_starts_with($phone, '+')) return substr($phone, 1);
        return $phone;
    }

    private function sendOtpViaFontee(string $phone, string $name, string $otpCode): bool
    {
        try {
            $apiKey  = config('services.fontee.api_key');
            $sender  = config('services.fontee.sender');

            $message = "Halo *{$name}*!\n\n"
                . "Kode OTP untuk mengatur ulang kata sandi Grisa PKL Anda adalah:\n\n"
                . "👉 *{$otpCode}* 👈\n\n"
                . "_Kode ini hanya berlaku selama 5 menit. Jangan bagikan kode ini kepada siapa pun._";

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
            Log::error('Fontee Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password-request');
    }
}
