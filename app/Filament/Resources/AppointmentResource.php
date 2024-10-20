<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\Pages\CreateAppointment;
use App\Filament\Resources\AppointmentResource\Pages\EditAppointment;
use App\Filament\Resources\AppointmentResource\Pages\ListAppointments;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table as TablesTable;
use Filament\Tables\Table;
use Symfony\Component\Console\Helper\Table as HelperTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        // Start with the base query
        $query = parent::getEloquentQuery();

        // Check the user's role and filter accordingly
        if ($user->user_role === 'doctor') {
            // Doctors only see their own appointments
            return $query->where('doctor_id', $user->id);
        }

        if ($user->user_role === 'patient') {
            // Patients only see their own appointments
            return $query->where('patient_id', $user->id);
        }

        if ($user->user_role === 'hospital_admin') {
            // Hospital admins only see appointments in their hospital
            return $query->where('hospital_id', $user->hospital_id);
        }

        // For admins or other roles, return all appointments
        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('patient_id')
                    ->relationship('patient', 'user_name')
                    ->required(),
                Forms\Components\Select::make('doctor_id')
                    ->relationship('doctor', 'user_name')
                    ->required(),
                Forms\Components\Select::make('hospital_id')
                    ->relationship('hospital', 'name')
                    ->required(),
                Forms\Components\DateTimePicker::make('appointment_datetime')->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('reason_for_visit'),
                Forms\Components\Textarea::make('doctor_notes'),
                Forms\Components\TextInput::make('duration_minutes')->integer()->required(),
                Forms\Components\Select::make('appointment_type')
                    ->options([
                        'in-person' => 'In-person',
                        'video' => 'Video',
                        'phone' => 'Phone',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('patient_feedback'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.user_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('doctor.user_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('hospital.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('appointment_datetime')->dateTime()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('appointment_type')->searchable()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('appointment_type')
                    ->options([
                        'in-person' => 'In-person',
                        'video' => 'Video',
                        'phone' => 'Phone',
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
