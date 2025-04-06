<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalHistoryResource\Pages;
use App\Filament\Resources\MedicalHistoryResource\RelationManagers;
use App\Models\MedicalHistory;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Mail\MedicationAlertMail;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;

class MedicalHistoryResource extends Resource
{
    protected static ?string $model = MedicalHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    protected static ?string $activeNavigationIcon = 'heroicon-s-pencil';

    public static function getNavigationGroup(): ?string
    {
        return __('user_fields.patients');
    }

    public static function getNavigationLabel(): string
    {
        return __('medical_history_fields.medical histories');
    }

    public static function getPluralLabel(): string
    {
        return __('medical_history_fields.medical histories');
    }

    public static function getModelLabel(): string
    {
        return __('medical_history_fields.medical history');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.fullName')->icon('heroicon-o-user')->iconColor('primary')->label(__('user_fields.full_name')),
                Tables\Columns\TextColumn::make('user.birth_date')
                    ->getStateUsing(function ($record) {
                        if ($record->user && $record->user->birth_date) {
                            return Carbon::parse($record->user->birth_date)->age;
                        }
                        return 'N/A';
                    })
                    ->icon('heroicon-o-cake')
                    ->iconColor('primary')
                    ->label(__('user_fields.age')),
                Tables\Columns\TextColumn::make('allergies')->listWithLineBreaks()->icon('heroicon-o-eye-dropper')->iconColor('primary')->label(__('medical_history_fields.allergies')),
                Tables\Columns\TextColumn::make('previous_surgeries')->listWithLineBreaks()->icon('heroicon-o-eye-dropper')->iconColor('primary')->label(__('medical_history_fields.previous surgeries')),
                Tables\Columns\TextColumn::make('past_medical_condition')->listWithLineBreaks()->icon('heroicon-o-eye-dropper')->iconColor('primary')->label(__('medical_history_fields.past medical condition')),
                Tables\Columns\TextColumn::make('medications')->listWithLineBreaks()->icon('heroicon-o-eye-dropper')->iconColor('primary')->label(__('medical_history_fields.medications')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('sendEmail')
                    ->label('Send Email')
                    ->icon('heroicon-o-envelope')
                    ->form([
                        Forms\Components\Textarea::make('message')
                            ->label('Email Message')
                            ->required(),
                    ])
                    ->modalHeading('Send Email to User')
                    ->action(function ($record, array $data) {
                        $record->load('user');

                        $userEmail = $record->user->email;
                        $message = $data['message'];

                        Mail::to($userEmail)->send(new MedicationAlertMail($message));

                        Notification::make()
                            ->title('Email sent to user')
                            ->success()
                            ->send();
                    })
                    ->color('primary'),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListMedicalHistories::route('/'),
        ];
    }
}
