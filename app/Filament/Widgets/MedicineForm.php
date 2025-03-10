<?php

namespace App\Filament\Widgets;

use App\Models\Medicine;
use Filament\Widgets\ChartWidget;

class MedicineForm extends ChartWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 4;

    protected static ?string $maxHeight = '300px';

    public function getHeading(): ?string
    {
        return __('medicine_fields.medicine_type');
    }

    protected function getData(): array
    {
        $medicineData = Medicine::groupBy('dosage_form')
            ->selectRaw('dosage_form, COUNT(*) as count')
            ->pluck('count', 'dosage_form');

        return [
            'datasets' => [
                [
                    'label' => 'Medicine Types',
                    'data' => $medicineData->values(),
                    'backgroundColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                    ],
                    'hoverOffset' => 15
                ],
            ],
            'labels' => $medicineData->keys()->map(fn($key) => __('medicine_fields.' . $key)),
        ];
    }


    protected function getType(): string
    {
        return 'doughnut';
    }
}
