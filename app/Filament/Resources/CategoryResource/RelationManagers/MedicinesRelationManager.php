<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use DiscoveryDesign\FilamentGaze\Forms\Components\GazeBanner;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class MedicinesRelationManager extends RelationManager
{
    protected static string $relationship = 'medicines';

    public static function getModelLabel(): string
    {
        return __('medicine_fields.medicine');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                GazeBanner::make()
                    ->lock()
                    ->canTakeControl(fn() => in_array('admin', Auth::guard('employee')->user()->role))
                    ->pollTimer(7)
                    ->columnSpan(1),
                Wizard::make([
                    Wizard\Step::make(__('medicine_fields.step1'))->schema([
                        Forms\Components\TextInput::make('brand_name')->required()->label(__('medicine_fields.brand_name')),
                        Forms\Components\TextInput::make('composition')->required()->label(__('medicine_fields.composition')),
                        Forms\Components\TextInput::make('dosage')->required()->label(__('medicine_fields.dosage')),
                        Forms\Components\Select::make('dosage_form')
                            ->options([
                                'Swallow' => __('medicine_fields.Swallow'),
                                'drink' => __('medicine_fields.drink'),
                                'inject' => __('medicine_fields.inject'),
                            ])->required()->label(__('medicine_fields.dosage_form')),
                        Forms\Components\FileUpload::make('image')->required()->label(__('medicine_fields.image'))
                            ->disk('images'),
                    ]),
                    Wizard\Step::make(__('medicine_fields.step2'))->schema([
                        Forms\Components\TextInput::make('price')->required()->label(__('medicine_fields.price'))->numeric()->prefix('SYP'),
                        Forms\Components\TextInput::make('quantity')->required()->label(__('medicine_fields.quantity'))->numeric(),
                        Forms\Components\TextInput::make('manufacturer')->required()->label(__('medicine_fields.manufacturer')),
                    ]),
                    Wizard\Step::make(__('medicine_fields.step3'))->schema([
                        Forms\Components\DatePicker::make('manufacture_date')->required()->label(__('medicine_fields.manufacture_date')),
                        Forms\Components\DatePicker::make('expire_date')->required()->label(__('medicine_fields.expire_date')),
                        Forms\Components\Toggle::make('rocheta')->required()->label(__('medicine_fields.rocheta')),
                    ]),
                ])->columnSpanFull()->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                >
                    {{ __('medicine_fields.save') }}
                </x-filament::button>
            BLADE)))
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('brand_name')
            ->columns([
                Tables\Columns\TextColumn::make('brand_name')->label(__('medicine_fields.brand_name'))->searchable()->icon('heroicon-o-tag')->iconColor('primary')->toggleable(),
                Tables\Columns\TextColumn::make('composition')->label(__('medicine_fields.composition'))->icon('heroicon-o-pencil')->iconColor('primary')->toggleable(),
                Tables\Columns\TextColumn::make('dosage')->label(__('medicine_fields.dosage'))->icon('heroicon-o-eye-dropper')->iconColor('primary')->toggleable(),
                Tables\Columns\TextColumn::make('dosage_form')->label(__('medicine_fields.dosage_form'))->icon('heroicon-o-scale')->iconColor('primary')->toggleable(),
                Tables\Columns\ImageColumn::make('image')->label(__('medicine_fields.image'))->disk('images')->toggleable(),
                Tables\Columns\TextColumn::make('price')->label(__('medicine_fields.price'))->money('SYP')->toggleable(),
                Tables\Columns\TextColumn::make('quantity')->label(__('medicine_fields.quantity'))->icon('heroicon-o-inbox-stack')->iconColor('primary')->numeric()->toggleable(),
                Tables\Columns\TextColumn::make('manufacture_date')->label(__('medicine_fields.manufacture_date'))->icon('heroicon-o-calendar')->iconColor('primary')->date()->toggleable(),
                Tables\Columns\TextColumn::make('expire_date')->label(__('medicine_fields.expire_date'))->icon('heroicon-o-calendar')->iconColor('primary')->date()->toggleable(),
                Tables\Columns\TextColumn::make('manufacturer')->label(__('medicine_fields.manufacturer'))->icon('heroicon-o-building-office')->iconColor('primary')->searchable()->toggleable(),
                Tables\Columns\IconColumn::make('rocheta')
                    ->label(__('medicine_fields.rocheta'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modalFooterActions(fn() => []),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
}
