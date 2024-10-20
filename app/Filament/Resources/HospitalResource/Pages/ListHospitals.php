<?php

namespace App\Filament\Resources\HospitalResource\Pages;

use App\Filament\Resources\HospitalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHospitals extends ListRecords
{
    protected static string $resource = HospitalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function canViewAny(): bool
    {
        // Only admins can view the hospitals
        return auth()->user()->user_role === 'admin';
    }
}
