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
        Schema::create('menu_details', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('idMenu');
            $table->unsignedBigInteger('id_pcs');
            $table->integer('jumlah_pcs');
            $table->timestamps();

            $table->foreign('idMenu')
                ->references('idMenu')
                ->on('menus')
                ->cascadeOnDelete();

            $table->foreign('id_pcs')
                ->references('id_pcs')
                ->on('pcs_tahus')
                ->cascadeOnDelete();

            $table->unique(['idMenu', 'id_pcs']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_details');
    }
};
