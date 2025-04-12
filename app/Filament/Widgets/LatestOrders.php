<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Forms;
use Filament\Forms\Components\Select;

use Illuminate\Support\Facades\Auth;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public static function boot(): void
    {
        static::$heading = __('order_fields.latest orders');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery()->where('status', 'pending'))
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.fullName')->icon('heroicon-o-user')->iconColor('primary')->label(__('user_fields.full_name')),
                Tables\Columns\TextColumn::make('medicines')
                    ->icon('heroicon-o-tag')
                    ->iconColor('primary')
                    ->label(__('medicine_fields.brand_name'))
                    ->formatStateUsing(function ($state) {
                        $items = is_string($state) ? explode(',', $state) : (is_array($state) ? $state : []);
                        return implode('<br>', $items);
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('quantities')
                    ->label(__('cart_fields.quantity'))
                    ->icon('heroicon-o-circle-stack')
                    ->iconColor('primary')
                    ->formatStateUsing(function ($state) {
                        $items = is_string($state) ? explode(',', $state) : (is_array($state) ? $state : []);
                        return implode('<br>', $items);
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('total_price')
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
                        default => 'danger',
                    })
                    ->label(__('order_fields.status')),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->icon('heroicon-o-calendar')->iconColor('primary')->label(__('order_fields.date')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(fn(Order $order) => [
                        Forms\Components\Select::make('status')
                            ->label(__('order_fields.status'))
                            ->options([
                                'pending' => __('order_fields.pending'),
                                'completed' => __('order_fields.completed'),
                                'canceled' => __('order_fields.canceled'),
                            ])
                            ->required(),
                    ])
                    ->visible(function () {
                        $employee = Auth::guard('employee')->user();
                        return !empty(array_intersect(['admin', 'sales manager'], $employee->role ?? []));
                    }),
            ]);
    }
}
