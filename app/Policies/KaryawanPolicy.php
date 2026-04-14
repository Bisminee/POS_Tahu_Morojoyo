<?php

namespace App\Policies;

use App\Models\Karyawan;
use App\Models\User;

class KaryawanPolicy
{
    /**
     * Owner bisa semua, manager tidak bisa akses sama sekali.
     */
    public function viewAny(User $user): bool
    {
        return $user->isOwner();
    }

    public function view(User $user, Karyawan $karyawan): bool
    {
        return $user->isOwner();
    }

    public function create(User $user): bool
    {
        return $user->isOwner();
    }

    public function update(User $user, Karyawan $karyawan): bool
    {
        return $user->isOwner();
    }

    public function delete(User $user, Karyawan $karyawan): bool
    {
        return $user->isOwner();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isOwner();
    }
}
