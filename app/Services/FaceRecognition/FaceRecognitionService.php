<?php

namespace App\Services\FaceRecognition;

use App\Models\User;

class FaceRecognitionService
{
    public function matchFace(array $inputDescriptor)
    {
        $users = User::whereNotNull('face_descriptor')->get();

        foreach ($users as $user) {
            $saved = json_decode($user->face_descriptor, true);

            $distance = $this->euclideanDistance($inputDescriptor, $saved);

            if ($distance < 0.5) {
                return $user;
            }
        }

        return null;
    }

    private function euclideanDistance($a, $b)
    {
        $sum = 0;

        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }

        return sqrt($sum);
    }
}