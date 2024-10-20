<?php

namespace App\Filament\Resources\PatientResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PrescriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'prescriptions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('doctor_id')
                    ->default(Auth::id()),
                Forms\Components\Hidden::make('hospital_id')
                    ->default(Auth::user()->hospital_id),
                Forms\Components\DatePicker::make('prescription_date')
                    ->required(),
                Forms\Components\Textarea::make('medication_name')
                    ->required(),
                Forms\Components\Textarea::make('dosage')
                    ->required(),
                Forms\Components\Textarea::make('frequency')
                    ->required(),
                Forms\Components\Textarea::make('duration_days')
                    ->required(),
                Forms\Components\Textarea::make('instructions')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('prescription_date')
                    ->date(),
                Tables\Columns\TextColumn::make('medication_name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
