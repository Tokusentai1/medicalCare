<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\Medicine;
use Illuminate\Auth\Access\Response;

class MedicinePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        if (array_intersect(['admin', 'pharmacist'], $employee->role)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Medicine $medicine): bool
    {
        if (array_intersect(['admin', 'pharmacist'], $employee->role)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        if (array_intersect(['admin', 'pharmacist'], $employee->role)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, Medicine $medicine): bool
    {
        if (array_intersect(['admin', 'pharmacist'], $employee->role)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, Medicine $medicine): bool
    {
        if (array_intersect(['admin', 'pharmacist'], $employee->role)) {
            return true;
        }
        return false;
    }
}
