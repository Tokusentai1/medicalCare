<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Completed Order', Order::where('status', 'completed')->count())
                ->description(__('widget_fields.number of completed order'))
                ->chart([1, 3, 5, 9, 10])
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success')
                ->label(__('widget_fields.completed order')),
            Stat::make('Pending Order', Order::where('status', 'pending')->count())
                ->description(__('widget_fields.number of pending order'))
                ->chart([4, 3, 2, 1])
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('warning')
                ->label(__('widget_fields.pending order')),
            Stat::make('Canceled Order', Order::where('status', 'canceled')->count())
                ->description(__('widget_fields.number of canceled order'))
                ->chart([5, 4, 3, 2, 1])
                ->color('danger')
                ->descriptionIcon('heroicon-o-x-circle')
                ->label(__('widget_fields.canceled order')),
        ];
    }
}
