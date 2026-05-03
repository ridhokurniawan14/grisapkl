<?php

namespace App\Filament\Resources\Monitorings\Pages;

use App\Filament\Resources\Monitorings\MonitoringResource;
use App\Models\Monitoring;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMonitoring extends EditRecord
{
    protected static string $resource = MonitoringResource::class;

    // ==============================================================
    // VARIABEL CATATAN INGATAN (Agar memori masa lalu tidak hilang)
    // ==============================================================
    public $oldScheduleId;
    public $oldDate;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hapus kolom palsu agar tidak error SQL
        unset($data['teacher_id'], $data['dudika_id']);

        return $data;
    }

    // ==============================================================
    // CEGAT SEBELUM DISIMPAN: Catat masa lalunya!
    // ==============================================================
    protected function beforeSave(): void
    {
        $record = $this->getRecord();
        $this->oldScheduleId = $record->monitoring_schedule_id;
        $this->oldDate = $record->date;
    }

    // ==============================================================
    // SETELAH DISIMPAN: Update semua saudaranya pakai catatan ingatan!
    // ==============================================================
    protected function afterSave(): void
    {
        $record = $this->getRecord();

        // Cari semua "saudara" pakai CATATAN INGATAN yang kita buat di beforeSave
        $siblings = Monitoring::where('id', '!=', $record->id)
            ->where('monitoring_schedule_id', $this->oldScheduleId)
            ->where('date', $this->oldDate)
            ->whereHas('pklPlacement', fn($q) => $q->where('dudika_id', $record->pklPlacement->dudika_id))
            ->get();

        // Timpa data semua saudaranya agar 100% kompak dengan data yang baru diedit!
        foreach ($siblings as $sibling) {
            $sibling->update([
                'monitoring_schedule_id' => $record->monitoring_schedule_id,
                'activity' => $record->activity,
                'date' => $record->date,
                'time' => $record->time,
                'photo_path' => $record->photo_path,
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
