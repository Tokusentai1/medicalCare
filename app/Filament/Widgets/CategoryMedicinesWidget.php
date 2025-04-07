<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Medicine;
use Filament\Widgets\ChartWidget;

class CategoryMedicinesWidget extends ChartWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 5;

    protected static ?string $maxHeight = '300px';

    public function getHeading(): ?string
    {
        return __('medicine_fields.category_medicines');
    }

    protected function getData(): array
    {
        $categoryData = Medicine::groupBy('category_id')
            ->selectRaw('category_id, COUNT(*) as count')
            ->pluck('count', 'category_id');

        $categories = Category::select('id', 'name')->get();
        return [
            'labels' => $categories->pluck('name'),
            'datasets' => [
                [
                    'label' => 'Medicine Categories',
                    'data' => $categoryData->values(),
                    'backgroundColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)',
                        'rgb(75, 192, 192)',
                        'rgb(255, 206, 86)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 99, 132)',
                    ],
                    'hoverOffset' => 15
                ]
            ]
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
