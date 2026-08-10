<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venta;

class VentaPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Venta $venta): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isCajera();
    }

    public function update(User $user, Venta $venta): bool
    {
        return false;
    }

    public function delete(User $user, Venta $venta): bool
    {
        return $user->isAdmin();
    }

    public function anular(User $user, Venta $venta): bool
    {
        return $user->isAdmin();
    }
}
