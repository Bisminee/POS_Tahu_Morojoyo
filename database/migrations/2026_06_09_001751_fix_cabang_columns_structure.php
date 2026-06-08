<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah id_cabang di transactions (belum ada sama sekali)
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'id_cabang')) {
                $table->unsignedBigInteger('id_cabang')->nullable()->after('user_id');
                $table->foreign('id_cabang')
                      ->references('idCabang')
                      ->on('cabangs')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['id_cabang']);
            $table->dropColumn('id_cabang');
        });
    }
};