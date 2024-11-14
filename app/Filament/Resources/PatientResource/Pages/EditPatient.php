<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Resources\Pages\EditRecord;

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;
    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if (auth()->user()->user_role === 'doctor') {
            if (!session('verified_patient_' . $this->record->id)) {
                redirect()->route('filament.admin.resources.patients.verify-id', ['record' => $this->record]);
            }
        }
    }
}
