<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;

class ClientePolicy
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
        return $user->isCajera() || $user->isContador();
    }

    public function view(User $user, Cliente $cliente): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isCajera() || $user->isAdmin();
    }

    public function update(User $user, Cliente $cliente): bool
    {
        return $user->isCajera();
    }

    public function delete(User $user, Cliente $cliente): bool
    {
        return $user->isAdmin();
    }
}
