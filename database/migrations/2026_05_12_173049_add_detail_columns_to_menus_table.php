<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('foto')->nullable();
            $table->string('tagline_product')->nullable();
            $table->text('deskripsi_produk')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn([
                'foto',
                'tagline_product',
                'deskripsi_produk',
            ]);
        });
    }
};
