<?php

namespace App\Filament\Resources;
use App\Filament\Resources\MedicalRecordResource\Pages\ListMedicalRecords;
use App\Filament\Resources\MedicalRecordResource\Pages\ViewMedicalRecord;
use App\Models\MedicalRecord;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\MedicalRecordResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MedicalRecordResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Medical Records';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->user_role === 'patient';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                MedicalRecord::query()->where('patient_id', auth()->id())
            )
            ->columns([
                TextColumn::make('record_date')
                    ->date(),
                TextColumn::make('diagnosis')
                    ->limit(50),
                TextColumn::make('treatment')
                    ->limit(50),
                TextColumn::make('prescriptions.medication_name')
                    ->label('Prescribed Medication')
                    ->listWithLineBreaks()
                    ->limitList(3),
                TextColumn::make('doctor.first_name')
                    ->label('Doctor')
                    ->formatStateUsing(fn ($record) => $record->doctor->first_name . ' ' . $record->doctor->last_name),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }
    public static function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('doctor.first_name')
                    ->label('Doctor Name')
                    ->formatStateUsing(fn ($record) => $record->doctor->first_name . ' ' . $record->doctor->last_name),
                Infolists\Components\TextEntry::make('diagnosis')
                    ->markdown()
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make('prescriptions.medication_name')
                    ->label('Medicine'),
                Infolists\Components\TextEntry::make('prescriptions.dosage')
                    ->label('Dosage'),
                Infolists\Components\TextEntry::make('prescriptions.frequency')
                    ->label('Frequency'),
                Infolists\Components\TextEntry::make('prescriptions.duration_days')
                    ->label('Duration'),
                Infolists\Components\TextEntry::make('notes')
                    ->markdown()
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedicalRecords::route('/'),
        ];
    }
}
