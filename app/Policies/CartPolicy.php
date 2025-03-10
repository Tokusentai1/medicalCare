<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class CartPolicy
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
    public function view(Employee $employee, Cart $cart): bool
    {
        if (array_intersect(['admin', 'sales manager'], $employee->role)) {
            return true;
        }
        return false;
    }
}
