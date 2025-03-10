<?php

namespace App\Policies;

use App\Models\MedicalHistory;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class MedicalHistoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        if (array_intersect(['admin', 'hr', 'pharmacist'], $employee->role)) {
            return true;
        }
        return false;
    }
}
