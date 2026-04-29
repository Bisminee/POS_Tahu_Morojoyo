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
        Schema::create('menu_compositions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('menu_id');
            $table->foreign('menu_id')
                ->references('idMenu')
                ->on('menus')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('pcs_tahu_id');
            $table->foreign('pcs_tahu_id')
                ->references('id_pcs')
                ->on('pcs_tahus')
                ->cascadeOnDelete();

            $table->unsignedInteger('jumlah_pakai')->default(1);

            $table->timestamps();

            $table->unique(['menu_id', 'pcs_tahu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_compositions');
    }
};
