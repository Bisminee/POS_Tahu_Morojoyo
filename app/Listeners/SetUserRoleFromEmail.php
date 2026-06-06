<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class SetUserRoleFromEmail
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Determine role based on email
        $role = match ($user->email) {
            'owner@gmail.com' => 'owner',
            'manager@gmail.com' => 'manager',
            'kasir@gmail.com' => 'kasir',
            default => 'manager', // default if not matched
        };

        // Update role if different
        if ($user->role !== $role) {
            $user->update(['role' => $role]);
        }
    }
}
