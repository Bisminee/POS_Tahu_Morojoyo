<?php

namespace App\Policies;

use App\Models\Karyawan;
use App\Models\User;

class KaryawanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'owner';
    }

    public function view(User $user, Karyawan $karyawan): bool
    {
        return $user->role === 'owner';
    }

    public function create(User $user): bool
    {
        return $user->role === 'owner';
    }

    public function update(User $user, Karyawan $karyawan): bool
    {
        return $user->role === 'owner';
    }

    public function delete(User $user, Karyawan $karyawan): bool
    {
        return $user->role === 'owner';
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === 'owner';
    }
}