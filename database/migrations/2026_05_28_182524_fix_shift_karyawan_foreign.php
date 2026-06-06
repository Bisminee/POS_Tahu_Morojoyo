<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {

            // drop FK lama
            $table->dropForeign('shifts_user_id_foreign');

        });

        Schema::table('shifts', function (Blueprint $table) {

            // tambah FK baru
            $table->foreign('karyawan_id')
                ->references('idKaryawan')
                ->on('karyawans')
                ->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {

            $table->dropForeign(['karyawan_id']);

            $table->foreign('karyawan_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

        });
    }
};