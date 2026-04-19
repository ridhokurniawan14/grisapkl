<?php

namespace App\Filament\Resources\SchoolProfiles\Pages;

use App\Filament\Resources\SchoolProfiles\SchoolProfileResource;
use App\Models\SchoolProfile;
use Filament\Resources\Pages\ListRecords;

class ListSchoolProfiles extends ListRecords
{
    protected static string $resource = SchoolProfileResource::class;

    // KITA BAJAK FUNGSI MOUNT SAAT HALAMAN DIBUKA
    public function mount(): void
    {
        // Cari apakah sudah ada data sekolah di database
        $profile = SchoolProfile::first();

        if ($profile) {
            // Kalau ADA, langsung lempar ke halaman Edit
            redirect()->to(SchoolProfileResource::getUrl('edit', ['record' => $profile->id]));
        } else {
            // Kalau KOSONG, langsung lempar ke halaman Create (Isi baru)
            redirect()->to(SchoolProfileResource::getUrl('create'));
        }
    }
}
