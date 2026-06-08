<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Shift;

class ValidateShiftSession
{
    public function handle(Request $request, Closure $next)
    {
        $selectedShiftId = session('selected_shift_id');
        $user = auth()->user();

        if ($selectedShiftId && $user) {
            $shift = Shift::with('karyawan')->find($selectedShiftId);

            if (!$shift) {
                session()->forget('selected_shift_id');
            } elseif ($user->cabang_id && $shift->cabang_id && $shift->cabang_id !== $user->cabang_id) {
                session()->forget('selected_shift_id');
            }
        }

        return $next($request);
    }
}
