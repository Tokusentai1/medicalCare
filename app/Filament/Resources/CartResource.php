<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CartResource\Pages;
use App\Filament\Resources\CartResource\RelationManagers;
use App\Models\Cart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $activeNavigationIcon = 'heroicon-s-shopping-cart';

    public static function getNavigationGroup(): ?string
    {
        return __('user_fields.orders');
    }

    public static function getNavigationLabel(): string
    {
        return __('cart_fields.carts');
    }

    public static function getPluralLabel(): string
    {
        return __('cart_fields.carts');
    }

    public static function getModelLabel(): string
    {
        return __('cart_fields.cart');
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
                Tables\Columns\TextColumn::make('total_price')->money('SYP')->label(__('cart_fields.total price')),
                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->label(__('cart_fields.active'))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarts::route('/'),
        ];
    }
}
