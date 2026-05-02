<?php

namespace App\Filament\Resources\Monitorings\Pages;

use App\Filament\Resources\Monitorings\MonitoringResource;
use App\Models\PklPlacement;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMonitoring extends CreateRecord
{
    protected static string $resource = MonitoringResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $dudikaId = $data['dudika_id'];
        $teacherId = $data['teacher_id']; // Tangkap ID Gurunya

        // Buang dari array agar tidak error karena field ini tidak ada di tabel monitorings
        unset($data['dudika_id']);
        unset($data['teacher_id']);

        // ==============================================================
        // MANTRA SAKTI 3: Ambil siswa di DUDIKA terpilih & GURU terpilih
        // ==============================================================
        $placements = \App\Models\PklPlacement::where('dudika_id', $dudikaId)
            ->where('teacher_id', $teacherId) // Filter super ketat!
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->get();

        $firstRecord = null;

        foreach ($placements as $placement) {
            $record = static::getModel()::create(array_merge($data, [
                'pkl_placement_id' => $placement->id,
            ]));

            if (! $firstRecord) {
                $firstRecord = $record;
            }
        }

        // Notif sukses dengan info jumlah siswa yang tercatat
        \Filament\Notifications\Notification::make()
            ->title('Monitoring berhasil dicatat!')
            ->body("{$placements->count()} siswa bimbingan guru terkait di DUDIKA ini otomatis tercatat.")
            ->success()
            ->send();

        // Fallback safety net
        return $firstRecord ?? static::getModel()::create($data);
    }

    protected function getSavedNotification(): ?Notification
    {
        return null; // Notif custom sudah kita handle di atas
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
