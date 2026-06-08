<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Path foto wajah referensi (disimpan saat setup akun)
            $table->string('face_photo')->nullable()->after('password');
            // Face descriptor (128-float array dari face-api.js, disimpan sebagai JSON)
            $table->json('face_descriptor')->nullable()->after('face_photo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_photo', 'face_descriptor']);
        });
    }
};