<?php

namespace App\Policies;

use App\Models\Identitas;
use App\Models\User;

class IdentitasPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['owner', 'manager']);
    }

    public function view(User $user, Identitas $identitas): bool
    {
        return in_array($user->role, ['owner', 'manager']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['owner', 'manager']);
    }

    public function update(User $user, Identitas $identitas): bool
    {
        return in_array($user->role, ['owner', 'manager']);
    }

    public function delete(User $user, Identitas $identitas): bool
    {
        return in_array($user->role, ['owner', 'manager']);
    }

    public function deleteAny(User $user): bool
    {
        return in_array($user->role, ['owner', 'manager']);
    }
}