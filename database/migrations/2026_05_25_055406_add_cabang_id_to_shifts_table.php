<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            // Tanpa constrained() karena PK cabangs bukan 'id'
            $table->unsignedBigInteger('cabang_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('cabang_id');
        });
    }
};
