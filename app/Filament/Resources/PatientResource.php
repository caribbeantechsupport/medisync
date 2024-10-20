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
        return parent::getEloquentQuery()->where('user_role', 'patient');
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
                    ->nullable() // Allow nullable values during updates
                    ->dehydrateStateUsing(function ($state) {
                        return $state ? bcrypt($state) : null; // Only hash if a new password is provided
                    })
                    ->dehydrated(fn ($state) => filled($state)) // Only save if a password is provided
                    ->required(fn (string $context): bool => $context === 'create') // Only required during creation
                    ->label('Password'),
                Forms\Components\TextInput::make('passwordConfirmation')
                    ->password()
                    ->label('Password Confirmation')
                    ->minLength(8)
                    ->dehydrated(false) // Do not save this field
                    ->required(fn (string $context): bool => $context === 'create') // Required only during creation
                    ->same('password'), // Ensure it matches the password field
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
        ];
    }
}
