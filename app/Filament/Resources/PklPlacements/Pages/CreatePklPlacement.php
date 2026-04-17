<?php

namespace App\Filament\Resources\PklPlacements\Pages;

use App\Filament\Resources\PklPlacements\PklPlacementResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePklPlacement extends CreateRecord
{
    protected static string $resource = PklPlacementResource::class;

    // Fungsi sakti untuk mencegat proses simpan
    protected function handleRecordCreation(array $data): Model
    {
        // 1. Ambil array ID Siswa
        $studentIds = $data['student_ids'];

        // 2. Hapus dari array utama agar tidak error masuk database
        unset($data['student_ids']);

        $firstRecord = null;

        // 3. Looping: Bikin data penempatan sebanyak siswa yang dipilih
        foreach ($studentIds as $studentId) {
            $data['student_id'] = $studentId; // Masukkan 1 ID Siswa

            // Simpan ke database
            $record = static::getModel()::create($data);

            // Filament butuh return 1 model agar tidak error setelah submit
            if (!$firstRecord) {
                $firstRecord = $record;
            }
        }

        return $firstRecord;
    }

    // Redirect kembali ke halaman List (Tabel) setelah berhasil bikin banyak
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
