<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use DiscoveryDesign\FilamentGaze\Forms\Components\GazeBanner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-currency-dollar';

    public static function getNavigationGroup(): ?string
    {
        return __('user_fields.orders');
    }

    public static function getNavigationLabel(): string
    {
        return __('order_fields.orders');
    }

    public static function getPluralLabel(): string
    {
        return __('order_fields.orders');
    }

    public static function getModelLabel(): string
    {
        return __('order_fields.order');
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                GazeBanner::make()
                    ->lock()
                    ->canTakeControl(fn() => in_array('admin', Auth::guard('employee')->user()->role))
                    ->pollTimer(5)
                    ->hideOnCreate()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => __('order_fields.pending'),
                        'completed' => __('order_fields.completed'),
                        'canceled' => __('order_fields.canceled'),
                    ])
                    ->required()
                    ->label(__('order_fields.status')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => __('order_fields.pending'),
                        'completed' => __('order_fields.completed'),
                        'canceled' => __('order_fields.canceled'),
                    ])
                    ->label(__('order_fields.status'))
                    ->attribute('status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ActivityLogTimelineTableAction::make(__('employee_fields.activity_log'))
                    ->visible(
                        fn() => in_array('admin', Auth::guard('employee')->user()->role),
                    ),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
