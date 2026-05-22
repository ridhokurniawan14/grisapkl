<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function afterCreate(): void
    {
        set_time_limit(120);
        $pengumuman = $this->record;

        // TAMBAH INI
        Log::info('=== FCM DEBUG ===');
        Log::info('Pengumuman ID: ' . $pengumuman->id);
        Log::info('is_active: ' . ($pengumuman->is_active ? 'true' : 'false'));
        Log::info('target_audience: ' . $pengumuman->target_audience);

        if (!$pengumuman->is_active) {
            Log::info('SKIP: pengumuman tidak aktif');
            return;
        }

        try {
            $target = $pengumuman->target_audience;
            $query = User::whereNotNull('fcm_token');

            if ($target === 'Siswa') {
                $query->role('siswa');
            } elseif ($target === 'Guru') {
                $query->role(['guru']);
            } elseif ($target === 'Dudika') {
                $query->role('dudika');
            }

            $tokens = array_filter(array_unique($query->pluck('fcm_token')->toArray()));

            // TAMBAH INI
            Log::info('Jumlah token ditemukan: ' . count($tokens));
            Log::info('Tokens: ' . json_encode(array_values($tokens)));

            if (empty($tokens)) {
                Log::info('SKIP: tidak ada token');
                return;
            }

            // Atur Redirect Link berdasarkan Target Audience
            $redirectLink = url('/'); // Default
            if ($target === 'Siswa') {
                $redirectLink = route('siswa.beranda');
            } elseif ($target === 'Guru') {
                $redirectLink = route('pembimbing.beranda');
            } elseif ($target === 'Dudika') {
                $redirectLink = route('dudika.beranda');
            }

            $tokenChunks = array_chunk($tokens, 500);
            $messaging = app('firebase.messaging');

            $title = "Pengumuman " . ($target === 'Umum' ? 'Baru' : $target) . "!";
            $body = strip_tags($pengumuman->content);
            $body = \Illuminate\Support\Str::limit($body, 120);

            $notification = Notification::create($title, $body);

            // Sisipkan Link Redirect di notifikasi sistem (Web Push)
            $webPush = WebPushConfig::fromArray([
                'notification' => [
                    'click_action' => $redirectLink
                ],
                'fcm_options' => [
                    'link' => $redirectLink
                ]
            ]);

            foreach ($tokenChunks as $chunk) {
                $message = CloudMessage::new()
                    ->withNotification($notification)
                    ->withWebPushConfig($webPush);

                $messaging->sendMulticast($message, $chunk);
            }

            foreach ($tokenChunks as $chunk) {
                $message = CloudMessage::new()
                    ->withNotification($notification)
                    ->withWebPushConfig($webPush);

                // TAMBAH LOG SEBELUM DAN SESUDAH
                Log::info('Mengirim ke ' . count($chunk) . ' token...');

                $report = $messaging->sendMulticast($message, $chunk);

                Log::info('Berhasil: ' . $report->successes()->count());
                Log::info('Gagal: ' . $report->failures()->count());

                // Log detail kegagalan
                foreach ($report->failures()->getItems() as $failure) {
                    Log::error('Token gagal: ' . $failure->target()->value());
                    Log::error('Alasan: ' . $failure->error()->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal kirim Notif FCM: ' . $e->getMessage());
        }
    }
}
