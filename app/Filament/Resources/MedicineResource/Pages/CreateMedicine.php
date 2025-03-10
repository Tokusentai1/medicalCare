<?php

namespace App\Filament\Resources\MedicineResource\Pages;

use App\Filament\Resources\MedicineResource;
use Filament\Actions;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Forms\Components\Wizard;

class CreateMedicine extends CreateRecord
{
    use HasWizard;

    protected static string $resource = MedicineResource::class;

    protected function getSteps(): array
    {
        return [
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
                Forms\Components\FileUpload::make('image')->disk('images')->required()->label(__('medicine_fields.image')),
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
        ];
    }
}
