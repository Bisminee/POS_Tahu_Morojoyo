<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FaceRecognition\FaceRecognitionService;
use App\Models\Attendance;

class FaceAttendanceController extends Controller
{
    public function absen(Request $request, FaceRecognitionService $service)
    {
        $descriptor = $request->descriptor;

        $user = $service->matchFace($descriptor);

        if (!$user) {
            return response()->json([
                'message' => 'Wajah tidak dikenali'
            ], 401);
        }

        Attendance::create([
            'user_id' => $user->id,
            'shift_time' => now(),
        ]);

        return response()->json([
            'message' => 'Absensi berhasil untuk ' . $user->name
        ]);
    }
}
