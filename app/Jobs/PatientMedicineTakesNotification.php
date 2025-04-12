<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\Employee;
use App\Models\User;

class PatientMedicineTakesNotification implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $employees = Employee::whereJsonContains('role', 'admin')
            ->orWhereJsonContains('role', 'pharmacist')
            ->get();

        foreach ($employees as $emp) {
            Notification::make()
                ->title('إضافة دواء جديد للمريض')
                ->success()
                ->body('تمت إضافة دواء جديد للمريض ' . $this->user->fullName . '، الرجاء التحقق منه.')
                ->actions([
                    Action::make('عرض')
                        ->button()
                        ->url(env('APP_URL') . '/admin/medical-histories')
                        ->openUrlInNewTab(false),
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
