<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Cart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CartsRelationManager extends RelationManager
{
    protected static string $relationship = 'carts';

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
                Tables\Columns\TextColumn::make('medicines.brand_name')
                    ->listWithLineBreaks()
                    ->icon('heroicon-o-tag')
                    ->iconColor('primary')
                    ->label(__('medicine_fields.brand_name')),
                Tables\Columns\TextColumn::make('medicines')
                    ->label(__('cart_fields.quantity'))
                    ->getStateUsing(function (Cart $record) {
                        return $record->medicines->map(function ($medicine) {
                            return "{$medicine->pivot->quantity}";
                        })->toArray();
                    })
                    ->listWithLineBreaks()
                    ->icon('heroicon-o-circle-stack')
                    ->iconColor('primary'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label(__('cart_fields.total price'))
                    ->sortable()
                    ->money('SYP'),
                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->label(__('cart_fields.active'))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
