<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use App\Models\User;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;

class VerifyPatientId extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = PatientResource::class;

    protected static string $view = 'filament.resources.patient-resource.pages.verify-patient-id';

    public ?array $data = [];

    public $record;

    public function mount($record): void
    {
        $this->record = User::find($record);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('national_id')
                    ->label('Patient National ID')
                    ->required()
                    ->placeholder('Enter patient National ID to verify access'),
            ])
            ->statePath('data');
    }
    public function verify()
    {
        $data = $this->form->getState();

        if ($this->record->national_identification === $data['national_id']) {
            session(['verified_patient_' . $this->record->id => true]);
            return redirect()->route('filament.admin.resources.patients.edit', ['record' => $this->record->id]);
        }

        $this->addError('national_id', 'Invalid National ID');
    }
}
