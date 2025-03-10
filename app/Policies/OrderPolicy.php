<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        if (array_intersect(['admin', 'sales manager'], $employee->role)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Order $order): bool
    {
        if (array_intersect(['admin', 'sales manager'], $employee->role)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, Order $order): bool
    {
        if (array_intersect(['admin', 'sales manager'], $employee->role)) {
            return true;
        }
        return false;
    }
}
