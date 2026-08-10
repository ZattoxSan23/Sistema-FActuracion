<?php

namespace App\Policies;

use App\Models\Caja;
use App\Models\User;

class CajaPolicy
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

    public function view(User $user, Caja $caja): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isCajera();
    }

    public function update(User $user, Caja $caja): bool
    {
        return $user->isCajera() && $caja->estaAbierta() && $caja->user_id_apertura === $user->id;
    }

    public function cerrar(User $user, Caja $caja): bool
    {
        return $user->isCajera() && $caja->estaAbierta() && $caja->user_id_apertura === $user->id;
    }
}
