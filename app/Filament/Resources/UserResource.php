<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

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
                Forms\Components\Select::make('user_role')
                    ->options([
                        'patient' => 'Patient',
                        'doctor' => 'Doctor',
                        'admin' => 'Admin',
                        'hospital_admin' => 'Hospital Admin',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('doctor_specialization')
                    ->visible(fn (callable $get) => $get('user_role') === 'doctor'),
                Forms\Components\Select::make('hospital_id')
                    ->relationship('hospital', 'name')
                    ->visible(fn (callable $get) => in_array($get('user_role'), ['doctor', 'hospital_admin'])),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(8)
                    ->same('passwordConfirmation'),
                Forms\Components\TextInput::make('passwordConfirmation')
                    ->password()
                    ->label('Password Confirmation')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(8)
                    ->dehydrated(false),
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
                Tables\Columns\TextColumn::make('user_role')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('hospital.name')->searchable()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_role')
                    ->options([
                        'patient' => 'Patient',
                        'doctor' => 'Doctor',
                        'admin' => 'Admin',
                        'hospital_admin' => 'Hospital Admin',
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
