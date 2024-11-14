<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\DoctorResource\Pages;

class DoctorResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $label = 'Doctor';

    protected static ?string $slug = 'doctors';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('user_role', 'doctor');
    }

    public static function canCreate(): bool
    {
        // Only non-doctors (e.g., admins) can create doctors
        return auth()->user()->user_role !== 'doctor';
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
                    ->dehydrateStateUsing(fn ($state) => 'doctor'),
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
                Tables\Columns\TextColumn::make('doctor_specialization')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('hospital.name')->searchable()->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        // Only allow non-doctors (e.g., admins) to edit doctor records
        return auth()->user()->user_role !== 'doctor';
    }
}
