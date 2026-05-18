<?php

namespace App\Filament\Resources\Dudikas\Schemas;

use Dotswan\MapPicker\Fields\Map;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Schemas\Schema;

class DudikaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs Dudika')
                    ->tabs([
                        Tab::make('Profil & Kontak')
                            ->icon('heroicon-m-building-office-2')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Instansi / DUDIKA')
                                    ->placeholder('Contoh: PT. Telkom Indonesia')
                                    ->autocomplete('off')
                                    ->required(),

                                // MANTRA SAKTI: Virtual Field Email (Tidak masuk ke tabel Dudika)
                                TextInput::make('email')
                                    ->label('Email DUDIKA (Untuk Login)')
                                    ->email()
                                    ->placeholder('Contoh: dudika@smkpgri1giri.sch.id')
                                    ->required()
                                    ->dehydrated(false) // Jangan simpan ke tabel dudikas!
                                    ->afterStateHydrated(function ($component, $record) {
                                        // Munculkan email saat mode Edit
                                        if ($record && $record->user) {
                                            $component->state($record->user->email);
                                        }
                                    })
                                    ->saveRelationshipsUsing(function ($record, $state) {
                                        if (blank($state)) return;

                                        // Jika DUDIKA sudah punya akun, update emailnya
                                        if ($record->user_id) {
                                            $user = \App\Models\User::find($record->user_id);
                                            if ($user) $user->update(['email' => $state]);
                                        }
                                        // Jika belum punya akun, buatkan baru
                                        else {
                                            $user = \App\Models\User::firstOrNew(['email' => $state]);
                                            if (!$user->exists) {
                                                $user->name = $record->supervisor_name ?? $record->name;
                                                $user->password = bcrypt('12345'); // Default 12345
                                                $user->save();

                                                // Handle perbedaan huruf besar/kecil di Spatie Role
                                                $roleName = \Spatie\Permission\Models\Role::where('name', 'dudika')->exists() ? 'dudika' : 'Dudika';
                                                $user->assignRole($roleName);
                                            }
                                            $record->updateQuietly(['user_id' => $user->id]);
                                        }
                                    }),

                                Textarea::make('address')
                                    ->label('Alamat Lengkap')
                                    ->placeholder('Masukkan alamat lengkap beserta kota...')
                                    ->autocomplete('off')
                                    ->columnSpanFull(),

                                Grid::make(2)->schema([
                                    TextInput::make('head_name')
                                        ->label('Nama Pimpinan / Direktur')
                                        ->placeholder('Contoh: Budi Santoso, S.T.')
                                        ->autocomplete('off'),
                                    TextInput::make('head_nip')
                                        ->label('NIP / NIK Pimpinan')
                                        ->placeholder('Opsional (jika ada)')
                                        ->autocomplete('off'),
                                ]),

                                \Filament\Forms\Components\Placeholder::make('pembatas')
                                    ->label('Data Pembimbing Lapangan (Instruktur)')
                                    ->content('Orang yang akan membimbing dan menilai siswa di lokasi.')
                                    ->columnSpanFull(),

                                Grid::make(3)->schema([
                                    TextInput::make('supervisor_name')
                                        ->label('Nama Pembimbing DUDIKA')
                                        ->placeholder('Contoh: Ahmad Yani')
                                        ->autocomplete('off'),
                                    TextInput::make('supervisor_nip')
                                        ->label('NIP / NIK Pembimbing')
                                        ->placeholder('Opsional (jika ada)')
                                        ->autocomplete('off'),
                                    TextInput::make('supervisor_phone')
                                        ->label('No. HP / WA Pembimbing')
                                        ->tel()
                                        ->placeholder('Contoh: 081234567890')
                                        ->autocomplete('off'),
                                ]),
                            ]),

                        Tab::make('Lokasi Absensi')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                Grid::make(5)->schema([
                                    TextInput::make('address_search')
                                        ->label('Cari Alamat / Tempat')
                                        ->placeholder('Ketik nama kota atau perusahaan, klik "Cari"...')
                                        ->dehydrated(false)
                                        ->columnSpan(4),

                                    Actions::make([
                                        Action::make('search_address')
                                            ->label('Cari')
                                            ->icon('heroicon-m-magnifying-glass')
                                            ->action(function (Get $get, Set $set, $livewire): void {
                                                $query = $get('address_search');
                                                if (!$query) return;

                                                $response = \Illuminate\Support\Facades\Http::withHeaders([
                                                    'User-Agent' => config('app.name') . ' location-search',
                                                ])->get('https://nominatim.openstreetmap.org/search', [
                                                    'q'      => $query,
                                                    'format' => 'json',
                                                    'limit'  => 1,
                                                ]);

                                                $results = $response->json();

                                                if (!empty($results)) {
                                                    $lat = (float) $results[0]['lat'];
                                                    $lng = (float) $results[0]['lon'];

                                                    $set('latitude', $lat);
                                                    $set('longitude', $lng);
                                                    $set('location', ['lat' => $lat, 'lng' => $lng]);
                                                    $livewire->dispatch('refreshMap');
                                                }
                                            }),
                                    ])->verticalAlignment(VerticalAlignment::End)->columnSpan(1),
                                ]),

                                Map::make('location')
                                    ->label('Peta Interaktif')
                                    ->columnSpanFull()
                                    ->defaultLocation(latitude: -8.219233, longitude: 114.369227)
                                    ->dehydrated(false)
                                    ->tilesUrl('https://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}')
                                    ->afterStateUpdated(function (Set $set, ?array $state): void {
                                        $set('latitude', $state['lat'] ?? null);
                                        $set('longitude', $state['lng'] ?? null);
                                    })
                                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                                        if ($record && $record->latitude && $record->longitude) {
                                            $set('location', ['lat' => $record->latitude, 'lng' => $record->longitude]);
                                        }
                                    })
                                    ->extraStyles(['min-height: 400px', 'border-radius: 12px'])
                                    ->liveLocation(true, true, 5000)
                                    ->showMarker(true)
                                    ->markerColor("#ef4444")
                                    ->showFullscreenControl(true)
                                    ->showZoomControl(true)
                                    ->draggable(true)
                                    ->clickable(true),

                                Grid::make(3)->schema([
                                    TextInput::make('latitude')
                                        ->label('Latitude (Bisa Diisi Manual)')
                                        ->numeric()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (Set $set, Get $get, $state, $livewire) {
                                            if (is_numeric($state) && is_numeric($get('longitude'))) {
                                                $set('location', ['lat' => (float)$state, 'lng' => (float)$get('longitude')]);
                                                $livewire->dispatch('refreshMap');
                                            }
                                        }),

                                    TextInput::make('longitude')
                                        ->label('Longitude (Bisa Diisi Manual)')
                                        ->numeric()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (Set $set, Get $get, $state, $livewire) {
                                            if (is_numeric($get('latitude')) && is_numeric($state)) {
                                                $set('location', ['lat' => (float)$get('latitude'), 'lng' => (float)$state]);
                                                $livewire->dispatch('refreshMap');
                                            }
                                        }),

                                    TextInput::make('radius')
                                        ->label('Batas Radius (Meter)')
                                        ->default(50)
                                        ->numeric()
                                        ->helperText('Jarak maksimal absen (Default: 50m).'),
                                ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
