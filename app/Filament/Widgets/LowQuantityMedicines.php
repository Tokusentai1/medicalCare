<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\MedicineResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowQuantityMedicines extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public static function boot(): void
    {
        static::$heading = __('medicine_fields.low_quantity_medicines');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(MedicineResource::getEloquentQuery()->where('quantity', '<=', 10))
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('brand_name')->label(__('medicine_fields.brand_name'))->searchable()->icon('heroicon-o-tag')->iconColor('primary'),
                Tables\Columns\TextColumn::make('composition')->label(__('medicine_fields.composition'))->icon('heroicon-o-pencil')->iconColor('primary'),
                Tables\Columns\TextColumn::make('dosage')->label(__('medicine_fields.dosage'))->icon('heroicon-o-eye-dropper')->iconColor('primary'),
                Tables\Columns\TextColumn::make('dosage_form')->label(__('medicine_fields.dosage_form'))->icon('heroicon-o-scale')->iconColor('primary'),
                Tables\Columns\ImageColumn::make('image')->disk('images')->label(__('medicine_fields.image')),
                Tables\Columns\TextColumn::make('price')->label(__('medicine_fields.price'))->money('SYP'),
                Tables\Columns\TextColumn::make('quantity')->label(__('medicine_fields.quantity'))->icon('heroicon-o-inbox-stack')->iconColor('primary')->numeric(),
                Tables\Columns\TextColumn::make('manufacture_date')->label(__('medicine_fields.manufacture_date'))->icon('heroicon-o-calendar')->iconColor('primary')->date(),
                Tables\Columns\TextColumn::make('expire_date')->label(__('medicine_fields.expire_date'))->icon('heroicon-o-calendar')->iconColor('primary')->date(),
                Tables\Columns\TextColumn::make('manufacturer')->label(__('medicine_fields.manufacturer'))->icon('heroicon-o-building-office')->iconColor('primary'),
                Tables\Columns\TextColumn::make('category.name')->label(__('category_fields.name'))->searchable()->toggleable(),
                Tables\Columns\IconColumn::make('rocheta')
                    ->label(__('medicine_fields.rocheta'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ]);
    }
}
