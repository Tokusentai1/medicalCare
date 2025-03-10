<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Queue\SerializesModels;

class SendOrderNotification implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $employees = Employee::whereJsonContains('role', 'admin')
            ->orWhereJsonContains('role', 'sales manager')
            ->get();

        foreach ($employees as $emp) {
            Notification::make()
                ->title('طلب جديد')
                ->success()
                ->body('تم اضافة طلب جديد الرجاء التحقق منه')
                ->actions([
                    Action::make('معاينة  الطلب')
                        ->url(url('/admin'))
                        ->button(),
                    Action::make('تحديد كمقروء')
                        ->button()
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->markAsRead(),
                ])
                ->sendToDatabase($emp, isEventDispatched: true);
        }
    }
}
