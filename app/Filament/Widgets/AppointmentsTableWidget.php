<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AppointmentsTableWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $query = Appointment::query();

        if ($user->user_role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->user_role === 'patient') {
            $query->where('patient_id', $user->id);
        }

        return $table
            ->query($query->latest())
            ->columns([
                Tables\Columns\TextColumn::make('patient.first_name')
                    ->label('Patient')
                    ->formatStateUsing(fn ($record) => "{$record->patient->first_name} {$record->patient->last_name}")
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.first_name')
                    ->label('Doctor')
                    ->formatStateUsing(fn ($record) => "{$record->doctor->first_name} {$record->doctor->last_name}")
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('hospital.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('appointment_datetime')->dateTime()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('appointment_type')->searchable()->sortable(),
            ]);
    }
}
