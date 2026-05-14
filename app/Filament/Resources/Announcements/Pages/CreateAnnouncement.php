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

        if (!$pengumuman->is_active) {
            return;
        }

        // GEMBOK ANTI-DOUBLE: Cegah pengiriman dobel dalam waktu 10 detik!
        if (!Cache::add('fcm_sent_announcement_' . $pengumuman->id, true, 10)) {
            return;
        }

        try {
            $target = $pengumuman->target_audience;
            $query = User::whereNotNull('fcm_token');

            // Filter Role berdasarkan Pilihan Humas
            if ($target === 'Siswa') {
                $query->role('siswa');
            } elseif ($target === 'Guru') {
                $query->role(['guru', 'pembimbing']);
            } elseif ($target === 'Dudika') {
                $query->role('dudika');
            }

            // FILTER ANTI-DOUBLE TOKEN (Mencegah token sama terkirim 2x)
            $tokens = array_filter(array_unique($query->pluck('fcm_token')->toArray()));

            if (empty($tokens)) {
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
        } catch (\Exception $e) {
            Log::error('Gagal kirim Notif FCM: ' . $e->getMessage());
        }
    }
}
