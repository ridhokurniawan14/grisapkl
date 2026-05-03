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

    protected function handleRecordCreation(array $data): Model
    {
        // Pakai null coalescing (??) agar terhindar dari Undefined array key
        $dudikaId = $data['dudika_id'] ?? null;
        $teacherId = $data['teacher_id'] ?? null;

        // Buang dari array agar tidak error karena field ini tidak ada di tabel monitorings
        unset($data['dudika_id'], $data['teacher_id']);

        // ==============================================================
        // MANTRA SAKTI 3: Ambil siswa di DUDIKA terpilih & GURU terpilih
        // ==============================================================
        $placements = PklPlacement::where('dudika_id', $dudikaId)
            ->where('teacher_id', $teacherId)
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

        // SAFETY NET SAKTI: Batalkan simpan jika ternyata tidak ada siswa
        if (! $firstRecord) {
            Notification::make()
                ->title('Gagal Disimpan!')
                ->body('Tidak ada siswa aktif dari guru ini di DUDIKA tersebut.')
                ->danger()
                ->send();

            $this->halt(); // Batalkan proses Filament
        }

        // Notif sukses dengan info jumlah siswa yang tercatat
        Notification::make()
            ->title('Monitoring berhasil dicatat!')
            ->body("{$placements->count()} siswa bimbingan guru terkait di DUDIKA ini otomatis tercatat.")
            ->success()
            ->send();

        return $firstRecord;
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
