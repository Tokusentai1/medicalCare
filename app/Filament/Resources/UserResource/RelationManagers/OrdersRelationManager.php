<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => __('order_fields.pending'),
                        'completed' => __('order_fields.completed'),
                        'canceled' => __('order_fields.canceled'),
                    ])->label(__('order_fields.status'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_id')
            ->columns([
                Tables\Columns\TextColumn::make('cart.medicines.brand_name')
                    ->listWithLineBreaks()
                    ->icon('heroicon-o-tag')
                    ->iconColor('primary')
                    ->label(__('medicine_fields.brand_name')),
                Tables\Columns\TextColumn::make('cart')
                    ->label(__('cart_fields.quantity'))
                    ->getStateUsing(function (Order $order) {
                        return $order->cart->medicines->map(function ($medicine) {
                            return "{$medicine->pivot->quantity}";
                        })->toArray();
                    })
                    ->listWithLineBreaks()
                    ->icon('heroicon-o-circle-stack')
                    ->iconColor('primary'),
                Tables\Columns\TextColumn::make('cart.total_price')
                    ->money('SYP')->label(__('cart_fields.total price')),
                Tables\Columns\TextColumn::make('status')
                    ->icon(fn($state) => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'completed' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-x-circle',
                    })
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        default => 'primary',
                    })
                    ->label(__('order_fields.status')),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->icon('heroicon-o-calendar')->iconColor('primary')->label(__('order_fields.date')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => __('order_fields.Pending'),
                        'completed' => __('order_fields.Completed'),
                        'canceled' => __('order_fields.Canceled'),
                    ])
                    ->label(__('order_fields.status'))
                    ->attribute('status'),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }
}
