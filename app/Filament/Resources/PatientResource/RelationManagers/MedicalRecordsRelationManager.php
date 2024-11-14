<?php
use App\Models\MedicalRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicalRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'medicalRecords';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('record_date')
                    ->date(),
                Tables\Columns\TextColumn::make('diagnosis')
                    ->limit(50),
                Tables\Columns\TextColumn::make('treatment')
                    ->limit(50),
                Tables\Columns\TextColumn::make('prescriptions.medication_name')
                    ->label('Prescribed Medication')
                    ->listWithLineBreaks()
                    ->limitList(3),
                Tables\Columns\TextColumn::make('doctor.first_name')
                    ->label('Doctor')
                    ->formatStateUsing(fn ($record) => $record->doctor->first_name . ' ' . $record->doctor->last_name),            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('record_date')
                    ->required(),
                Forms\Components\Textarea::make('diagnosis')
                    ->required(),
                Forms\Components\Textarea::make('treatment')
                    ->required(),
                Forms\Components\Repeater::make('prescriptions')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('medication_name')
                            ->required(),
                        Forms\Components\TextInput::make('dosage')
                            ->required(),
                        Forms\Components\TextInput::make('frequency')
                            ->required(),
                        Forms\Components\TextInput::make('duration_days')
                            ->required(),
                        Forms\Components\Textarea::make('instructions')
                            ->required(),
                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, MedicalRecord $record) {
                        $data['patient_id'] = $record->patient_id;
                        $data['prescription_date'] = $record->record_date;
                        $data['medical_record_id'] = $record->id;
                        $data['doctor_id'] = Auth::id();
                        $data['hospital_id'] = Auth::user()->hospital_id;
                        return $data;
                    }),
            ]);
    }

    public function canCreate(): bool
    {
        return Auth::user()->user_role === 'doctor';
    }

    public function canEdit(Model $record): bool
    {
        return Auth::id() === $record->doctor_id;
    }

    public function canDelete(Model $record): bool
    {
        return Auth::id() === $record->doctor_id;
    }
}
