<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use DiscoveryDesign\FilamentGaze\Forms\Components\GazeBanner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $activeNavigationIcon = 'heroicon-s-squares-2x2';

    public static function getNavigationGroup(): ?string
    {
        return __('employee_fields.pharmacy');
    }

    public static function getNavigationLabel(): string
    {
        return __('category_fields.categories');
    }

    public static function getPluralLabel(): string
    {
        return __('category_fields.categories');
    }

    public static function getModelLabel(): string
    {
        return __('category_fields.category');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                GazeBanner::make()
                    ->lock()
                    ->canTakeControl(fn() => in_array('admin', Auth::guard('employee')->user()->role))
                    ->pollTimer(7)
                    ->hideOnCreate()
                    ->columnSpan(2),
                Forms\Components\TextInput::make('name')->required()->label(__('category_fields.name')),
                Forms\Components\FileUpload::make('picture')->required()->label(__('category_fields.picture'))->disk('categories'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('category_fields.name'))->searchable()->toggleable()->icon('heroicon-o-tag')->iconColor('primary'),
                Tables\Columns\ImageColumn::make('picture')->label(__('category_fields.picture'))->toggleable()->disk('categories'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                ActivityLogTimelineTableAction::make(__('employee_fields.activity_log'))
                    ->visible(
                        fn() => in_array('admin', Auth::guard('employee')->user()->role),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MedicinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
