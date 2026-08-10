<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;

class ProductoPolicy
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

    public function view(User $user, Producto $producto): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false; // Solo admin
    }

    public function update(User $user, Producto $producto): bool
    {
        return false; // Solo admin
    }

    public function delete(User $user, Producto $producto): bool
    {
        return false;
    }
}
