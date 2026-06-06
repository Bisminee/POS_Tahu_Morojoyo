<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

use App\Models\User;

class SetUserRoleFromEmail
{
    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        // Determine role based on email
        $role = match ($user->email) {
            'owner@gmail.com' => 'owner',
            'manager@gmail.com' => 'manager',
            'kasir@gmail.com', 'kasir.cabang1@gmail.com', 'kasir.cabang2@gmail.com' => 'kasir',
            default => $user->role,
        };

        if ($user->role !== $role) {
            $user->role = $role;
            $user->save();
        }
    }
}
