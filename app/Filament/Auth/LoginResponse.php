<?php

namespace App\Filament\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        if ($user->role === 'kasir') {
            return redirect('/cashier/pos');
        }

        // Default Filament redirect untuk role lain (admin, dll)
        return redirect('/admin');
    }
}