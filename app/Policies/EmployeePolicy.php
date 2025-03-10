<?php

namespace App\Policies;

use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        if (array_intersect(['admin', 'hr'], $employee->role)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Employee $model): bool
    {
        if (array_intersect(['admin', 'hr'], $employee->role)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        if (array_intersect(['admin', 'hr'], $employee->role)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, Employee $model): bool
    {
        if (array_intersect(['admin', 'hr'], $employee->role)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, Employee $model): bool
    {
        if (array_intersect(['admin', 'hr'], $employee->role)) {
            return true;
        }
        return false;
    }
}
