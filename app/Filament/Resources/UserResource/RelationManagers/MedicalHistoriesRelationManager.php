<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MedicalHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'medicalHistories';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_id')
            ->columns([
                Tables\Columns\TextColumn::make('allergies')->icon('heroicon-o-eye-dropper')->iconColor('primary')->label(__('medical_history_fields.allergies')),
                Tables\Columns\TextColumn::make('previous_surgeries')->icon('heroicon-o-eye-dropper')->iconColor('primary')->label(__('medical_history_fields.previous surgeries')),
                Tables\Columns\TextColumn::make('past_medical_condition')->icon('heroicon-o-eye-dropper')->iconColor('primary')->label(__('medical_history_fields.past medical condition')),
                Tables\Columns\TextColumn::make('medications')->icon('heroicon-o-eye-dropper')->iconColor('primary')->label(__('medical_history_fields.medications')),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
