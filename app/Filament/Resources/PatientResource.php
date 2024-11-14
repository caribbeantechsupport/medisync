<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers\PrescriptionsRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use MedicalRecordsRelationManager;

class PatientResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $label = 'Patient';

    protected static ?string $slug = 'patients';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->where('user_role', 'patient');

        $user = auth()->user();

        if ($user->user_role === 'doctor') {
            return $query->where(function ($query) use ($user) {
                $query->whereHas('appointments', function ($query) use ($user) {
                    $query->where('doctor_id', $user->id);
                })
                ->orWhereHas('medicalRecords', function ($query) use ($user) {
                    $query->where('doctor_id', $user->id);
                })
                ->orWhereHas('prescriptions', function ($query) use ($user) {
                    $query->where('doctor_id', $user->id);
                });
            });
        }

        if ($user->user_role === 'hospital_admin') {
            return $query->where('hospital_id', $user->hospital_id);
        }

        return $query;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_name')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('first_name')->required(),
                Forms\Components\TextInput::make('middle_name'),
                Forms\Components\TextInput::make('last_name')->required(),
                Forms\Components\TextInput::make('national_identification')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('contact_number')->required(),
                Forms\Components\TextInput::make('address')->required(),
                Forms\Components\TextInput::make('country')->required(),
                Forms\Components\TextInput::make('kin_name'),
                Forms\Components\TextInput::make('kin_contact_number'),
                Forms\Components\TextInput::make('kin_address'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->minLength(8)
                    ->nullable()
                    ->dehydrateStateUsing(function ($state) {
                        return $state ? bcrypt($state) : null;
                    })
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->visible(fn(): bool => auth()->user()->user_role === 'admin')
                    ->label('Password'),
                Forms\Components\TextInput::make('passwordConfirmation')
                    ->password()
                    ->label('Password Confirmation')
                    ->minLength(8)
                    ->dehydrated(false)
                    ->required(fn (string $context): bool => $context === 'create')
                    ->same('password')
                    ->visible(fn(): bool => auth()->user()->user_role === 'admin'),
                Forms\Components\Hidden::make('user_role')
                    ->default('doctor')
                    ->dehydrateStateUsing(fn ($state) => 'patient'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('first_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('last_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact_number')->searchable()->sortable(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MedicalRecordsRelationManager::class,
            PrescriptionsRelationManager::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
            'verify-id' => Pages\VerifyPatientId::route('/{record}/verify'),
            'view' => Pages\ViewPatient::route('/{record}'),
        ];
    }
}
